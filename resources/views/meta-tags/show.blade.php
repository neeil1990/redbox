@component('component.card', ['title' => __('Histories')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">

    @endslot

    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Projects') }}</h3>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-striped projects">
                        <thead>
                        <tr>
                            <th style="width: 1%">#</th>
                            <th style="">{{ __('Date') }}</th>
                            <th style="">{{ __('Time') }}</th>
                            <th style="">{{ __('Show') }}</th>
                            <th style="">{{ __('Link') }}</th>
                            <th style="">{{ __('Errors') }}</th>
                            <th style="">{{ __('Compare') }}</th>
                            <th style="">
                                <span data-toggle="tooltip" data-placement="left" title="{{ __('Select the story you want to follow.') }}">{{ __('Ideal') }} <i class="far fa-question-circle"></i></span>
                            </th>
                            <th style=""></th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach($histories as $history)
                            <tr>
                                <td>{{$history->id}}</td>
                                <td>{{$history->created_at->format('d.m.Y')}}</td>
                                <td>{{$history->created_at->format('H:m')}}</td>
                                <td>
                                    <a class="btn btn-info btn-sm" href="/meta-tags/history/{{ $history->id }}">
                                        <i class="fas fa-play-circle"></i>
                                        {{ __('Start') }}
                                    </a>
                                </td>
                                <td>{{$history->quantity}}</td>
                                <td><span class="badge badge-danger">{{$history->error_quantity}}</span></td>
                                <td>
                                    <select name="compare" class="form-control">
                                        <option value=""></option>
                                        @foreach($histories as $option)
                                            <option value="{{$option->id}}">{{$option->created_at->format('d.m.Y')}} ({{$option->id}})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="customRadioIdeal{{ $history->id }}" name="ideal" value="{{ $history->id }}" @if($history->ideal) checked @endif>
                                        <label for="customRadioIdeal{{ $history->id }}" class="custom-control-label"> #{{ $history->id }}</label>
                                    </div>
                                </td>
                                <td class="project-actions text-center">

                                    <a class="btn btn-info btn-sm" href="{{ route('meta.history.export', $history->id) }}">
                                        <i class="fas fa-file-download"></i>
                                        {{ __('Export') }}
                                    </a>

                                    <a class="btn btn-info btn-sm compare-history" href="{{ route('meta.history.compare', [$history->id, $history->id]) }}">
                                        <i class="far fa-clone"></i>
                                        {{ __('Compare') }}
                                    </a>

                                    <a class="btn btn-info btn-sm delete-history" href="{{ route('meta.history.delete', $history->id) }}">
                                        <i class="fas fa-trash-alt"></i>
                                        {{ __('Delete') }}
                                    </a>

                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
                <!-- /.card-body -->

                <div class="card-footer clearfix">
                    <button type="button" class="btn btn-info" id="lazy-load"><i class="fas fa-plus"></i> {{ __('Load more') }}</button>
                    {{ $histories->links() }}
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

        <script>
            toastr.options = {
                "timeOut": "1000"
            };
        </script>

        <script>
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });

            var tbody = $('.table tbody');

            tbody.on('change', 'input[name="ideal"]', function () {
                let that = $(this);

                $.ajax({
                    method: "PUT",
                    url: "/meta-tags/histories/ideal/{{ $project->id }}",
                    data: { id: that.val() }
                }).done(function ( msg ) {
                    toastr.success('Успешно изменено');
                });
            });

            tbody.on('click', '.delete-history', function (e) {
                e.preventDefault();

                let than = $(this);

                axios.delete(than.attr('href'));
                than.closest('tr').remove();
                toastr.info('Успешно удалено');
            });

            tbody.on('change', 'select[name="compare"]', function () {
                let self = $(this);
                let id_compare = self.val();
                let url = self.closest('tr').find('.compare-history').attr('href').split('/');
                url.splice(-1, 1, id_compare);

                self.closest('tr').find('.compare-history').attr('href', url.join('/'));
            });

            var LazyLoad = function(){

                var self = $(this);
                var pagination = $('.pagination');
                var current = pagination.find('li.active');
                var next = current.next();

                if(next.find('a').length){

                    self.prop( "disabled", true );

                    tbody.css('cursor', 'wait');

                    var href = next.find('a').attr('href');

                    $.get(href, function(response) {

                        var category = $(response);

                        var items = category.find('.table tbody tr');
                        tbody.append(items);

                        var paging = category.find('.pagination');

                        pagination.after(paging);
                        pagination.remove();
                        pagination = paging;

                        self.prop( "disabled", false );

                        tbody.css('cursor', 'auto');
                    });
                }else{
                    toastr.success('Пока что больше данных нет.');
                }
            };

            $('#lazy-load').click(_.debounce(LazyLoad, 500, {
                'leading': true,
                'trailing': false
            }));
        </script>

    @endslot


@endcomponent
