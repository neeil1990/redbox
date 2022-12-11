@component('component.card', ['title' => __('Balance')])

    @slot('css')

    @endslot

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Пополнить баланс</h3>
                </div>

                {!! Form::open(['method' => 'POST', 'route' => ['balance-add.store']]) !!}
                <div class="card-body">

                    <div class="form-group">
                        {!! Form::label('sum', 'Сумма') !!}
                        {!! Form::number('sum', null, ['class' => 'form-control' . ($errors->has('domain') ? ' is-invalid' : ''), 'min' => '1']) !!}
                        @error('sum') <span class="error invalid-feedback d-block">{{ $message }}</span> @enderror
                    </div>

                </div>
                <div class="card-footer">
                    {!! Form::submit('Пополнить', ['class' => 'btn btn-success']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
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
                                            <small class="badge badge-danger"><i
                                                    class="far fa-clock"></i> {{ $balance->statuses[$balance->status] }}
                                            </small>
                                            @break

                                        @case(1)
                                            <small class="badge badge-success"><i
                                                    class="fas fa-plus-circle"></i> {{ $balance->statuses[$balance->status] }}
                                            </small>
                                            @break

                                        @case(2)
                                            <small class="badge badge-info"><i
                                                    class="fas fa-minus-circle"></i> {{ $balance->statuses[$balance->status] }}
                                            </small>
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
            </div>
        </div>
    </div>

    @if($response == 'success')
        @include('balance.success')
    @endif

    @if($response == 'fail')
        @include('balance.fail')
    @endif

    @slot('js')

        @if($response)
            <script>
                let url = new URL(window.location.href)
                let invId = url.searchParams.get('InvId')
                if (invId !== null) {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: "{{ route('counting.metrics') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: invId,
                        },
                        success: function (response) {
                            if (response.click) {
                                $('.modal').modal('show');
                                $('#counting-metrics').trigger('click')
                            }

                            $('#counting-metrics-block').remove()
                        },
                    });
                }
            </script>
        @else
            <script>
                $('#counting-metrics-block').remove()
            </script>
        @endif

    @endslot

@endcomponent
