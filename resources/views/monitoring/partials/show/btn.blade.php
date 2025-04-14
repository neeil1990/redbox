<div class="btn-group">
    @can('edit_query_monitoring')
        <button type="button" class="btn btn-sm btn-default" data-id="{{ $key->id }}" data-toggle="modal" data-target=".modal" data-type="edit_singular">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('delete_query_monitoring')
        <button type="button" class="btn btn-sm btn-default delete-keyword" data-id="{{ $key->id }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
