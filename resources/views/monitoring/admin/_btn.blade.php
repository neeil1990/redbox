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

    @component('component.btn-app', ['href' => route('set.positions'), 'class' => ''])
        <i class="fas fa-plus"></i> {{ __('Set positions') }}
    @endcomponent

    @component('component.btn-app', ['href' => route('offset.positions'), 'class' => ''])
        <i class="fas fa-link"></i> {{ __('Корректировать') }}
    @endcomponent

    @component('component.btn-app', ['href' => route('monitoring-permissions.index'), 'class' => ''])
        <i class="fas fa-lock-open"></i> {{ __('Права') }}
    @endcomponent

@endcomponent
