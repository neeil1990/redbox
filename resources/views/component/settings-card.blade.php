<form action="{{ $action }}" method="{{ $method }}">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Settings') }}</h3>
        </div>

        <div class="card-body">
            <div class="row">
                {{ $slot }}
            </div>
        </div>

        <div class="card-footer">
            <input type="submit" value="{{ __('Save') }}" class="btn btn-success">
        </div>
    </div>
</form>
