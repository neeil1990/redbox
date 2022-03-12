@component('component.card', ['title' => __('Behavior')])

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
                        @error('sum') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                </div>
                <div class="card-footer">
                    {!! Form::submit('Пополнить', ['class' => 'btn btn-success']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    @slot('js')

    @endslot


@endcomponent
