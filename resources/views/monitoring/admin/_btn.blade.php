<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('Administration panel') }}</h3>
    </div>
    <div class="card-body">
        <p>Some description <code>.btn.btn-app</code> to an <code>&lt;a&gt;</code> tag to achieve the following:</p>

        <a href="{{ route('monitoring.index') }}" class="btn btn-app ml-0">
            <i class="fas fa-home"></i> {{ __('Projects') }}
        </a>

        <a href="{{ route('monitoring.admin') }}" class="btn btn-app">
            <i class="fas fa-users"></i> {{ __('Administration') }}
        </a>
        <a href="{{ route('monitoring.stat') }}" class="btn btn-app">
            <i class="fas fa-bullhorn"></i> {{ __('Statistics') }}
        </a>
    </div>
</div>
