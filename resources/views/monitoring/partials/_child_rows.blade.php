<div class="row">
    @foreach($groups as $engine)
    <div class="col-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">{{ ucfirst($engine->engine) }}, {{ $engine->location->name }} [{{ $engine->lr }}]</h3>

                <div class="card-tools">
                    <span class="badge bg-success">{{ __('Region code') }}: {{ $engine->lr }}</span>
                    <span class="badge bg-success">{{ __('Region ID') }}: {{ $engine->id }}</span>
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
                            <th class="tooltip-child-table" title="В этом столбце средняя позиция по поисковой системе определенного региона/города. Мы ее считаем классическим способом: сумма всех позиций, разделенная на количество слов. Благодаря группировке по регионам и дням, Вы сможете увидеть ее динамику. Чем ближе среднее значение к 1, тем лучше.">Средняя позиция <i class="far fa-question-circle"></i></th>
                            <th class="tooltip-child-table" title="В столбцах ТОП Вы увидите результат в процентах, какое количество слов попадает в ТОП 3/5/10/30/100. Чем выше процент фраз, тем лучше. Благодаря группировке по регионам и дням, Вы сможете увидеть ее динамику в сравнении с 30/90/180/365 днями ранее, если результат за этот период есть в системе.">ТОП-1 <i class="far fa-question-circle"></i></th>
                            <th>ТОП-3</th>
                            <th>ТОП-5</th>
                            <th>ТОП-10</th>
                            <th>ТОП-20</th>
                            <th>ТОП-50</th>
                            <th>ТОП-100</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($engine->data as $data)
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
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">{{ __('No data') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    @endforeach
</div>
