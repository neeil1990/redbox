
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

    <button type="submit" class="btn btn-default btn-sm parse-positions" data-toggle="tooltip" title="{{ __('Update') }}">
        <i class="fas fa-sync-alt"></i>
    </button>
</div>



