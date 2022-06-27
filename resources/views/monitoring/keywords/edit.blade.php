<form action="{{ route('keywords.update', $keyword->id) }}" method="PATCH">

    <div class="form-group">
        <label class="col-form-label">{{ __('Query') }}:</label>
        <input type="text" class="form-control" name="query" value="{{ $keyword->query }}">
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
        {{ Form::select('group', $keyword->project->groups->pluck('name', 'id'), $keyword->monitoring_group_id, ['class' => 'custom-select']) }}
    </div>

</form>
