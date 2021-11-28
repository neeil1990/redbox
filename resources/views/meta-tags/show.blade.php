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
                            <th style="">compare</th>
                            <th style="">ideal</th>
                            <th style=""></th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach($project->histories as $history)
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
                                <td>
                                    <select class="form-control">
                                        <option value=""></option>
                                        @foreach($project->histories as $option)
                                            <option value="">{{$option->created_at->format('d.m.Y')}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="customRadioIdeal{{ $history->id }}" name="ideal" value="{{ $history->id }}" @if($history->ideal) checked @endif>
                                        <label for="customRadioIdeal{{ $history->id }}" class="custom-control-label">Custom Radio {{ $history->id }}</label>
                                    </div>
                                </td>
                                <td class="project-actions">

                                    <a class="btn btn-info btn-sm" href="#">
                                        <i class="fas fa-play-circle"></i>
                                        Update
                                    </a>

                                    <a class="btn btn-info btn-sm" href="#">
                                        <i class="fas fa-play-circle"></i>
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

        </script>

    @endslot


@endcomponent
