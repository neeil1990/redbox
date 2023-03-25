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
                <input type="text" name="startDate" class="form-control datetimepicker-input" data-target="#startDatePicker" data-toggle="datetimepicker" value="{{ Carbon::now()->subMonth()->isoFormat('DD.MM.YYYY') }}"/>
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
            <select class="custom-select" name="group">
                <option value="">Все</option>
                @foreach($project['groups'] as $groups)
                    <option value="{{ $groups['id'] }}">{{ $groups['name'] }}</option>
                @endforeach
            </select>
        </div>

    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" class="btn btn-success save-modal">{{ __('Export') }}</button>
    </div>

</form>
