<div class="row">
    @foreach($groups as $engine)
    <div class="col-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">{{ ucfirst($engine->engine) }}, {{ $engine->location->name }} [{{ $engine->lr }}]</h3>

                <div class="card-tools">
                    <span title="3 New Messages" class="badge bg-success">{{ __('Region code') }}: {{ $engine->lr }}</span>
                    <span title="3 New Messages" class="badge bg-success">{{ __('Region ID') }}: {{ $engine->id }}</span>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body" style="display: block;">
                <table class="table table-bordered table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Дата обновления</th>
                            <th>Средняя позиция</th>
                            <th>ТОП-1</th>
                            <th>ТОП-3</th>
                            <th>ТОП-5</th>
                            <th>ТОП-10</th>
                            <th>ТОП-20</th>
                            <th>ТОП-50</th>
                            <th>ТОП-100</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($engine->data as $data)
                        <tr>
                            <td>{{ $data->latest_created->format('d.m.Y') }}</td>
                            <td>{{ $data->middle_position }}</td>
                            <td class="top">{{$data->top_1}}</td>
                            <td class="top">{{$data->top_3}}</td>
                            <td class="top">{{$data->top_5}}</td>
                            <td class="top">{{$data->top_10}}</td>
                            <td class="top">{{$data->top_20}}</td>
                            <td class="top">{{$data->top_50}}</td>
                            <td class="top">{{$data->top_100}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    @endforeach
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>REGION</th>
                    <th>Источник</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $engine)
                <tr data-widget="expandable-table" aria-expanded="true">
                    <td>{{ $engine->id }}</td>
                    <td>{{ $engine->lr }}</td>
                    <td>{{ ucfirst($engine->engine) }}, {{ $engine->location->name }}</td>
                </tr>
                <tr class="expandable-body">
                    <td colspan="4">
                        <table class="table table-bordered table-hover table-sm" style="margin: 0;width: 100%;">
                            <thead>
                                <tr>
                                    <th>Дата обновления</th>
                                    <th>Средняя позиция</th>
                                    <th>ТОП-1</th>
                                    <th>ТОП-3</th>
                                    <th>ТОП-5</th>
                                    <th>ТОП-10</th>
                                    <th>ТОП-20</th>
                                    <th>ТОП-50</th>
                                    <th>ТОП-100</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($engine['data'] as $data)
                                <tr>
                                    <td>{{ $data->latest_created->format('d.m.Y') }}</td>
                                    <td>{{ $data->middle_position }}</td>
                                    <td class="top">{{$data->top_1}}</td>
                                    <td class="top">{{$data->top_3}}</td>
                                    <td class="top">{{$data->top_5}}</td>
                                    <td class="top">{{$data->top_10}}</td>
                                    <td class="top">{{$data->top_20}}</td>
                                    <td class="top">{{$data->top_50}}</td>
                                    <td class="top">{{$data->top_100}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
