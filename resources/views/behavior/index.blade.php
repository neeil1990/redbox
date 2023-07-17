@component('component.card', ['title' => __('Behavior')])

    @slot('css')
        <style>
            .behavior {
                background: oldlace;
            }
        </style>
    @endslot

    <div class="card">
        <div class="card-header border-0">
            <h3 class="card-title">{{ __('Sites') }}</h3>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-valign-middle">
                <thead>
                <tr>
                    <th>{{ __('Domain') }}</th>
                    <th>Количество</th>
                    <th>Выполнено</th>
                    <th>Не выполнено</th>
                    <th>Количество уникальных фраз</th>
                    <th></th>
                    <th>{{ __('Url') }}</th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                @foreach($behaviors as $behavior)
                    <tr>
                        <td><a href="{{ route('behavior.show', [$behavior->id]) }}"
                               class="text-muted text-bold">{{ $behavior->domain }}</a>
                        </td>
                        <td>{{ $behavior->phrases()->count() }}</td>
                        <td>{{ $behavior->phrases()->success()->count() }}</td>
                        <td>{{ $behavior->phrases()->fail()->count() }}</td>
                        <td><a href="{{ route('behavior.unique.phrases', $behavior->id) }}">{{ $behavior->phrases()->unique()->get()->count() }}</a></td>
                        <td>
                            <a href="{{ route('behavior.show', [$behavior->id]) }}" class="btn btn-app">
                                <i class="fas fa-project-diagram"></i> {{ __('Go to project') }}
                            </a>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control"
                                       value="{{ route('behavior.check', [$behavior->id]) }}">
                                <div class="input-group-append">
                                <span class="input-group-text">
                                    <a href="{{ route('behavior.check', [$behavior->id]) }}" target="_blank"
                                       class="text-muted"> <i class="fas fa-window-restore"></i></a>
                                </span>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('behavior.edit', [$behavior->id]) }}" class="btn btn-app">
                                <i class="fas fa-edit"></i> {{ __('Add request') }}
                            </a>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>

        <div class="card-footer clearfix">
            <a href="{{ route('behavior.create') }}" class="btn btn-secondary">
                <i class="fas fa-plus"></i> {{ __('Add project') }}
            </a>
        </div>
    </div>


    @slot('js')

    @endslot

@endcomponent
