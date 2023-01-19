
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

    <button type="submit" class="btn btn-default btn-sm parse-positions" data-toggle="tooltip" title="{{ __('Update selected project') }}">
        <i class="fas fa-sync-alt"></i>
    </button>

    <div class="btn-group">
        @foreach ([
            ['name' => 'engines', 'text' => __('Search engine')],
            ['name' => 'words', 'text' => __('Words')],
            ['name' => 'middle', 'text' => __('Middle position')],
            ['name' => 'top3', 'text' => __('TOP') . '3'],
            ['name' => 'top5', 'text' => __('TOP') . '5'],
            ['name' => 'top10', 'text' => __('TOP') . '10'],
            ['name' => 'top30', 'text' => __('TOP') . '30'],
            ['name' => 'top100', 'text' => __('TOP') . '100'],
        ] as $col)
            <a href="javascript:void(0)" class="btn btn-default btn-sm column-visible" data-toggle="tooltip" data-column="{{ $col['name'] }}" title="Скрыть/Показать столбец">{{ $col['text'] }}</a>
        @endforeach
    </div>
</div>



