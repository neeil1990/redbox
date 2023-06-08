@component('component.card', ['title' => __('General statistics modules')])
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
    @endslot

    <table id="table" class="table table-striped border">
        <thead>
        <tr>
            <th>Модуль</th>
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
                    <a href="{{ $project['link'] }}" target="_blank">{{ $project['title'] }}</a>
                </td>
                <td>
                    <div
                        style="height: 25px; width: 25px; border-radius: 40px; background-color: {{ $project['color'] }}"></div>
                </td>
                <td>{{ $project['statistics']['actions_counter'] }}</td>
                <td>{{ $project['statistics']['refresh_page_counter'] }}</td>
                <td>{{ $project['statistics']['seconds'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="d-flex mt-5">
        <div class="col-4 text-center">
            <h3>Счётчик действий</h3>
            <canvas id="doughnut-actions-chart" style="position: relative; width: 100%"></canvas>
        </div>
        <div class="col-4 text-center">
            <h3>Счётчик обновлений страниц</h3>
            <canvas id="doughnut-refreshes-chart" style="position: relative; width: 100%"></canvas>
        </div>
        <div class="col-4 text-center">
            <h3>Счётчик проведённого времени</h3>
            <canvas id="doughnut-times-chart" style="position: relative; width: 100%"></canvas>
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
                    }
                }
            });
        </script>
        <script>
            $('#table').DataTable({
                orderCellsTop: true,
                fixedHeader: true,
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
