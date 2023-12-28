<form action="{{ route('keywords.store') }}" method="POST">

    <input type="hidden" name="monitoring_project_id" value="{{ $project->id }}">

    <div class="modal-header">
        <h4 class="modal-title">{{__('Add keyword')}}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <div class="modal-body">

        <div class="form-group">
            <label>{{ __('Queries') }}:</label>
            <textarea name="query" class="form-control" rows="10" placeholder="Введите ваш список запросов, каждый с новой строки"></textarea>
            <div class="invalid-feedback query">
                {{ __('Please add queries') }}
            </div>
        </div>

        <div class="input-group">
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="upload">
                <label class="custom-file-label" for="upload">{{ __('Upload CSV file') }}</label>
            </div>
            <div class="input-group-append">
                <button type="button" class="btn btn-success" id="upload-queries">{{ __('Upload') }}</button>
            </div>
        </div>

        <p class="text-sm text-muted">
            Вы можете выгрузить только ключевые слова. Если Вы хотите добавить сразу через файл ключевые слова + группу + релевантную, то перейдите в <a href="/monitoring/create#id={{ $project->id }}">редактирование проекта</a>
        </p>

        <div class="form-group">
            <label class="col-form-label">{{ __('Relative URL') }}:</label>
            <input type="text" class="form-control" name="page" value="">
        </div>

        <div class="form-group">
            <label class="col-form-label">{{ __('Target') }}:</label>
            {{ Form::select('target', [1 => 1, 3 => 3, 5 => 5, 10 => 10, 50 => 50, 100 => 100], 10, ['class' => 'custom-select']) }}
        </div>

        <div class="form-group">
            <label class="col-form-label">{{ __('Groups') }}:</label>
            {{ Form::select('monitoring_group_id', $project->groups->pluck('name', 'id'), null, ['class' => 'custom-select']) }}
        </div>

        <div class="input-group mb-3">
            <input type="text" data-id="{{ $project->id }}" placeholder="{{ __('Name of group') }}" class="form-control">

            <div class="input-group-append">
                <button type="button" class="btn btn-success" id="create-group">{{ __('Create a new group') }}</button>
            </div>
            <div class="invalid-feedback monitoring_group_id">
                {{ __('Please add a group') }}
            </div>
        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
        <button type="button" class="btn btn-success save-modal">{{ __('Save') }}</button>
    </div>

</form>
