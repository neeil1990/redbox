
<!-- Check all button -->
<button type="button" class="btn btn-default btn-sm checkbox-toggle tooltip-on" title="Выбрать все">
    <i class="far fa-square"></i>
</button>

<div class="btn-group">
    <button type="button" class="btn btn-default btn-sm tooltip-on" data-toggle="modal" data-target=".modal" data-type="create_keywords" title="Добавить запрос">
        <i class="fas fa-plus"></i>
    </button>

    <button type="button" class="btn btn-default btn-sm tooltip-on" data-toggle="modal" data-target=".modal" data-type="edit_plural" title="Редактировать запросы">
        <i class="fas fa-pen"></i>
    </button>

    <button type="button" class="btn btn-default btn-sm delete-multiple tooltip-on" title="Удалить запросы">
        <i class="far fa-trash-alt"></i>
    </button>
</div>
<!-- /.btn-group -->

<div class="btn-group">
    <button type="button" class="btn btn-default btn-sm parse-positions-keys tooltip-on" title="Добавить в очередь выбранные">
        <i class="fas fa-layer-group"></i>
    </button>

    <button type="button" class="btn btn-default btn-sm parse-positions tooltip-on" title="Добавить в очередь все">
        <i class="fas fa-sync-alt"></i>
    </button>
</div>

<div class="btn-group">
    @foreach ([
            ['name' => 'query', 'text' => __('Query')],
            ['name' => 'url', 'text' => __('URL')],
            ['name' => 'group', 'text' => __('Group')],
            ['name' => 'target', 'text' => __('Target')],
            ['name' => 'dynamics', 'text' => __('Dynamics')],
        ] as $col)
        <a href="javascript:void(0)" class="btn btn-default btn-sm tooltip-on column-visible" data-toggle="tooltip" data-column="{{ $col['name'] }}" title="Скрыть/Показать столбец">{{ $col['text'] }}</a>
    @endforeach
</div>

<div class="float-right"></div>
<!-- /.float-right -->
