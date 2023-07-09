<h3 class="mt-5">Клики по кнопкам</h3>
<table id="actionsTable" class="table table-striped no-footer border">
    <thead>
    <tr>
        <th>
            <label for="email">Почта пользователя</label>
            <input type="text" class="form form-control filter-input" name="email" id="email" data-index="0"
                   placeholder="email">
        </th>
        <th>
            <label for="role">Тарифы</label>
            <select name="role" id="role" class="custom-select filter-input" data-index="1">
                <option value="Любой">Любой</option>
                <option value="Maximum">Максимальный</option>
                <option value="Ultimate">Ultimate</option>
                <option value="Optimal">Optimal</option>
                <option value="Free">Free</option>
            </select>
        </th>
        <th></th>
        @foreach($columns as $column)
            <th></th>
        @endforeach
    </tr>
    <tr>
        <th>Пользователь</th>
        <th>Роли пользователя</th>
        <th>URL</th>
        @foreach($columns as $column)
            <th>{{ __($column) }}</th>
        @endforeach
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
            drawCallback: function () {
                $('.filter-input').unbind().on('input', function () {
                    let timeout
                    clearTimeout(timeout)
                    timeout = setTimeout(() => {
                        table.column($(this).attr('data-index')).search($(this).val()).draw();
                    }, 500)
                });
            }
        })
    })
</script>
