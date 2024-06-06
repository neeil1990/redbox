@component('component.card', ['title' => __('Meta tags')])

    <div class="row">
        <div class="col-6">

            @component('component.admin-card')

                @slot('description')
                @endslot

                @component('component.btn-app', ['href' => route('meta-tags.index'), 'class' => 'ml-0'])
                    <i class="fas fa-home"></i> {{ __('Home') }}
                @endcomponent

                @component('component.btn-app', ['href' => route('meta-tags.settings'), 'class' => ''])
                    <i class="fas fa-cog"></i> {{ __('Settings') }}
                @endcomponent

            @endcomponent

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Statistic') }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td style="width: 80%;">Количество пользователей модуля</td>
                                <td style="width: 20%;"><span class="text-bold">{{ $response['users'] }}</span></td>
                            </tr>
                            <tr>
                                <td style="width: 80%;">Создано проектов</td>
                                <td style="width: 20%;"><span class="text-bold">{{ $response['projects'] }}</span></td>
                            </tr>
                            <tr>
                                <td style="width: 80%;">Добавлено страниц на мониторинг</td>
                                <td style="width: 20%;"><span class="text-bold">{{ $response['links'] }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

@endcomponent
