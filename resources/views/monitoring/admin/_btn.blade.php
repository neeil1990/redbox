@component('component.admin-card')

    @slot('description')
        <p>Some description <code>.btn.btn-app</code> to an <code>&lt;a&gt;</code> tag to achieve the following:</p>
    @endslot

    @component('component.btn-app', ['href' => route('monitoring.index'), 'class' => 'ml-0'])
        <i class="fas fa-home"></i> {{ __('Projects') }}
    @endcomponent

    @component('component.btn-app', ['href' => route('monitoring.admin'), 'class' => ''])
        <i class="fas fa-users"></i> {{ __('Administration') }}
    @endcomponent

    @component('component.btn-app', ['href' => route('monitoring.stat'), 'class' => ''])
        <i class="fas fa-bullhorn"></i> {{ __('Statistics') }}
    @endcomponent

@endcomponent
