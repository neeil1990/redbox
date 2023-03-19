@component('component.card', ['title' => __('Project') . " $project->name" ])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>

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
                top: 0;
                z-index: 1;
                background-color: white;
            }

            .custom-info-bg {
                background-color: rgba(23, 162, 184, 0.5) !important;
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Keywords filter') }}</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <form action="" style="display: contents;">
                            <div class="col-4">
                                <div class="form-group">
                                    <label>{{ __('Search engine') }}:</label>
                                    <select name="region" class="custom-select" id="searchengines"
                                            onchange="this.form.submit()">
                                        @if($project->searchengines->count() > 1)
                                            <option value="">{{ __('All search engine and regions') }}</option>
                                        @endif

                                        @foreach($project->searchengines as $search)
                                            @if($search->id == request('region'))
                                                <option value="{{ $search->id }}"
                                                        selected>{{ strtoupper($search->engine) }} {{ $search->location->name }}
                                                    [{{$search->lr}}]
                                                </option>
                                            @else
                                                <option
                                                    value="{{ $search->id }}">{{ strtoupper($search->engine) }} {{ $search->location->name }}
                                                    [{{$search->lr}}]
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-3">
        {{  __('Project') . " $project->name" }}
    </h3>

    <h4>
        Количество фраз: {{ $countQuery }}
    </h4>

    <table class="table table-hover table-bordered no-footer">
        <thead>
        <tr>
            <th>Конкурент?</th>
            <th>Домен ({{ count($competitors) }})</th>
            <th>Поисковые системы</th>
            <th>Видимость</th>
        </tr>
        </thead>
        <tbody>
        @foreach($competitors as $competitor => $info)
            <tr>
                <td>
                    <div>
                        <input type="checkbox" class="change-domain-state" data-target="{{ $competitor }}"
                               @if(isset($info['competitor'])) checked @endif>
                    </div>
                </td>
                <td @if(isset($info['mainPage'])) class="custom-info-bg" @endif>
                    {{ $competitor }}
                    <span class="__helper-link ui_tooltip_w">
                        <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __right" style="width: 460px">
                            <span class="ui_tooltip_content">
                                @foreach($info['urls'] as $engine => $words)
                                    <b class="mb-2 text-info"> {{ $engine }}: </b>
                                    @foreach($words as $word => $stats)
                                        @foreach($stats as $stat)
                                            <div class="mb-2">
                                                {{ $word }}: <a href="{{ $stat }}" target="_blank"> {{ $stat }} </a>
                                            </div>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </span>
                        </span>
                    </span>
                </td>
                <td>
                    @foreach($info['urls'] as $engine => $urls)
                        @if($engine === 'google')
                            <i class="fab fa-google fa-sm mr-2"></i>
                        @endif
                        @if($engine === 'yandex')
                            <i class="fab fa-yandex fa-sm mr-2"></i>
                        @endif
                    @endforeach
                </td>
                <td>
                    @php($count = 0)
                    @foreach($info['urls'] as $engine => $urls)
                        @foreach($urls as $url)
                            @php($count += count($url))
                        @endforeach
                    @endforeach
                    {{ $count }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @slot('js')
        <script>
            $('.change-domain-state').on('click', function () {
                let url = $(this).attr('data-target')
                if ($(this).is(':checked')) {
                    if (confirm(`Вы собираетесь добавить домен "${url}" в конкуренты`)) {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "{{ route('monitoring.add.competitor') }}",
                            data: {
                                '_token': $('meta[name="csrf-token"]').attr('content'),
                                'url': url,
                                'projectId': {{ $project->id }}
                            },
                            success: function (response) {

                            },
                        });
                    } else {
                        $(this).prop('checked', false);
                    }
                } else {
                    if (confirm(`Вы собираетесь убрать домен "${url}" из конкурентов`)) {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "{{ route('monitoring.remove.competitor') }}",
                            data: {
                                '_token': $('meta[name="csrf-token"]').attr('content'),
                                'url': target,
                                'projectId': {{ $project->id }}
                            },
                            success: function (response) {

                            },
                        });
                    } else {
                        $(this).prop('checked', true);
                    }
                }
            })
        </script>
    @endslot
@endcomponent
