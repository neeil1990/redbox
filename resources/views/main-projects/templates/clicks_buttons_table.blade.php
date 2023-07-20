<h3 class="mt-5">Клики по кнопкам</h3>
<table id="actionsTable" class="table table-striped no-footer border">
    <thead>
    <tr>
        <th>
            <label for="email">Почта пользователя</label>
            <input type="text" class="form form-control filter-input" name="email" id="email" data-index="0">
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
        <th>
            <label for="url">URL</label>
            <select name="url" id="filter-url" class="custom-select filter-input" data-index="2"></select>
        </th>
        @php($i = 3)
        @if(is_array($columns))
            @foreach($columns as $column)
                <th>
                    <label for="url">{{ __($column) }}</label>
                    <input type="text" class="form form-control filter-input" name="url" id="url" data-index="{{ $i }}">
                </th>
                @php($i++)
            @endforeach
        @endif
    </tr>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        @php($i = 3)
        @if(is_array($columns))
            @foreach($columns as $column)
                <th></th>
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
                                return row.".str_replace(' ', '_', $column)."
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
            lengthMenu: [10, 25, 50, 100, 1000, 2000, 3000, 5000],
            pageLength: 100,
            columns: columns,
            dom: 'lBfrtip',
            buttons: [
                'copy', 'csv', 'excel'
            ],
            language: {
                lengthMenu: "количество юзеров _MENU_",
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
                console.clear()
                let timeout

                $('.filter-input').unbind().on('input', function () {
                    clearTimeout(timeout)
                    timeout = setTimeout(() => {
                        table.column($(this).attr('data-index')).search($(this).val()).draw();
                    }, 500)
                });

                $('#actionsTable_info').hide()
                $('#actionsTable_filter').hide()

                for (let i = 4; i <= {{ $i }}; i++) {
                    let sum = 0;
                    $('#actionsTable tbody td:nth-child(' + i + ')').each(function () {
                        const value = parseFloat($(this).text());
                        if (!isNaN(value)) {
                            sum += value;
                        }
                    });


                    $('#actionsTable > thead > tr:nth-child(2) > th:nth-child(' + i + ')').html('Общее количество нажатий: ' + sum)
                }

                let links = []
                $.each($('#actionsTable > tbody > tr > td:nth-child(3)'), function () {
                    links.push($(this).children('a').eq(0).text())
                })
                let uniqueLinks = [...new Set(links)];

                console.log(uniqueLinks)
                $.each(uniqueLinks, function (key, value) {
                    $('#filter-url').append('<option value="' + value + '">' + value + '</option>')
                })
            }
        })

        $('#actionsTable').wrap('<div style="width:100%; overflow: auto"></div>')
    })
</script>
