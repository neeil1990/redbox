@component('component.card', ['title' =>  __('Result though analyse') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <style>
            .fa {
                color: grey;
            }

            tr:hover .fa {
                color: black;
            }

            .sticky {
                position: sticky;
                top: 0;
                background: white
            }

            .render-child {
                background: #f5f7ff !important;
            }

            .child-table {
                z-index: 100 !important;
                word-break: break-word
            }

            .dt-buttons {
                margin-left: 20px;
                float: left;
            }

            .fixed-width {
                z-index: 1003 !important;
                min-width: 400px !important;
                max-width: 400px !important;
            }

            #though-table > tbody > tr > td:nth-child(8)::after {
                content: " / {{ $countUniqueScanned }}";
            }
        </style>
    @endslot

    <div class="text-center" id="preloaderBlock">
        <img src="{{ asset('/img/1485.gif') }}" alt="preloader_gif">
        <p>Получено <b id="getCount"> {{ $count }} </b> из <b>{{ $allCount }}</b></p>
    </div>
    <div style="display: none" id="though-block">
        <table class="table table-bordered table-striped dtr-inline" id="though-table">
            <thead>
            <tr>
                <th class="sticky"></th>
                <th class="sticky">{{ __('Word') }}</th>
                <th class="sticky fixed-width">Пересечения</th>
                <th class="sticky">Сумма tf</th>
                <th class="sticky">Сумма idf</th>
                <th class="sticky">Сумма повторений в тексте посадочной страницы</th>
                <th class="sticky">Сумма повторений в ссылке посадочной страницы</th>
                <th class="sticky">Сумма количества вхождений</th>
            </tr>
            </thead>
            <tbody>
            @foreach($though->result as $key => $item)
                <tr>
                    <td>
                        <i class="fa fa-plus show-more" data-target="{{ $key }}"></i>
                    </td>
                    <td>{{ $key }}</td>
                    <td>
                        <a data-toggle="collapse" href="#collapseExample{{ $key }}"
                           role="button" aria-expanded="false" aria-controls="collapseExample{{ $key }}">
                            Посмотреть таблицу
                        </a>
                        <div class="collapse" id="collapseExample{{ $key }}">
                            <table class="child-table">
                                <thead>
                                <tr>
                                    <th class="col-9">Ссылка</th>
                                    <th class="col-3">Кол-во вхождений</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($item['total']['throughLinks'] as $keyLink => $link)
                                    <tr>
                                        <td>
                                            <a href="{{ $keyLink }}" target="_blank">
                                                {{ $keyLink }}
                                            </a>
                                        </td>
                                        <td>{{ $link }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </td>
                    <td>{{ $item['total']['tf'] }}</td>
                    <td>{{ $item['total']['idf'] }}</td>
                    <td>{{ $item['total']['repeatInTextMainPage'] }}</td>
                    <td>{{ $item['total']['repeatInLinkMainPage'] }}</td>
                    <td>{{ $item['total']['throughCount'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @slot('js')
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script>
            let totalResults = "{{ json_encode($allElems) }}";
            totalResults = totalResults.replace(/&quot;/g, '"')
            totalResults = JSON.parse(totalResults)
            let count = {{ $count }};
            let allCount = {{ $allCount }};
            let iterator = count
            let recordId = "{{ $though->id }}";

            $(document).ready(function () {
                let thoughTable = $('#though-table').DataTable({
                    "order": [[3, "desc"]],
                    "pageLength": 50,
                    "searching": true,
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel'
                    ],
                });
                $('.dt-button').addClass('btn btn-secondary')

                setTimeout(() => {
                    getNextItems(recordId, thoughTable, count, iterator, allCount)
                }, 3000)
            });

            function removeElems() {
                $('.remove-child').click(function () {
                    let target = $(this).attr('data-target')
                    $("tr[data-target='" + target + "']").remove();
                })
            }

            function getNextItems(recordId, table, count, iterator, allCount) {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('get.slice.result') }}",
                    data: {
                        id: recordId,
                        count: count
                    },
                    success: function (response) {
                        $.each(response.elems, function (key, value) {
                            let tbody = ''

                            $.each(value['total']['throughLinks'], function (keyLink, link) {
                                tbody +=
                                    '<tr> ' +
                                    '   <td> ' +
                                    '       <a href="' + keyLink + '" target="_blank"> ' + keyLink + '</a> ' +
                                    '   </td>' +
                                    '   <td>' + link + '</td>' +
                                    '</tr>'
                            })

                            let ChildTable = '<td> ' +
                                '<a data-toggle="collapse" href="#collapseExample' + key + '" role="button" aria-expanded="false" aria-controls="collapseExample' + key + '">' +
                                'Посмотреть таблицу </a> ' +
                                '<div class="collapse" id="collapseExample' + key + '"> ' +
                                '<table class="child-table"> ' +
                                '   <thead> ' +
                                '       <tr> ' +
                                '           <th class="col-9">Ссылка</th> ' +
                                '           <th class="col-3">Кол-во вхождений</th> ' +
                                '       </tr> ' +
                                '   </thead> ' +
                                '<tbody> ' +
                                '</tbody> ' +
                                tbody +
                                '</table> ' +
                                '</div> ' +
                                '</td>'

                            table.row.add({
                                0: '<i class="fa fa-plus show-more" data-target="' + key + '"></i> ',
                                1: key,
                                2: ChildTable,
                                3: (value['total']['tf']).toFixed(5),
                                4: (value['total']['idf']).toFixed(5),
                                5: value['total']['repeatInTextMainPage'],
                                6: value['total']['repeatInLinkMainPage'],
                                7: value['total']['throughCount']
                            });
                        })
                        $('#getCount').html(count)
                        count += iterator
                        if (count < allCount) {
                            $(document).ready(function () {
                                setTimeout(() => {
                                    getNextItems(recordId, table, count, iterator, allCount)
                                }, 1000)
                            })
                        } else {
                            $(document).ready(function () {
                                table.draw()
                                $('#preloaderBlock').hide(300);
                                $('#though-block').show()

                                $('.sticky').click(function () {
                                    $('.render-child').remove()
                                });
                            })
                        }
                    },
                });
            }

            setInterval(() => {
                $('.show-more').unbind().on('click', function () {
                    let tr = $(this).parent().parent()
                    let target = $(this).attr('data-target')
                    $("tr[data-target='" + target + "']").remove();
                    $.each(totalResults[target], function (key, value) {
                        let childRows = ''

                        $.each(value['throughLinks'], function (key2, value2) {
                            childRows +=
                                '<tr>' +
                                '   <td>' + key2 + '</td>' +
                                '   <td>' + value2 + '</td>' +
                                '</tr>'
                        })

                        let childTable =
                            '<table class="child-table">' +
                            '   <thead>' +
                            '       <tr>' +
                            '           <th class="col-9">Ссылка</th>' +
                            '           <th class="col-3">Кол-во вхождений</th>' +
                            '       </tr>' +
                            '   </thead>' +
                            '   <tbody>' +
                            childRows +
                            '   </tbody>' +
                            '</table>'
                        if (key !== 'total') {
                            tr.after(
                                '<tr class="render-child" data-target="' + target + '">' +
                                '   <td class="remove-child" data-target="' + target + '"> <i class="fa fa-minus" ></i></td>' +
                                '   <td>' + key + '</td>' +
                                '   <td>' +
                                '       <a data-toggle="collapse" href="#childTable' + key + '" role="button" aria-expanded="false" aria-controls="childTable' + key + '">' +
                                '           Посмотреть таблицу </a>' +
                                '       <div class="collapse" id="childTable' + key + '">' +
                                childTable +
                                '       </div>' +
                                '   </td>' +
                                '   <td>' + (value['tf']).toFixed(6) + '</td>' +
                                '   <td>' + (value['idf']).toFixed(6) + '</td>' +
                                '   <td>' + value['repeatInTextMainPage'] + '</td>' +
                                '   <td>' + value['repeatInLinkMainPage'] + '</td>' +
                                '   <td>' + value['throughCount'] + '</td>' +
                                '</tr>'
                            )
                        }
                    })
                    removeElems()
                })
            }, 100)
        </script>
    @endslot
@endcomponent
