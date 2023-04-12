@component('component.card', ['title' =>  'Статистика модуля "' . __($project->title) . '"'])
    @if(count($result) > 0)
        @slot('css')
            <!-- Toastr -->
            <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
            <!-- DataTables -->
            <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
            <link rel="stylesheet"
                  href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
            <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
            <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>

            <!-- daterange picker -->
            <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">

            <style>
                .exist-position {
                    color: #28a745 !important;
                    font-weight: bold;
                }

                #actions-table_wrapper {
                    width: 75%;
                }
            </style>
        @endslot

        <table id="table" class="table table-striped no-footer border">
            <thead>
            <tr>
                <th>Дата</th>
                <th>
                    Количество обновлений страницы
                    <span class="__helper-link ui_tooltip_w" style="font-weight: normal">
                        <i class="fa fa-question-circle" style="color: grey"></i>
                        <span class="ui_tooltip __bottom">
                            <span class="ui_tooltip_content" style="width: 400px">
                                Учитывается переход на страницу и её обновление
                            </span>
                        </span>
                    </span>
                </th>
                <th>
                    Количество действий
                    <span class="__helper-link ui_tooltip_w" style="font-weight: normal">
                        <i class="fa fa-question-circle" style="color: grey"></i>
                        <span class="ui_tooltip __bottom">
                            <span class="ui_tooltip_content" style="width: 400px">
                                Учитывается нажатие кнопок для получения дополнительной инфомрации из бд и т.п.
                            </span>
                        </span>
                    </span>
                </th>
                <th>Всего действий</th>
                <th>Пользователи</th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $date => $info)
                <tr>
                    <td data-order="{{ strtotime($date) }}">{{ $date }}</td>
                    <td>{{ $info['refreshPageCounter'] }}</td>
                    <td>{{ $info['actionsCounter'] }}</td>
                    <td>{{ $info['refreshPageCounter'] + $info['actionsCounter'] }}</td>
                    <td>
                        <button class="btn btn-default" type="button" data-toggle="collapse"
                                data-target="#collapseExample{{ $date }}" aria-expanded="false"
                                aria-controls="collapseExample{{ $date }}">
                            Пользователи
                        </button>

                        <div class="collapse mt-3" id="collapseExample{{ $date }}">
                            <table class="table table-striped no-footer border">
                                <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Обновления страницы</th>
                                    <th>Другие действия</th>
                                    <th>Всего действий</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($info['users'] as $user)
                                    <tr>
                                        <td>{{ $user['email'] }}</td>
                                        <td>{{ $user['refreshPageCounter'] }}</td>
                                        <td>{{ $user['actionsCounter'] }}</td>
                                        <td>{{ $user['refreshPageCounter'] + $user['actionsCounter'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @slot('js')
            <!-- datatables -->
            <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>

            <script src="{{ asset('plugins/datatables-buttons/js/buttons.excel.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.js') }}"></script>
            <script>
                $(document).ready(function () {
                    $('#table').DataTable({
                        "order": [[0, 'desc']],
                        lengthMenu: [10, 25, 50, 100],
                        pageLength: 10,
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
                })
            </script>
        @endslot
    @else
        Нет данных
    @endif
@endcomponent
