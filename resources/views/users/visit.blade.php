@component('component.card', ['title' =>  __('Статистика посещений') ])
    @slot('css')
    @endslot
    <div class="d-flex">
        <div class="d-flex flex-row w-100">
            <div class="card w-75">
                <div class="card-header">
                    <h3>Статистика за {{ $now }}</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover dataTable no-footer">
                        <thead>
                        <tr>
                            <th>Модуль</th>
                            <th>Количество посещений</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($toDay as $module)
                            <tr>
                                <td>{{ __($module->project->title) }}</td>
                                <td>{{ $module->counter }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="w-25">
                <canvas id="doughnut-chart" style="position: relative; height: 15vh; width: 100%"></canvas>
            </div>
        </div>
    </div>
    @slot('js')
        <!-- Charts -->
        <script src="{{ asset('plugins/chart.js/3.9.1/chart.js') }}"></script>
        <script>
            let labels = [];
            let colors = []
            let colorsArray = getColorArray()

            for (let i = 0; i < {{ count($toDay) }}; i++) {
                colors.push(colorsArray.shift())
            }

            new Chart(document.getElementById("doughnut-chart"), {
                type: 'doughnut',
                data: {
                    labels: {!! $labels !!},
                    datasets: [
                        {
                            label: "Population (millions)",
                            backgroundColor: colors,
                            data: {!! $counters !!}
                        }
                    ]
                },
                options: {
                    title: {
                        display: false,
                    }
                }
            });

            function getColorArray() {
                let colorArray = [
                    "rgba(220, 51, 10, 0.6)",
                    "rgb(203,60,25)",
                    "rgba(121, 25, 6, 1)",
                    "rgba(214, 96, 110, 0.6)",
                    "rgba(214, 96, 110, 1)",
                    "rgba(252, 170, 153, 0.6)",
                    "rgba(252, 170, 153, 1)",
                    "rgba(214, 2, 86, 0.6)",
                    "rgba(214, 2, 86, 1)",
                    "rgba(147,50,88, 1)",
                    "rgba(247, 220, 163, 1)",
                    "rgba(204, 118, 32, 0.6)",
                    "rgba(204, 118, 32, 1)",
                    "rgba(255,89,0,0.6)",
                    "rgba(255, 89, 0, 1)",
                    "rgba(164, 58 ,1, 1)",
                    "rgba(73, 28, 1, 0.6)",
                    "rgba(178, 135, 33, 0.6)",
                    "rgba(178, 135, 33, 1)",
                    "rgba(246, 223, 78, 1)",
                    "rgba(1, 253, 215, 0.6)",
                    "rgba(1, 253, 215, 1)",
                    "rgba(1, 148, 130, 0.6)",
                    "rgba(1, 79, 66, 0.6)",
                    "rgba(139, 150, 24, 0.6)",
                    "rgba(154, 205, 50, 0.6)",
                    "rgba(154, 205, 50, 1)",
                    "rgb(17, 255, 0)",
                    "rgba(151, 186, 229, 1)",
                    "rgba(0, 69, 255, 0.6)",
                    "rgba(0, 69, 255, 1)",
                    "rgba(1, 45, 152, 0.6)",
                    "rgba(157, 149, 226, 1)",
                    "rgba(6, 136, 165, 0.6)",
                    "rgba(64, 97, 206, 1)",
                    "rgba(19,212,224, 0.6)",
                    "rgba(19,212,224, 1)",
                    "rgba(2, 97, 214, 0.6)",
                    "rgba(159, 112, 216, 0.6)",
                    "rgba(239, 50, 223, 0.6)",
                    "rgba(239, 50, 223, 1)",
                    "rgba(209, 46, 127, 0.6)",
                    "rgba(209, 46, 127, 1)",
                    "rgba(194, 85, 237, 1)",
                    "rgba(252, 194, 243, 1)",
                    "rgba(244, 139, 200, 0.6)",
                    "rgba(244, 139, 200, 1)",
                    "rgba(87, 64, 64, 0.6)",
                    "rgba(239, 211, 211, 0.6)",
                    "rgba(163, 209, 234, 0.6)",
                    "rgba(234,163,163,0.6)",
                    "rgba(232,194,90,0.6)",
                ]

                return colorArray.sort(() => Math.random() - 0.5);
            }
        </script>
    @endslot
@endcomponent
