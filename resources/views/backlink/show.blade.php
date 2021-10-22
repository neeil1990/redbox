@component('component.card', ['title' => __('My project')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" href="{{ asset('plugins/backlink/css/backlink.css') }}">
    @endslot
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div id="toast-container" class="toast-top-right success-message">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message">{{ __('Successfully changed') }}</div>
        </div>
    </div>
    <div id="toast-container" class="toast-top-right error-message">
        <div class="toast toast-error" aria-live="assertive">
            <div class="toast-message error-msg">{{ __('The field must contain more than 0 characters') }}</div>
        </div>
    </div>
    <div class='mt-3'>
        <div class='form-group required d-flex align-items-center' projectId="{{ $project->id }}">
            {!! Form::text('project_name', $project->project_name ,['class' => 'form-control col-3 project-name']) !!}
        </div>
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline" role="grid"
               aria-describedby="example1_info">
            <thead>
            <tr>
                <th class="fixed-th-height"
                    style="vertical-align: middle; text-align: center;">{{ __('Link to the page of the donor website') }}</th>
                <th class="fixed-th-height"
                    style="vertical-align: middle; text-align: center;">{{ __('The link that the script will search for') }}</th>
                <th class="fixed-th-height" style="vertical-align: middle; text-align: center;">{{ __('Anchor') }}</th>
                <th class="fixed-th-height"
                    style="vertical-align: middle; text-align: center;">{{ __('Check nofollow') }}</th>
                <th class="fixed-th-height"
                    style="vertical-align: middle; text-align: center;">{{ __('Check noindex') }}</th>
                <th class="fixed-th-height"
                    style="vertical-align: middle; text-align: center;">{{ __('Last check') }}</th>
                <th class="fixed-th-height" style="vertical-align: middle; text-align: center;">{{ __('Status') }}</th>
                <th class="fixed-th-height" style="vertical-align: middle; text-align: center;"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($project->link as $link)
                <tr id="{{ $link->id }}">
                    <td class="table-d">
                        {!! Form::textarea('site_donor', $link->site_donor ,['class' => 'form-control backlink', 'rows' => 6]) !!}
                    </td>
                    <td class="table-d">
                        {!! Form::textarea('link', $link->link ,['class' => 'form-control backlink','rows' => 6]) !!}
                    </td>
                    <td class="table-d">
                        {!! Form::textarea('anchor', $link->anchor ,['class' => 'form-control backlink','rows' => 6]) !!}
                    </td>
                    <td class="">
                        {!! Form::select('nofollow', ['1' => __('Yes'), '0' => __('No')], $link->nofollow, ['class' => 'form-control backlink']) !!}
                    </td>
                    <td class="">
                        {!! Form::select('noindex', ['1' => __('Yes'), '0' => __('No')], $link->noindex, ['class' => 'form-control backlink']) !!}

                    </td>
                    <td class="">@isset($link->last_check){{ $link->last_check }}@endisset</td>
                    <td class="fixed-height">
                        @if((boolean)$link->broken)
                            <span class="text-danger">{{ $link->status }}</span>
                        @else
                            <span class="text-info">{{ $link->status }}</span>
                        @endif
                    </td>
                    <td class="d-flex justify-content-around m-auto">
                        <form action="{{ route('check.link', $link->id)}}" method="get">
                            @csrf
                            <button class="btn btn-default" type="submit">
                                <i aria-hidden="true" class="fa fa-search"></i>
                            </button>
                        </form>
                        <form action="{{ route('delete.link', $link->id)}}" method="post">
                            @csrf @method('DELETE')
                            <button class="btn btn-default" type="submit">
                                <i aria-hidden="true" class="fa fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class='pt-3'>
            <a href='{{ route('add.link.view', $project->id) }}' class='btn btn-secondary'> {{ __('Add link') }}</a>
            <a href='{{ route('backlink') }}' class='btn btn-default'> {{ __('To my projects') }}</a>
        </div>
    </div>
    @slot('js')
        <script>
            var oldValue = ''
            var oldProjectName = ''

            $(document).ready(function () {
                $(".form-control.col-3.project-name").focus(function () {
                    oldProjectName = $(this).val()
                })
                $(".form-control.col-3.project-name").blur(function () {
                    if (oldProjectName !== $(this).val()) {
                        console.log($(this).parent().attr("projectId"))
                        console.log($(this).attr('name'))
                        console.log($(this).val())
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "{{ route('edit.backlink') }}",
                            data: {
                                id: $(this).parent().attr("projectId"),
                                name: $(this).attr('name'),
                                option: $(this).val(),
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function () {
                                $('.toast-top-right.success-message').show(300)
                                setTimeout(() => {
                                    $('.toast-top-right.success-message').hide(300)
                                }, 4000)
                            },
                            error: function () {
                                $('.toast-top-right.error-message').show()
                                setTimeout(() => {
                                    $('.toast-top-right.error-message').hide(300)
                                }, 4000)
                            }
                        });
                    }
                })
                $(".backlink").focus(function () {
                    oldValue = $(this).val()
                })
                $(".backlink").blur(function () {
                    if (oldValue != $(this).val()) {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "{{ route('edit.link') }}",
                            data: {
                                id: $(this).parent().parent().attr("id"),
                                name: $(this).attr('name'),
                                option: $(this).val(),
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function () {
                                $('.toast-top-right.success-message').show(300)
                                setTimeout(() => {
                                    $('.toast-top-right.success-message').hide(300)
                                }, 4000)
                            },
                            error: function () {
                                $('.toast-top-right.error-message').show()
                                setTimeout(() => {
                                    $('.toast-top-right.error-message').hide(300)
                                }, 4000)
                            }
                        });
                    }
                });
            });
        </script>
    @endslot
@endcomponent
