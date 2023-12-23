@component('component.card', ['title' => __('Project') . ' ' .  $project->name ])
    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>
        <!-- daterange picker -->
        <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">

        <style>
            .exist-position {
                color: #28a745 !important;
                font-weight: bold;
            }
        </style>
    @endslot

    <div id="toast-container" class="toast-top-right error-message" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message"></div>
        </div>
    </div>

    <div class="row">
        @foreach($navigations as $navigation)
            <div class="col-lg-2 col-6">
                <a href="{{ $navigation['href'] }}" class="small-box {{ $navigation['bg'] }}" style="min-height: 137px">
                    <div class="inner">
                        @if($navigation['h3'])
                            <h3 class="mb-0">{{ $navigation['h3'] }}</h3>
                        @endif

                        {!! $navigation['content'] !!}

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

    <div class="d-flex flex-row mb-3 mt-3 btn-group w-50">
        <a class="btn btn-outline-secondary" href="{{ route('monitoring.competitors', $project->id) }}">
            {{ __('My competitors') }}
        </a>
        <a class="btn btn-outline-secondary" href="{{ route('monitoring.competitors.positions', $project->id) }}">
            {{ __('Comparison with competitors') }}
        </a>
    </div>

    <div id="dateRange" class="mt-5">
        <h3 class="mt-3">{{ __('Project') . ' ' .  $project->name }}</h3>
        <h3>{{ __('Changes by top and date') }}</h3>
        <div class="card mt-3">
            <div class="card-header d-flex flex-row justify-content-start align-items-center">
                <div class="input-group col-8 pl-0 ml-0">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="date-range">
                    <select name="region" class="custom-select" id="searchEngines">
                        @foreach($searchEngines as $search)
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
                    <button id="competitors-history-positions" class="btn btn-default"
                            style="border-top-left-radius: 0; border-bottom-left-radius: 0">
                        {{ __('Analyse') }}
                    </button>
                </div>
            </div>
            <div class="card-body" id="history-block">
                <table class="table table-bordered w-50">
                    <thead>
                    <tr>
                        <th>{{ __('Date range') }}</th>
                        <th>{{ __('Region') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="changeDatesTbody">
                    @if(count($project->dates) > 0)
                        @foreach($project->dates as $result)
                            <tr @if($result['state'] === 'in queue' || $result['state'] === 'in process') class="need-check"
                                data-id="{{ $result['id'] }}"
                                id="analyse-in-queue-{{ $result['id'] }}" @endif>
                                <td>{{ $result['range'] }}</td>
                                <td>
                                    @foreach($searchEngines as $engine)
                                        @if($engine['id'] == json_decode($result['request'], true)['region'])
                                            {{ strtoupper($engine['engine']) }}, {{ $engine['location']['name'] }}
                                            [{{ $engine['location']['lr'] }}]
                                            @break
                                        @endif
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @if($result['state'] === 'ready')
                                        <a class="btn btn-default"
                                           href="{{ route('monitoring.changes.dates.result', $result['id']) }}"
                                           target="_blank">{{ __('show') }}</a>
                                        <button class="btn btn-default remove-error-results"
                                                data-id="{{ $result['id'] }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @elseif($result['state'] === 'in queue')
                                        {{ __("In queue") }}
                                        <img src="/img/1485.gif" style="width: 20px; height: 20px;">
                                    @elseif($result['state'] === 'in process')
                                        {{ __("In process") }}
                                        <img src="/img/1485.gif" style="width: 20px; height: 20px;">
                                    @else
                                        {{ __('Fail') }}
                                        <button class="btn btn-default remove-error-results"
                                                data-id="{{ $result['id'] }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr id="empty-row">
                            <td class="text-center" colspan="3">{{ __('Empty') }}</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @slot('js')

    @endslot()
@endcomponent
