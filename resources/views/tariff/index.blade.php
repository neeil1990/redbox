@component('component.card', ['title' =>  __('Tariff') ])
    @slot('css')
        <style>
            #app > div > div > div.card-body > div > div.col-md-12.d-flex.flex-row.flex-wrap > div > div.card-body > div:hover {
                background: oldlace;
                cursor: pointer;
            }
        </style>
    @endslot
    <div class="row">
        @if (session('info'))
            <div class="col-md-12">
                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> {{ __('info') }}!</h5>
                    {{ session('info') }}
                </div>
            </div>
        @endif

        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Tariff') }}</h3>
                </div>

                {!! Form::open(['method' => 'POST', 'route' => ['tariff.store']]) !!}

                <div class="card-body">

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-ban"></i> {{ __('Error') }}!</h5>
                            {{ session('error') }}
                        </div>
                    @endif

                    @include('tariff.partials._form')

                    @include('tariff.partials._table', ['id' => 'total'])
                </div>

                <div class="card-footer">
                    {!! Form::submit('Купить', ['class' => 'btn btn-success']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        @if($actual->isNotEmpty())
            <div class="col-md-6">
                @include('tariff.subscribe')
            </div>
        @endif

        <div class="col-md-12 d-flex flex-row flex-wrap">
            @foreach ($tariffsArray as $tariff)
                <div class="card col-3 p-0">
                    <div class="card-header bg-primary">
                        Тариф: {{ $tariff['name'] }}
                    </div>
                    <div class="card-body">
                        @foreach ($tariff['settings'] as $module)
                            @if($module['name'] !== 'Цена тарифа')
                                <div class="border-bottom pt-2">
                                    {{ $module['name'] }}:
                                    @if($module['value'] === 1000000)
                                        <b>Без ограничений</b>
                                    @else
                                        <b>{{ $module['value'] }}</b>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

    </div>
    @section('js')

        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

        <script>

            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "1500"
            };

            $('#tariff, #period').change(function () {
                let tariff = $('#tariff').val();
                let period = $('#period').val();

                axios.request({
                    url: "/tariff/total",
                    method: "POST",
                    data: {
                        name: tariff,
                        period: period,
                    },
                }).then(function (response) {
                    let total = $('#total tbody');

                    total.find('tr').remove();
                    $.each(response.data, function (i, val) {
                        let tr = $('<tr />');
                        tr.append($('<th />').css('width', '50%').text(val.title), $('<td />').text(val.value));
                        total.append(tr);
                    });
                }).catch(function (error) {
                    if (error.response) {
                        toastr.error(error.response.data.message);
                    }
                });
            });

            $('#unsubscribe').click(function () {
                axios.request({
                    url: "{{ route('tariff.unsubscribe', ['confirm']) }}",
                    method: "GET",
                }).then(function (response) {
                    let msg = `Вам будет начислено ${response.data.prices.priceWithDiscount} баллов за ${response.data.active_days} дней, по текущей ставке тарифа ${response.data.prices.percent}%. Вы уверены, что хотите отписаться от тарифа?`;
                    let result = confirm(msg);
                    if (result) {
                        axios.get("{{ route('tariff.unsubscribe', ['canceled']) }}")
                            .then(function () {
                                window.location.reload();
                            })
                            .catch(function (error) {
                                if (error.response) {
                                    toastr.error(error.response.data.message);
                                }
                            });
                    }
                }).catch(function (error) {
                    if (error.response) {
                        toastr.error(error.response.data.message);
                    }
                });
            });
        </script>

    @endsection
@endcomponent

