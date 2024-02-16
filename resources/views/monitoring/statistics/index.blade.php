@component('component.card', ['title' => __('Monitoring statistics')])

    @slot('css')
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-select/css/select.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-editor/css/editor.bootstrap4.min.css') }}">

    @endslot

    @slot('tools')
        <div class="btn-group">
            <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-wrench"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" role="menu">
                <h6 class="dropdown-header text-left">Представление кнопки настроек</h6>
                <a href="#" class="dropdown-item">Action</a>
                <h6 class="dropdown-header text-left">Заголовок</h6>
                <a href="#" class="dropdown-item">Another action</a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">Something else here</a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">Separated link</a>
            </div>
        </div>
    @endslot

    <div class="row">
        <div class="col-12">

            <div class="card card-info">

                <div class="card-header">
                    <h3 class="card-title">{{ __('Control panel') }}</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- checkbox -->
                        <form class="px-3 widget-form" style="display:contents">
                            @foreach($menu as $item)
                            <div class="col-3">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="{{ $item['code'] }}" class="custom-control-input widgets-menu" id="customSwitch{{ $item['code'] }}" @if($item['active']) checked="checked" @endif>
                                        <label class="custom-control-label text-nowrap" for="customSwitch{{ $item['code'] }}" style="cursor: pointer">{{ $item['name'] }}</label>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row connectedSortable">
        @foreach($widgets as $widget)
        <div class="col-lg-3 col-6" id="{{ $widget['id'] }}">
            <!-- small box -->
            <div class="small-box {{ $widget['bg'] }}">
                <div class="inner">
                    <h3>{{ $widget['title'] }}</h3>
                    <p>{{ $widget['description'] }}</p>
                </div>
                <div class="icon">
                    <i class="{{ $widget['icon'] }}"></i>
                </div>
                @if($widget['link'])
                <a href="{{ $widget['link'] }}" class="small-box-footer">{{ __('More info') }} <i class="fas fa-arrow-circle-right"></i></a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-12">

            <div class="card card-info">

                <div class="card-header">
                    <h3 class="card-title">Bar Chart</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="chart">
                        <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <table class="table table-hover table-sm" id="project-manager"></table>
            </div>
            <!-- /.card -->
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <table class="table table-hover table-sm" id="project-seo"></table>
            </div>
            <!-- /.card -->
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <div class="card card-info">

                <div class="card-header">
                    <h3 class="card-title">{{ __('Attention projects') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="card card-primary card-outline card-outline-tabs">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" role="tablist">
                                @foreach($period as $key => $date)
                                    <li class="nav-item">
                                        <a class="nav-link @if(!$key) active @endif" data-toggle="pill" href="#custom-tabs-{{ $key }}" role="tab">
                                            {{ $date->monthName }} {{ $date->year }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                @foreach($period as $key => $date)
                                    <div class="tab-pane fade @if(!$key) show active @endif" id="custom-tabs-{{ $key }}" role="tabpanel">
                                        <table data-date="{{ $date->toDateString() }}" class="table table-striped table-bordered attention-table" cellspacing="0" width="100%"></table>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>

            </div>
        </div>
    </div>

    @slot('js')
        <!-- jQuery UI -->
        <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <script>
            $.widget.bridge('uibutton', $.ui.button)
        </script>
        <!-- ChartJS -->
        <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
        <!-- DataTables  & Plugins -->
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-editor/js/datatables_editor.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-select/js/dataTables.select.min.js') }}"></script>

        <script>
            // Make the dashboard widgets sortable Using jquery UI
            $('.connectedSortable').sortable({
                placeholder: 'sort-highlight',
                connectWith: '.connectedSortable',
                handle: '.small-box',
                forcePlaceholderSize: true,
                zIndex: 999999,
                stop: function( event, ui ) {
                    let items = $(this).sortable("toArray");

                    axios.post('/monitoring/statistics/sort-widgets', {
                        ids: items,
                    });
                }
            });

            $('.connectedSortable .small-box').css('cursor', 'move');

            $('.widgets-menu').change(function () {
                let menu = $('.widgets-menu');
                const formData = new FormData(document.querySelector('.widget-form'));

                let fields = [];
                $.each(menu, function (i, el) {
                    let item = $(el);
                    let field = formData.get(item.attr('name'));

                    if(field)
                        fields.push({ name: item.attr('name'), active: true});
                    else
                        fields.push({ name: item.attr('name'), active: false});
                });

                axios.post('/monitoring/statistics/active-widgets', {
                    fields: fields
                }).then(function(){
                    window.location.reload();
                });
            });

            // BAR CHART
            let dates = JSON.parse('{!! $chartData["dates"] !!}');
            let budget = JSON.parse('{!! $chartData["budget"] !!}');
            let mastered = JSON.parse('{!! $chartData["mastered"] !!}');

            let areaChartData = {
                labels  : dates,
                datasets: [
                    {
                        label               : '{{ __('Budget') }}',
                        backgroundColor     : '#00c0ef',
                        data                :  budget,
                    },
                    {
                        label               : '{{ __('Mastered') }}',
                        backgroundColor     : '#00a65a',
                        data                :  mastered,
                    },
                ]
            };

            let barChartCanvas = $('#barChart').get(0).getContext('2d');

            new Chart(barChartCanvas, {
                type: 'bar',
                data: areaChartData,
                options: {
                    responsive              : true,
                    maintainAspectRatio     : false,
                    datasetFill             : false
                },
            });

            let managerTable = $('#project-manager').DataTable({
                dom: '<"card-header"<"card-title">><"card-body p-0"rt><"card-footer clearfix"p><"clear">',
                paging: false,
                serverSide: true,
                ajax: {
                    url: "/monitoring/statistics/manager-table",
                    type: 'GET',
                },
                columns: [
                    { title: 'ФИО', data: 'name' },
                    { title: 'Кол-во проектов', data: 'count' },
                    { title: 'TOP 10', data: 'top10' },
                    { title: 'TOP 30', data: 'top30' },
                    { title: 'TOP 100', data: 'top100' },
                    { title: 'Бюджет', data: 'budget' },
                ],
                initComplete: function(settings, json) {
                    let card = $(this).closest('.card');
                    card.find('.card-title').text('{{ __('Manager projects') }}');
                },
            });

            let seoTable = $('#project-seo').DataTable({
                dom: '<"card-header"<"card-title">><"card-body p-0"rt><"card-footer clearfix"p><"clear">',
                paging: false,
                serverSide: true,
                ajax: {
                    url: "/monitoring/statistics/seo-table",
                    type: 'GET',
                },
                order: [
                    [1, 'asc'],
                ],
                columnDefs: [
                    { orderable: false, targets: [0] },
                ],
                columns: [
                    {
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: '<a href="#" class="dt-control text-muted"><i class="fas fa-plus-circle"></i></a>'
                    },
                    { data: 'id', visible: false },
                    { title: 'ФИО', data: 'name' },
                    { title: 'Кол-во проектов', data: 'count' },
                    { title: 'TOP 10', data: 'top10' },
                    { title: 'TOP 30', data: 'top30' },
                    { title: 'TOP 100', data: 'top100' },
                    { title: 'Бюджет', data: 'budget' },
                    { title: 'Освоено', data: 'mastered' },
                ],
                initComplete: function(settings, json) {
                    let card = $(this).closest('.card');
                    card.find('.card-title').text('{{ __('Seo projects') }}');
                },
            });

            seoTable.on('click', 'td.dt-control', function (e) {
                let tr = e.target.closest('tr');
                let row = seoTable.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                } else {
                    let data = row.data();
                    axios.get(`/monitoring/statistics/project-table/${data.id}`)
                        .then(function(response){
                            row.child(response.data).show();
                        });
                }
                return false;
            });

            let attentionTable = $('.attention-table').DataTable({
                ajax: function(data, callback, settings) {
                    let date = this.data('date');

                    axios.get('/monitoring/statistics/attention-table', {
                        params: {
                            date: date
                        }
                    })
                    .then(function (response) {
                        // handle success
                        callback(response.data);
                    });
                },
                info: false,
                paging: false,
                searching: false,
                scrollCollapse: true,
                scrollY: 200,
                columns: [
                    { title: '{{ __('Project') }}', data: 'name' },
                    { title: '{{ __('Users') }}', data: 'users' },
                    { title: '{{ __('TOP 10') }}', data: 'top10' },
                    { title: '{{ __('Mastered') }}', data: 'mastered' },
                    { title: '{{ __('Words') }}', data: 'words' },
                ],
            });

            $('a[data-toggle="pill"]').on('shown.bs.tab', function (event) {
                attentionTable.tables().columns.adjust();
            });

        </script>

    @endslot

@endcomponent
