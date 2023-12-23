<div class="card sticky-top" id="{{ $id }}">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ion ion-clipboard mr-1"></i>
            {{ $title }}
        </h3>
    </div>

    <div class="card-body">
        <ul class="todo-list" data-widget="todo-list">
            @foreach($items as $item)
                @include('manage-access.partials._items', ['item' => $item])

                @foreach($item->permissions as $permissions)
                    @include('manage-access.partials._items_permissions', ['item' => $permissions])
                @endforeach
            @endforeach
        </ul>
    </div>

    <div class="card-footer clearfix">
        <button type="button" class="btn btn-primary float-right add-item">
            <i class="fas fa-plus"></i>
            {{ __('Add item') }}
        </button>
    </div>
</div>
