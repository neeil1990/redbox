@component('component.card', ['title' => __('Balance')])

    @slot('css')

    @endslot

    <div class="row">
        <div class="col-md-6">

            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">{{ __('History') }}</h3>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                        <tr>
                            <th>Status</th>
                            <th>Sum</th>
                            <th>Source</th>
                            <th>Date</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($balances as $balance)
                            <tr>
                                <td>
                                    @switch($balance->status)
                                        @case(0)
                                        <small class="badge badge-danger"><i class="far fa-clock"></i> {{ $balance->statuses[$balance->status] }}</small>
                                        @break

                                        @case(1)
                                        <small class="badge badge-success"><i class="fas fa-plus-circle"></i> {{ $balance->statuses[$balance->status] }}</small>
                                        @break

                                        @case(2)
                                        <small class="badge badge-info"><i class="fas fa-minus-circle"></i> {{ $balance->statuses[$balance->status] }}</small>
                                        @break
                                    @endswitch
                                </td>
                                <td>{{ $balance->sum }}</td>
                                <td>{{ $balance->source }}</td>
                                <td>{{ $balance->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr align="center">
                                <td colspan="4">{{ __('No data') }}</td>
                            </tr>
                        @endforelse

                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    <a href="{{ route('balance-add.index') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> {{ __('Add balance') }}
                    </a>
                </div>
            </div>
        </div>
    </div>


    @slot('js')

    @endslot


@endcomponent
