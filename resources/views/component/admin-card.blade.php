@hasanyrole('Super Admin|admin')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Administration panel') }}</h3>
        </div>
        <div class="card-body">
            {{ $description }}

            {{ $slot }}
        </div>
    </div>
@endhasanyrole
