
<!-- Check all button -->
<button type="button" class="btn btn-default btn-sm checkbox-toggle tooltip-on" title="Выбрать все">
    <i class="far fa-square"></i>
</button>

<div class="btn-group queries-controls">
    @can('create_query_monitoring')
        <button type="button" class="btn btn-default btn-sm tooltip-on" data-toggle="modal" data-target=".modal" data-type="create_keywords" title="Добавить запрос">
            <i class="fas fa-plus"></i>
        </button>
    @endcan

    @can('edit_query_monitoring')
        <button type="button" class="btn btn-default btn-sm tooltip-on" data-toggle="modal" data-target=".modal" data-type="edit_plural" title="Редактировать запросы">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('delete_query_monitoring')
    <button type="button" class="btn btn-default btn-sm delete-multiple tooltip-on" title="Удалить запросы">
        <i class="far fa-trash-alt"></i>
    </button>
    @endcan
</div>
<!-- /.btn-group -->

<div class="btn-group positions-controls">
    @can('update_position_monitoring')
        <button type="button" class="btn btn-default btn-sm parse-positions-keys tooltip-on" title="Добавить в очередь выбранные">
            <i class="fas fa-layer-group"></i>
        </button>
    @endcan

    @can('update_position_all_monitoring')
        <button type="button" class="btn btn-default btn-sm parse-positions tooltip-on" title="Добавить в очередь все">
            <i class="fas fa-sync-alt"></i>
        </button>
    @endcan
</div>

<div class="btn-group columns-hidden">
    @foreach ([
            ['name' => 'query', 'text' => __('Query'), 'default' => 'on'],
            ['name' => 'url', 'text' => __('URL'), 'default' => 'on'],
            ['name' => 'group', 'text' => __('Group'), 'default' => 'on'],
            ['name' => 'target_url', 'text' => __('Target URL'), 'default' => 'off'],
            ['name' => 'target', 'text' => __('Target'), 'default' => 'on'],
            ['name' => 'dynamics', 'text' => __('Dynamics'), 'default' => 'on'],
            ['name' => 'base', 'text' => __('YW'), 'default' => 'off'],
            ['name' => 'phrasal', 'text' => __('YW') . ' "[]"', 'default' => 'off'],
            ['name' => 'exact', 'text' => __('YW') . ' "[!]"', 'default' => 'off'],
        ] as $col)
        <a href="javascript:void(0)" class="btn btn-default btn-sm tooltip-on column-visible" data-toggle="tooltip" data-default="{{ $col['default'] }}" data-column="{{ $col['name'] }}" title="Скрыть/Показать столбец">{{ $col['text'] }}</a>
    @endforeach
</div>

<div class="float-right"></div>
<!-- /.float-right -->
