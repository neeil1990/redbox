@component('component.card', ['title' => __('Behavior')])

    @slot('css')

    @endslot

    <div class="card">
        <div class="card-header border-0">
            <h3 class="card-title">{{ __('Sites') }}</h3>
            <div class="card-tools">
                <a href="#" class="btn btn-tool btn-sm">
                    <i class="fas fa-download"></i>
                </a>
                <a href="#" class="btn btn-tool btn-sm">
                    <i class="fas fa-bars"></i>
                </a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-valign-middle">
                <thead>
                <tr>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Domain') }}</th>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Url') }}</th>
                    <th>{{ __('More') }}</th>
                </tr>
                </thead>

                <tbody>
                @foreach($behaviors as $behavior)
                <tr>
                    <td>
                        @if($behavior->status)
                            <i class="fas fa-check-square">
                        @else
                            <i class="fas fa-window-close">
                        @endif
                    </td>
                    <td>{{ $behavior->domain }}</td>
                    <td>{{ $behavior->code }}</td>
                    <td>
                        {{ route('behavior.check', [$behavior->id]) }}
                        <a href="{{ route('behavior.check', [$behavior->id]) }}" target="_blank" class="text-muted"> <i class="fas fa-window-restore"></i></a>
                    </td>
                    <td>
                        <a href="{{ route('behavior.show', [$behavior->id]) }}" class="text-muted">
                            <i class="fas fa-search"></i>
                        </a>
                    </td>
                </tr>
                @endforeach

                </tbody>
            </table>
        </div>

        <div class="card-footer clearfix">
            <a href="{{ route('behavior.create') }}" class="btn btn-secondary">
                <i class="fas fa-plus"></i> {{ __('Add item') }}
            </a>
        </div>
    </div>


    @slot('js')

    @endslot


@endcomponent
