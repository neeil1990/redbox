@component('component.card', ['title' => __('Http headers')])

<div class="row">
    <div class="col-6">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @component('component.admin-card')

            @slot('description') @endslot

            @component('component.btn-app', ['href' => '/http-headers', 'class' => 'ml-0'])
                <i class="fas fa-home"></i> {{ __('Home') }}
            @endcomponent

        @endcomponent

        @component('component.settings-card', ['action' => '', 'method' => 'GET'])
            <div class="col-6">
                <div class="form-group">
                    <label>Оставить записи в таблице http_headers, количество дней</label>
                    <input type="number" name="delete_records" min="0" class="form-control" value="{{ $delete_records }}">
                </div>
            </div>
        @endcomponent

    </div>
</div>

@endcomponent
