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
        </style>
    @endslot

    <div style='width: 100%; overflow-x: scroll; max-height:90vh;'>
        <table class="table table-bordered table-striped dtr-inline" id="though-table">
            <thead>
            <tr>
                <th class="sticky"></th>
                <th class="sticky">{{ __('Word') }}</th>
                <th class="sticky" style="z-index: 1003 !important; max-width: 350px">Пересечения</th>
                <th class="sticky">Сумма tf</th>
                <th class="sticky">Сумма idf</th>
                <th class="sticky">Сумма повторений в тексте посадочной страницы</th>
                <th class="sticky">Сумма повторений в ссылке посадочной страницы</th>
                <th class="sticky">кол-во вхождений</th>
            </tr>
            </thead>
            <tbody>
            @foreach(json_decode($though->result, true) as $key => $item)
                <tr>
                    <th class="show-more" data-target="{{ $key }}">
                        <i class="fa fa-plus"></i>
                    </th>
                    <td>{{ $key }}</td>
                    <td>
                        <table style="z-index: 100 !important; width: 400px">
                            <thead>
                            <tr>
                                <th class="col-8">Ссылка</th>
                                <th class="col-4">Кол-во вхождений</th>
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
                    </td>
                    <td>{{ $item['total']['tf'] }}</td>
                    <td>{{ $item['total']['idf'] }}</td>
                    <td>{{ $item['total']['repeatInTextMainPage'] }}</td>
                    <td>{{ $item['total']['repeatInLinkMainPage'] }}</td>
                    <td data-target="{{ $item['total']['throughCount'] }}">{{ $item['total']['throughCount'] }}
                        / {{ $item['total']['repeat'] }}</td>
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
            //  храним дочерние элементы в локалсторадже
            localStorage.setItem("{{ $microtime }}", "{{ $though->result }}")

            //  удаляем дочерние элементы из хранилища когда пользователь закрывает сайт/влкадку
            window.onbeforeunload = function () {
                localStorage.removeItem("{{ $microtime }}")
            };
        </script>
        <script>
            $(document).ready(function () {
                $('#though-table').DataTable({
                    "order": [[3, "desc"]],
                    "pageLength": 50,
                    "searching": true,
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel'
                    ]
                });

                $('.dt-button').addClass('btn btn-secondary')
            });

            $('.show-more').click(function () {

                let tr = $(this).parent()
                let words = JSON.parse(localStorage.getItem({{ $microtime }}).replace(/&quot;/g, '"'));
                let target = $(this).attr('data-target')
                $("tr[data-target='" + target + "']").remove();
                $.each(words[target], function (key, value) {
                    let childRows = ''

                    $.each(value['throughLinks'], function (key2, value2) {
                        childRows +=
                            '<tr>' +
                            '   <td>' + key2 + '</td>' +
                            '   <td>' + value2 + '</td>' +
                            '</tr>'
                    })

                    let childTable =
                        '<table>' +
                        '   <thead>' +
                        '       <tr>' +
                        '           <th>Ссылка</th>' +
                        '           <th>Кол-во вхождений</th>' +
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
                            '   <td>' + childTable + '</td>' +
                            '   <td>' + value['tf'] + '</td>' +
                            '   <td>' + value['idf'] + '</td>' +
                            '   <td>' + value['repeatInTextMainPage'] + '</td>' +
                            '   <td>' + value['repeatInLinkMainPage'] + '</td>' +
                            '   <td>' + value['throughCount'] + ' / ' + value['total'] + '</td>' +
                            '</tr>'
                        )
                    }
                })
                removeElems()
            })

            $('.sticky').click(function () {
                $('.render-child').remove()
            });

            function removeElems() {
                $('.remove-child').click(function () {
                    let target = $(this).attr('data-target')
                    $("tr[data-target='" + target + "']").remove();
                })
            }
        </script>
    @endslot
@endcomponent
