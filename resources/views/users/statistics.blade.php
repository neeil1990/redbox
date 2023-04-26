@component('component.card', ['title' =>  __('Users statistics') ])
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <style>
            .dt-buttons {
                float: left;
            }
        </style>
    @endslot
    <table class="table table-striped border" id="statistics">
        <thead>
        <tr>
            <th>id</th>
            <th>Информация о юзере</th>
            <th>Тариф</th>
            <th>Обновлений страниц</th>
            <th>Количество действий</th>
            <th>Время проведёное в модулях</th>
            <th style="min-width: 460px; max-width: 460px; width: 460px">{{ __('utm metrics') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($results as $userId => $result)
            <tr>
                <td>{{ $userId }}</td>
                <td data-order="{{ $result['userInfo']['email'] }}">
                    <div>
                        {{ $result['userInfo']['email'] }}
                    </div>
                    <div>
                        {{ $result['userInfo']['name'] . ' ' . $result['userInfo']['last_name']}}
                    </div>
                </td>
                <td>
                    @foreach($result['userInfo']['tariff'] as $role)
                        <div>{{ $role }}</div>
                    @endforeach
                </td>
                <td>
                    {{ $result['stat']['refresh_page_counter'] }}
                </td>
                <td>
                    {{ $result['stat']['actions_counter'] }}
                </td>
                <td>
                    {{ $result['stat']['seconds'] }}
                </td>
                @php($json = json_decode($result['userInfo']['metrics'], true))
                @if(isset($json['utm_source']))
                    <td data-order="{{ strlen($json['utm_source']) }}">
                        <div style="max-width: 420px">
                            <b>utm_source:</b> {{ $json['utm_source'] }}
                        </div>
                    </td>
                @else
                    <td data-order="{{ strlen($result['userInfo']['metrics']) }}">
                        <div style="max-width: 420px">
                            {{ $result['userInfo']['metrics'] }}
                        </div>
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
    @slot('js')
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#statistics').DataTable({
                    lengthMenu: [10, 25, 50, 100],
                    pageLength: 50,
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel'
                    ],
                    language: {
                        lengthMenu: "_MENU_",
                        search: "_INPUT_",
                        searchPlaceholder: "{{ __('Search') }}",
                        paginate: {
                            "first": "«",
                            "last": "»",
                            "next": "»",
                            "previous": "«"
                        },
                    },
                })

                $('.dt-buttons').addClass('ml-2')
                $.each($('.dt-buttons').children('button'), function () {
                    $(this).addClass('btn btn-secondary')
                })
            })
        </script>
    @endslot
@endcomponent
