@component('component.card', ['title' =>  __('Your limits') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
    @endslot
    <div class="dropdown p-0 m-0 nav-item">
        <table id="table" class="table table-bordered p-0 m-0">
            <thead>
            <tr>
                <th>{{ __('Module') }}</th>
                <th>{{ __('Limits') }}</th>
                <th>{{ __('Left') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($limitsStatistics as $key => $tariff)
                @if($key != 'price')
                    <tr class="{{ $key }}">
                        <td>{{ $tariff['name'] }}</td>
                        <td>
                            @if($tariff['value'] === 1000000)
                                {{ __('No restrictions') }}
                            @else
                                {{ $tariff['value'] }}
                            @endif
                        </td>
                        <td>
                            @if(gettype($tariff['used']) == 'integer')
                                {{ $tariff['value'] - $tariff['used'] }}
                            @else
                                {{ $tariff['used'] }}
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
    @slot('js')
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script>
            $('#table').dataTable({
                pageLength: 50,
            })
        </script>
    @endslot
@endcomponent
