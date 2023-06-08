@component('component.card', ['title' =>  __('General statistics users') ])
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <style>
            .dt-buttons {
                float: left;
            }

            #statistics > thead > tr.filters > th:nth-child(8) {
                display: none;
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
            <th>{{ __('utm metrics') }}</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($results as $userId => $result)
            <tr>
                <td data-order="{{ $userId }}">
                    {{ $userId }}
                </td>
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
                    {{ Carbon::createFromTimestampUTC($result['stat']['seconds'])->toTimeString() }}
                </td>
                @php($json = $result['userInfo']['metrics'])
                @if(is_array($json))
                    @if(isset($json['utm_source']))
                        <td data-order="{{ $json['utm_source'] }}">
                            <b>utm_source</b>: {{ $json['utm_source'] }}
                        </td>
                    @else
                        @foreach($json as $key => $val)
                            @php($split = explode(':', $val))
                            @if($split[0] === 'utm_source')
                                <td data-order="{{ $split[1] }}">
                                    <b>utm_source</b>: {{ $split[1] }}
                                </td>
                                @break
                            @endif
                        @endforeach
                    @endif
                @else
                    <td>
                        <div style="max-width: 420px"></div>
                    </td>
                @endif
                <td>
                    <a href="/visit-statistics/{{ $userId }}" class="btn btn-default">
                        <i class="fas fa-chart-pie"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @slot('js')
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script>
            $('#statistics thead tr').clone(true).addClass('filters').appendTo('#statistics thead');

            $(document).ready(function () {
                $('#statistics').DataTable({
                    orderCellsTop: true,
                    fixedHeader: true,
                    lengthMenu: [10, 25, 50, 100],
                    pageLength: 50,
                    dom: 'lBfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'csv',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                    ],
                    aoColumnDefs: [
                        {
                            bSortable: false,
                            aTargets: [7]
                        }
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
                    initComplete: function () {
                        let api = this.api();

                        api.columns().eq(0).each(function (colIdx) {
                            let cell = $('.filters th').eq($(api.column(colIdx).header()).index());
                            $(cell).html('<input type="text" class="form form-control"/>');

                            $('input', $('.filters th').eq($(api.column(colIdx).header()).index()))
                                .off('keyup change').on('change', function (e) {
                                $(this).attr('title', $(this).val());
                                let regexr = '({search})';

                                api.column(colIdx).search(
                                    this.value != ''
                                        ? regexr.replace('{search}', '(((' + this.value + ')))')
                                        : '',
                                    this.value != '',
                                    this.value == ''
                                )
                                    .draw();
                            }).on('keyup', function (e) {
                                e.stopPropagation();
                                $(this).trigger('change');
                                $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
                            });
                        });
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
