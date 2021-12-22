@component('component.card', ['title' => __('News and updates')])
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <a href="{{ route('create.news') }}" class="btn btn-secondary mb-3" style="margin-left: 7.5px">
        {{ __('Add News') }}
    </a>
    @isset($news[0])
        <div class="col-lg-8 col-md-12 pb-3">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content">
                        @foreach($news as $item)
                            <div class="post" id="news-{{ $item->id }}">
                                <div class="user-block">
                                    <img class="img-circle img-bordered-sm"
                                         src="https://lk.redbox.su/storage/{{ $item->user->image }}" alt="avatar">
                                    <span class="username">
                                        <span>{{ $item->user->name }}</span>
                                            @if($item->user_id === \Illuminate\Support\Facades\Auth::id() || $admin)
                                            <a class="float-right btn-tool"
                                               data-toggle="modal"
                                               data-target="#remove-news-{{ $item->id }}">
                                                <i class="fas fa-times"></i>
                                            </a>
                                            <a class="float-right btn-tool mr-4"
                                               href="{{ route('edit.news', $item->id) }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endif
                                    </span>
                                    <span class="description">{{ $item->created_at->diffForHumans() }}</span>
                                </div>
                                <div>{!! $item->content !!}</div>
                                <div>
                                <span class="link-black text-sm like-news @isset($item->like) text-danger @endisset"
                                      data-target="{{ $item->id }}">
                                    <i class="far fa-thumbs-up mr-1"></i>
                                    <span class="number-of-likes">{{ $item->number_of_likes }}</span>
                                </span>
                                    <span class="float-right">
                                    <span class="link-black text-sm comments" style="cursor:pointer">
                                        <i class="far fa-comments mr-1"></i>
                                        <span>{{ __('Comments') }}</span>
                                        (<span>{{ count($item->comments) }}</span>)
                                    </span>
                                </span>
                                </div>
                                <div id="comments-{{$item->id}}" class="mt-3 comments-block" style="display: none;">
                                    <div class="input-group input-group-sm mb-0">
                                        <input type="hidden" name="news_id" value="{{ $item->id }}">
                                        <textarea name="comment" class="form-control" rows="3" required></textarea>
                                        <div class="input-group-append">
                                            <button type="submit"
                                                    class="btn btn-secondary send-comment">{{ __('Send') }}</button>
                                        </div>
                                    </div>
                                    <div class="mt-3 ml-2 news-comments">
                                        @foreach($item->comments as $comment)
                                            <div class="direct-chat-msg" id="comment-{{$comment->id}}">
                                                <div class="direct-chat-infos clearfix">
                                                    <div class="direct-chat-name float-left">
                                                        {{ $comment->user->name }}
                                                        (<span
                                                            class="text-info">@if($admin){{ __('Admin') }}@else{{ __('User') }}@endif</span>)
                                                        <span class="text-muted font-weight-normal ml-2">
                                                            {{ $comment->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                    <span class="float-right">
                                                        @if($comment->user_id === \Illuminate\Support\Facades\Auth::id() || $admin)
                                                            <span><i class="fa fa-edit btn-tool"></i></span>
                                                            <span data-toggle="modal"
                                                                  class="remove-comment btn-tool"
                                                                  data-target="{{ $comment->id }}">
                                                                <i class="fas fa-times"></i>
                                                            </span>
                                                        @endif
                                                </span>
                                                </div>
                                                <img class="direct-chat-img"
                                                     src="https://lk.redbox.su/storage/{{ $comment->user->image}}"
                                                     alt="avatar">
                                                <div class="direct-chat-text">
                                                    <span>{{ $comment->comment }}</span>
                                                    <textarea rows="5" class="form form-control"
                                                              data-target="{{ $comment->id }}"
                                                              style="display: none">{{ $comment->comment }}</textarea>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="modal fade" id="remove-news-{{ $item->id }}" tabindex="-1" role="dialog"
                                     aria-hidden="true">
                                    <div class="modal-dialog w-25" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <p>{{__('Удалить новость')}}</p>
                                                <p>{{__('Are you sure?')}}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button"
                                                        class="btn btn-secondary remove-news"
                                                        data-dismiss="modal"
                                                        data-target="{{ $item->id }}">
                                                    Удалить
                                                </button>
                                                <button type="button"
                                                        class="btn btn-default btn-flat"
                                                        data-dismiss="modal">
                                                    {{__('Back')}}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endisset
    @slot('js')
        <script>
            $(document).ready(function () {
                $('html').height($('.col-lg-8.col-md-12.pb-3').height() + 150)
            });
            $('.btn.btn-secondary.remove-news').click(function () {
                var id = $(this).attr('data-target')
                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: "{{ route('remove.news') }}",
                    data: {
                        id: id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        $('.modal-backdrop.fade.show').hide()
                        $('#news-' + id).remove()
                    }
                });
            })

            $('.remove-comment.btn-tool').click(function () {
                var item = $(this)
                var id = item.attr('data-target')
                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: "{{ route('remove.comment') }}",
                    data: {
                        id: id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        let span = item.parent().parent().parent().parent().parent()
                            .parent().children('div').eq(2).children('span').eq(1)
                            .children().children('span').eq(1)
                        let number = Number(span.text())
                        span.text(number - 1)
                        $('#comment-' + id).hide(300)
                        $('.modal-backdrop.fade.show').hide()
                    }
                });
            })

            $('.link-black.text-sm.like-news').click(function () {
                var id = $(this).attr('data-target')
                var item = $(this)
                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: "{{ route('like') }}",
                    data: {
                        id: id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        let number = Number(item.children('span').text())
                        if (response[0] === 'like') {
                            item.children('span').text(number + 1)
                            item.addClass(' text-danger')
                        } else {
                            item.children('span').text(number - 1)
                            item.removeClass('text-danger')
                        }
                    }
                });

            })

            $('.btn.btn-secondary.send-comment').click(function () {
                var elem = $(this)
                var textarea = $(this).parent().parent().children('textarea')
                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: "{{ route('create.comment') }}",
                    data: {
                        news_id: $(this).parent().parent().children('input').val(),
                        comment: textarea.val(),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        textarea.val("")
                        let span = elem.parent().parent().parent().parent().children('div').eq(2).children('span').eq(1).children('span').children('span').eq(1)
                        let number = Number(span.text())
                        span.text(number + 1)
                        let comments = elem.parent().parent().parent().children('div').eq(1)

                        comments.append(
                            "<div id='comment-" + response.commentId + "' class='direct-chat-msg'> " +
                            "<div class='direct-chat-infos clearfix'>" +
                            "<div class='direct-chat-name float-left'>" + response.userName + "(<span class='text-info'>" + role + "</span>)" +
                            "<span class='text-muted font-weight-normal ml-2'>{{__('Just now')}}</span></div>" +
                            "<span class='float-right'> " +
                            "<span><i class='fa fa-edit btn-tool' onclick='editComment(this)'></i> " +
                            "</span> " +
                            "<span data-toggle='modal' data-target='" + response.commentId + "' class='remove-comment btn-tool' onclick='removeComment(this)'> " +
                            "<i class='fas fa-times'></i> " +
                            "</span></span></div> " +
                            "<img src='https://lk.redbox.su/storage/" + response.avatar + "' alt='avatar' class='direct-chat-img'>" +
                            "<div class='direct-chat-text'> " +
                            "<span>" + response.comment + "</span> " +
                            "<textarea rows='5' data-target='" + response.commentId + "' class='form form-control' style='display: none;'>" + response.comment + "</textarea>" +
                            "</div>" +
                            "</div>"
                        );
                    }
                });
            })

            $('.comments').click(function () {
                let comments = $(this).parent().parent().parent().children('div').eq(3);
                if (comments.is(':visible')) {
                    comments.slideUp(300);
                } else {
                    comments.slideDown(300);
                }
            });

            function removeComment(elem) {
                let item = $(elem)
                var id = item.attr('data-target')
                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: "{{ route('remove.comment') }}",
                    data: {
                        id: id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        let span = item.parent().parent().parent().parent().parent().parent().children('div').eq(2).children('span').eq(1)
                            .children().children('span').eq(1)
                        let number = Number(span.text())
                        span.text(number - 1)
                        $('#comment-' + id).hide(300)
                    }
                });
            }

            function editComment(elem) {
                let item = $(elem)
                let span = item.parent().parent().parent().parent().children('div').eq(1).children('span').eq(0)
                let textarea = item.parent().parent().parent().parent().children('div').eq(1).children('textarea').eq(0)
                span.hide()
                textarea.show()

                textarea.blur(function () {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: "{{ route('edit.comment') }}",
                        data: {
                            id: textarea.attr('data-target'),
                            comment: textarea.val(),
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            span.text(textarea.val())
                            span.show()
                            textarea.hide()
                        }
                    });
                })
            }

            $('.fa.fa-edit.btn-tool').click(function () {
                let span = $(this).parent().parent().parent().parent().children('div').eq(1).children('span').eq(0)
                let textarea = $(this).parent().parent().parent().parent().children('div').eq(1).children('textarea').eq(0)
                span.hide()
                textarea.show()

                textarea.blur(function () {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: "{{ route('edit.comment') }}",
                        data: {
                            id: textarea.attr('data-target'),
                            comment: textarea.val(),
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            span.text(textarea.val())
                            span.show()
                            textarea.hide()
                        }
                    });
                })
            })
        </script>
    @endslot
@endsection
@endcomponent
