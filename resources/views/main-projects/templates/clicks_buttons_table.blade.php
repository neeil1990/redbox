<h3 class="mt-5">Клики по кнопкам</h3>
<table id="actionsTable" class="table table-striped no-footer border">
    <thead>
    <tr>
        <th>
            <label for="email">Почта пользователя</label>
            <input type="text" class="form form-control filter-input" name="email" id="email" data-index="0"
                   placeholder="email">
        </th>
        <th class="col-2">
            <label for="role">Тарифы</label>
            <select name="role" id="role" class="custom-select filter-input" data-index="1">
                <option value="Любой">Любой</option>
                <option value="Maximum">Максимальный</option>
                <option value="Ultimate">Ultimate</option>
                <option value="Optimal">Optimal</option>
                <option value="Free">Free</option>
            </select>
        </th>
        <th data-index="2"></th>
        @php($i = 3)
        @if(is_array($columns))
            @foreach($columns as $column)
                <th data-index="{{ $i }}"></th>
                @php($i++)
            @endforeach
        @endif
    </tr>
    <tr>
        <th data-index="0" class="col-2">Пользователь</th>
        <th data-index="1">Роли пользователя</th>
        <th data-index="2">URL</th>
        @php($i = 3)
        @if(is_array($columns))
            @foreach($columns as $column)
                <th data-index="{{ $i }}">{{ __($column) }}</th>
                @php($i++)
            @endforeach
        @endif
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
    $(document).ready(function () {
        let columns = [
            {
                name: 'email',
                data: 'email',
            },
            {
                name: 'roles',
                data: function (row) {
                    let content = ''

                    $.each(row.roles, function (k, role) {
                        content += `<div>${role.name}</div>`
                    })

                    return content;
                }
            },
            {
                name: 'url',
                data: function (row) {
                    return `<a class="nav-link" href="${row.url}" target="_blank">${row.url}</a>`
                }
            },
            @if(is_array($columns))
                @foreach($columns as $column)
                {!!  "{
                        name: \"$column\",
                        data: function(row) {
                            if(row.".str_replace(' ', '_', $column)." != undefined) {
                                return row.".str_replace(' ', '_', $column).".button_counter
                            }

                            return 0;
                        },
                    },"
                 !!}
                @endforeach
            @endif()
        ]

        let table = $('#actionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "/get-click-actions/{{ $id }}",
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            columns: columns,
            dom: 'lBfrtip',
            buttons: [
                'copy', 'csv', 'excel'
            ],
            columnDefs: [
                { orderable: false, targets: '_all' },
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
                emptyTable: "{{ __('No records') }}"
            },
            drawCallback: function () {
                let timeout

                $('.filter-input').unbind().on('input', function () {
                    clearTimeout(timeout)
                    timeout = setTimeout(() => {
                        table.column($(this).attr('data-index')).search($(this).val()).draw();
                    }, 500)
                });
            }
        })

        $('#actionsTable').wrap('<div style="width:100%; overflow: auto"></div>')
    })
</script>
