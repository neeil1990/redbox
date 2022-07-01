<form action="{{ route('keywords.update.plural') }}" method="POST">

    <div class="form-group">
        <label class="col-form-label">{{ __('Relative URL') }}:</label>
        <input type="text" class="form-control" name="page">
    </div>

    <div class="form-group">
        <label class="col-form-label">{{ __('Target') }}:</label>
        {{ Form::select('target', [1 => 1, 3 => 3, 5 => 5, 10 => 10, 50 => 50, 100 => 100], null, ['class' => 'custom-select']) }}
    </div>

    <div class="form-group">
        <label class="col-form-label">{{ __('Groups') }}:</label>
        {{ Form::select('monitoring_group_id', $project->groups->pluck('name', 'id'), null, ['class' => 'custom-select']) }}
    </div>

    <div class="input-group mb-3">
        <input type="text" data-id="{{$project->id}}" placeholder="{{ __('Name of group') }}" class="form-control">

        <div class="input-group-append">
            <button type="button" class="btn btn-success" id="create-group">{{ __('Create a new group') }}</button>
        </div>
    </div>

</form>
