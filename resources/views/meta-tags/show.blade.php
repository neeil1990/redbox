@component('component.card', ['title' => __('Histories')])

    @slot('css')

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
                            <th style="">delete</th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach($project->histories as $history)
                            <tr>
                                <td>{{$history->id}}</td>
                                <td>{{$history->created_at->format('d.m.Y')}}</td>
                                <td>{{$history->created_at->format('H:m')}}</td>
                                <td>
                                    <a class="btn btn-info btn-sm" href="#">
                                        <i class="fas fa-play-circle"></i>
                                        Go
                                    </a>
                                </td>
                                <td>{{$history->quantity}}</td>
                                <td>
                                    <select class="form-control">
                                        <option value=""></option>
                                        @foreach($project->histories as $history)
                                            <option value="">{{$history->created_at->format('d.m.Y')}}</option>
                                        @endforeach
                                    </select>
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

    @endslot


@endcomponent
