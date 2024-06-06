@component('component.card', ['title' => __('Meta tags')])

    <div class="row">
        <div class="col-6">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @component('component.admin-card')

                @slot('description') @endslot

                @component('component.btn-app', ['href' => route('meta-tags.index'), 'class' => 'ml-0'])
                    <i class="fas fa-home"></i> {{ __('Home') }}
                @endcomponent

                @component('component.btn-app', ['href' => route('meta-tags.statistic'), 'class' => ''])
                    <i class="fas fa-bullhorn"></i> {{ __('Statistic') }}
                @endcomponent

            @endcomponent

            @component('component.settings-card', ['action' => '', 'method' => 'GET'])
                <div class="col-6">
                    <div class="form-group">
                        <label>Оставить записи в таблице meta_tags_histories, количество дней</label>
                        <input type="number" name="delete_records" min="0" class="form-control" value="{{ $delete_records }}">
                    </div>
                </div>
            @endcomponent

        </div>
    </div>

@endcomponent
