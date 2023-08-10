@component('component.card', ['title' => __('General statistics modules')])
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
    @endslot

    <table id="table" class="table table-striped border">
        <thead>
        <tr>
            <th>Модуль</th>
            <th>Ссылка на статистику модуля</th>
            <th>Цвет</th>
            <th>Количество действий</th>
            <th>Количество обновлений страницы</th>
            <th>Время проведённое у модуле</th>
        </tr>
        </thead>
        <tbody>
        @foreach($projects as $project)
            <tr>
                <td>
                    <a href="{{ $project['link'] }}" target="_blank">{{ __($project['title']) }}</a>
                </td>
                <td>
                    <a class="btn btn-default" href="{{ route('main-projects.statistics', $project['id'])}}"
                       target="_blank">Статистика</a>
                </td>
                <td>
                    <div
                        style="height: 25px; width: 25px; border-radius: 40px; background-color: {{ $project['color'] }}"></div>
                </td>
                <td>{{ $project['statistics']['actions_counter'] }}</td>
                <td>{{ $project['statistics']['refresh_page_counter'] }}</td>
                <td>{{ Carbon::now()->addSeconds($project['statistics']['seconds'])->diff(Carbon::now())->format('%H:%I:%S') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="card mt-5">
        <div class="card-header d-flex p-0">
            <h3 class="card-title p-3">Графики</h3>
            <ul class="nav nav-pills ml-auto p-2">
                <li class="nav-item">
                    <a class="nav-link" href="#tab_1" data-toggle="tab">Счётчик действий</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tab_2" data-toggle="tab">Счётчик обновлений страниц</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tab_3" data-toggle="tab">Счётчик времени проведённого в модулях (сек)</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane" id="tab_1">
                    <div class="w-50 ml-auto mr-auto">
                        <canvas id="doughnut-actions-chart"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="tab_2">
                    <div class="w-50 ml-auto mr-auto">
                        <canvas id="doughnut-refreshes-chart"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="tab_3">
                    <div class="w-50 ml-auto mr-auto">
                        <canvas id="doughnut-times-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/chart.js/3.9.1/chart.js') }}"></script>
        <script>
            new Chart(document.getElementById('doughnut-actions-chart'), {
                type: 'doughnut',
                data: {
                    labels: {!! $names !!},
                    datasets: [
                        {
                            backgroundColor: {!! $colors !!},
                            data: {!! $actions !!}
                        }
                    ]
                },
                options: {
                    title: {
                        display: false,
                    },
                    animation: {
                        duration: 0
                    }
                }
            });
            new Chart(document.getElementById('doughnut-refreshes-chart'), {
                type: 'doughnut',
                data: {
                    labels: {!! $names !!},
                    datasets: [
                        {
                            backgroundColor: {!! $colors !!},
                            data: {!! $refreshes !!}
                        }
                    ]
                },
                options: {
                    title: {
                        display: false,
                    },
                    animation: {
                        duration: 0
                    }
                }
            });
            new Chart(document.getElementById('doughnut-times-chart'), {
                type: 'doughnut',
                data: {
                    labels: {!! $names !!},
                    datasets: [
                        {
                            backgroundColor: {!! $colors !!},
                            data: {!! $seconds !!}
                        }
                    ]
                },
                options: {
                    title: {
                        display: false,
                    },
                    animation: {
                        duration: 0
                    }
                }
            });
        </script>
        <script>
            $('#table').DataTable({
                order: [[5, 'desc']],
                lengthMenu: [10, 25, 50, 100],
                pageLength: 25,
                dom: 'lBfrtip',
                language: {
                    lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "{{ __('Search') }}",
                    paginate: {
                        "first": "«",
                        "last": "»",
                        "next": "»",
                        "previous": "«"
                    },
                },
            })
        </script>
    @endslot
@endcomponent
