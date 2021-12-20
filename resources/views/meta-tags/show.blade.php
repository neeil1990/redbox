@component('component.card', ['title' => __('Histories')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">

    @endslot

    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Проекты</h3>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-striped projects">
                        <thead>
                        <tr>
                            <th style="width: 1%">#</th>
                            <th style="">date</th>
                            <th style="">time</th>
                            <th style="">show</th>
                            <th style="">link</th>
                            <th style="">error</th>
                            <th style="">compare</th>
                            <th style="">ideal <i class="far fa-question-circle" data-toggle="tooltip" data-placement="right" title="Выберете историю за которой хотите следить."></i></th>
                            <th style=""></th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach($project->histories->sortByDesc('id') as $history)
                            <tr>
                                <td>{{$history->id}}</td>
                                <td>{{$history->created_at->format('d.m.Y')}}</td>
                                <td>{{$history->created_at->format('H:m')}}</td>
                                <td>
                                    <a class="btn btn-info btn-sm" href="/meta-tags/history/{{ $history->id }}">
                                        <i class="fas fa-play-circle"></i>
                                        Go
                                    </a>
                                </td>
                                <td>{{$history->quantity}}</td>
                                <td><span class="badge badge-danger">{{$history->error_quantity}}</span></td>
                                <td>
                                    <select name="compare" class="form-control">
                                        <option value=""></option>
                                        @foreach($project->histories->sortByDesc('id') as $option)
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
                                <td class="project-actions">

                                    <a class="btn btn-info btn-sm" href="{{ route('meta.history.export', $history->id) }}">
                                        <i class="fas fa-file-download"></i>
                                        Export
                                    </a>

                                    <a class="btn btn-info btn-sm compare-history" href="{{ route('meta.history.compare', [$history->id, $history->id]) }}">
                                        <i class="far fa-clone"></i>
                                        Compare
                                    </a>

                                    <a class="btn btn-info btn-sm delete-history" href="{{ route('meta.history.delete', $history->id) }}">
                                        <i class="fas fa-trash-alt"></i>
                                        Delete
                                    </a>

                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
                <!-- /.card-body -->
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
            })

            $('input[name="ideal"]').change(function () {
                let that = $(this);

                $.ajax({
                    method: "PUT",
                    url: "/meta-tags/histories/ideal/{{ $project->id }}",
                    data: { id: that.val() }
                }).done(function ( msg ) {
                    toastr.success('Успешно изменено');
                });
            });

            $('.delete-history').click(function(e){
                e.preventDefault();

                let than = $(this);

                axios.delete(than.attr('href'));
                than.closest('tr').remove();
                toastr.info('Успешно удалено');
            });

            $('select[name="compare"]').change(function () {
                let self = $(this);
                let id_compare = self.val();
                let url = self.closest('tr').find('.compare-history').attr('href').split('/');
                url.splice(-1, 1, id_compare);

                self.closest('tr').find('.compare-history').attr('href', url.join('/'));
            });

        </script>

    @endslot


@endcomponent
