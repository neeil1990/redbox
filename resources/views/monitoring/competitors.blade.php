@component('component.card', ['title' => __('Project') . " $monitoring->name" ])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

        <style>
            .dTable {
                display: none;
            }

            .dataTables_processing {
                margin: 10px auto;
                z-index: 4;
            }

            .exist-position {
                color: #28a745 !important;
                font-weight: bold;
            }

            .popover {
                max-width: none;
            }

            .progress-spinner {
                position: absolute;
                top: 10%;
                width: 100%;
                text-align: center;
                z-index: 1;
            }

            .reset-zoom {
                position: absolute;
                top: 50px;
                right: 30px;
            }

            .dataTables_scrollHead {
                position: sticky !important;
                top: 0px;
                z-index: 1;
                background-color: white;
            }
        </style>
    @endslot

    <div class="row">
        @foreach($navigations as $navigation)
            <div class="col-lg-2 col-6">
                <a href="{{ $navigation['href'] }}" class="small-box {{ $navigation['bg'] }}" style="min-height: 137px">
                    <div class="inner">
                        <h3>{{ $navigation['h3'] }}</h3>
                        <p>{{ $navigation['p'] }}</p>
                        @isset($navigation['small'])
                            <small>{{ $navigation['small'] }}</small>
                        @endisset
                    </div>
                    <div class="icon">
                        <i class="{{ $navigation['icon'] }}"></i>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    @include('monitoring.keywords.modal.main')

    <h3>
        {{  __('Project') . " $monitoring->name" }}
    </h3>

    <h4>Количество фраз: {{ $countQuery }}</h4>

    <table class="table table-hover table-bordered no-footer mt-3">
        <thead>
        <tr>
            <th>Конкурент?</th>
            <th>Домен ({{ count($competitors) }})</th>
            <th>Поисковые системы</th>
            <th>Видимость</th>
        </tr>
        </thead>
        <tbody>
        @foreach($competitors as $competitor => $count)
            <tr>
                <td>
                    <div>
                        <input type="checkbox">
                    </div>
                </td>
                <td @if(parse_url($competitor)['host'] === $monitoring->url) class="bg-info" @endif>
                    <a href="{{ $competitor }}" target="_blank">{{ parse_url($competitor)['host'] }}</a>
                </td>
                <td>
                    @foreach($searchEngines as $searchEngine)
                        @if($searchEngine === 'yandex')
                            <i class="fab fa-yandex fa-sm mr-2"></i>
                        @else
                            <i class="fab fa-google fa-sm mr-2"></i>
                        @endif
                    @endforeach
                </td>
                <td>{{ $count }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endcomponent
