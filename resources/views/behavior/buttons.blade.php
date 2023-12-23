<div class="col-6 mb-3">
    <a href="{{ route('behavior.edit_project', $behavior->id) }}" class="btn btn-success">
        {{ __('Edit project') }}
    </a>
    <a href="{{ route('behavior.edit', [$behavior->id]) }}" class="btn btn-success">
        {{ __('Add request') }}
    </a>
</div>

<div class="col-6 mb-3">
    <a href="{{ route('behavior.phrases.destroy', [$behavior->id]) }}" class="btn btn-warning destroy-phrases">
        Удалить все фразы 
    </a>
    <a href="{{ route('behavior.destroy', [$behavior->id]) }}" class="btn btn-danger destroy-project">
        Удалить проект
    </a>
</div>

