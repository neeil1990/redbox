<form action="{{ route('keywords.update', $keyword->id) }}" method="PATCH">

    <div class="modal-header">
        <h4 class="modal-title">{{__('Edit keyword')}}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <div class="modal-body">
        <div class="form-group">
            <label class="col-form-label">{{ __('Query') }}:</label>
            <input type="text" class="form-control" name="query" value="{{ $keyword->query }}">
            <div class="invalid-feedback query">
                {{ __('Please add queries') }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-form-label">{{ __('Relative URL') }}:</label>
            <input type="text" class="form-control" name="page" value="{{ $keyword->page }}">
        </div>

        <div class="form-group">
            <label class="col-form-label">{{ __('Target') }}:</label>
            {{ Form::select('target', [1 => 1, 3 => 3, 5 => 5, 10 => 10, 50 => 50, 100 => 100], $keyword->target, ['class' => 'custom-select']) }}
        </div>

        <div class="form-group">
            <label class="col-form-label">{{ __('Groups') }}:</label>
            {{ Form::select('monitoring_group_id', $keyword->project->groups->pluck('name', 'id'), $keyword->monitoring_group_id, ['class' => 'custom-select']) }}
        </div>

        <div class="input-group mb-3">
            <input type="text" data-id="{{ $keyword->project->id }}" placeholder="{{ __('Name of group') }}" class="form-control">

            <div class="input-group-append">
                <button type="button" class="btn btn-success" id="create-group">{{ __('Create a new group') }}</button>
            </div>
        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
        <button type="button" class="btn btn-success save-modal">{{ __('Save') }}</button>
    </div>

</form>
