<form action="/monitoring/{{ $project['id'] }}/export" method="GET">

    <div class="modal-header">
        <h4 class="modal-title">{{__('Export')}}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <div class="modal-body">

        <!-- Date -->
        <div class="form-group">
            <label>Начальная дата:</label>
            <div class="input-group date" id="startDatePicker" data-target-input="nearest">
                <input type="text" name="startDate" class="form-control datetimepicker-input" data-target="#startDatePicker" data-toggle="datetimepicker" value="{{ Carbon::now()->startOfMonth()->isoFormat('DD.MM.YYYY') }}"/>
                <div class="input-group-append">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
            </div>
        </div>

        <!-- Date -->
        <div class="form-group">
            <label>Конечная дата:</label>
            <div class="input-group date" id="endDatePicker" data-target-input="nearest">
                <input type="text" name="endDate" class="form-control datetimepicker-input" data-target="#endDatePicker" data-toggle="datetimepicker" value="{{ Carbon::now()->isoFormat('DD.MM.YYYY') }}"/>
                <div class="input-group-append">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Тип</label>
            <select class="custom-select" name="mode">
                <option value="range">Все дни</option>
                <option value="finance">Финансовый</option>
                <option value="datesFind">Две даты (фиксированные)</option>
                <option value="dates">Две даты (плавающие)</option>
                <option value="randWeek">Случайная дата 1 за неделю</option>
                <option value="randMonth">Случайная дата 1 за месяц</option>
            </select>
        </div>

        <div class="form-group">
            <label>Регион</label>
            <select class="custom-select" name="region">
                @foreach($project['searchengines'] as $searchengines)
                <option value="{{ $searchengines['id'] }}">{{ $searchengines['location']['name'] }} [{{ $searchengines['lr'] }}]</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Группа</label>
            <select multiple class="custom-select" name="group[]">
                @foreach($project['groups'] as $groups)
                    <option value="{{ $groups['id'] }}">{{ $groups['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Формат</label>
            <select class="custom-select" name="format">
                <option value="pdf">PDF</option>
                <option value="xls">Excel</option>
                <option value="html">HTML</option>
                <option value="csv">CSV</option>
            </select>
        </div>

        <div class="row">

            <div class="col-sm-12">
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" name="dynamicsDays" type="checkbox" id="dynamicsDays" value="1">
                        <label for="dynamicsDays" class="custom-control-label">Убрать динамику по дням</label>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <!-- checkbox -->
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" name="phrasalCol" type="checkbox" id="phrasal" value="1">
                        <label for="phrasal" class="custom-control-label">{{ __('YW') }} "[]"</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" name="baseCol" type="checkbox" id="base" value="1">
                        <label for="base" class="custom-control-label">{{ __('YW') }}</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" name="exactCol" type="checkbox" id="exact" value="1">
                        <label for="exact" class="custom-control-label">{{ __('YW') }} "[!]"</label>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <!-- checkbox -->
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" name="groupCol" type="checkbox" id="group" value="1">
                        <label for="group" class="custom-control-label">{{ __('Group') }}</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" name="dynamicsCol" type="checkbox" id="dynamics" value="1">
                        <label for="dynamics" class="custom-control-label">{{ __('Dynamics') }}</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" name="urlCol" type="checkbox" id="url" value="1">
                        <label for="url" class="custom-control-label">{{ __('URL') }}</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row d-none" id="finance">
            <div class="col-sm-6">
                <!-- checkbox -->
                <div class="form-group">
                    @foreach([1, 3, 5, 10, 20, 50, 100] as $top)
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" name="price_top_{{ $top }}Col" type="checkbox" id="price_top_{{ $top }}" value="1" @if(in_array($top, [10])) checked @endif>
                        <label for="price_top_{{ $top }}" class="custom-control-label">{{ __('Price') }} top-{{ $top }}</label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="col-sm-6">
                <!-- checkbox -->
                <div class="form-group">
                    @foreach([1, 3, 5, 10, 20, 50, 100] as $top)
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" name="days_top_{{ $top }}Col" type="checkbox" id="days_top_{{ $top }}" value="1" @if(in_array($top, [1, 3, 5, 10])) checked @endif>
                            <label for="days_top_{{ $top }}" class="custom-control-label">{{ __('Days') }} top-{{ $top }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" class="btn btn-success save-modal">{{ __('Export') }}</button>
    </div>

</form>
