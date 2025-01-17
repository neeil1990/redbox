
<div class="col-md-12">

    <button type="button" class="btn btn-default btn-sm checkbox-toggle">
        <i class="far fa-square"></i>
    </button>

    <div class="btn-group">

        <a href="{{ route('monitoring.create') }}" class="btn btn-default btn-sm" data-toggle="tooltip" title="{{ __('Create project') }}">
            <i class="fas fa-plus"></i>
        </a>

        <button type="button" class="btn btn-default btn-sm checkbox-delete" data-toggle="tooltip" title="{{ __('Delete') }}">
            <i class="far fa-trash-alt"></i>
        </button>
    </div>

    <div class="btn-group">
        @foreach ([
            ['name' => 'name', 'text' => 'Название'],
            ['name' => 'users', 'text' => __('Users')],
            ['name' => 'engines', 'text' => __('Search engine')],
            ['name' => 'words', 'text' => __('Words')],
            ['name' => 'middle', 'text' => __('Middle position')],
            ['name' => 'top3', 'text' => __('TOP') . '3'],
            ['name' => 'top5', 'text' => __('TOP') . '5'],
            ['name' => 'top10', 'text' => __('TOP') . '10'],
            ['name' => 'top30', 'text' => __('TOP') . '30'],
            ['name' => 'top100', 'text' => __('TOP') . '100'],
            ['name' => 'budget', 'text' => __('Budget')],
            ['name' => 'mastered', 'text' => __('Mastered')],
        ] as $col)
            <a href="javascript:void(0)" class="btn btn-default btn-sm column-visible click_tracking" data-click="{{ $col['text'] }}" data-toggle="tooltip" data-column="{{ $col['name'] }}" title="Скрыть/Показать столбец">{{ $col['text'] }}</a>
        @endforeach
    </div>

    <div class="btn-group">
        <select id="filter-user-status" class="custom-select custom-select-sm form-control form-control-sm">
            <option value="">{{ __('Show all users status') }}</option>
            @foreach(\App\Http\Controllers\MonitoringProjectUserStatusController::getOptions() as $option)
                <option value="{{ $option['val'] }}">{{ $option['text'] }}</option>
            @endforeach
        </select>
    </div>

</div>



