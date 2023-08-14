@component('component.card', ['title' => __('Projects')])
    @slot('css')
        <style>
            table tr > th,
            table tr > td {
                padding: 10px;
            }
        </style>
    @endslot()

    <div class="btn-group mb-3">
        <button class="btn btn-secondary">
            <a href="{{ route('users.statistics') }}" class="text-white" target="_blank">{{ __('General statistics users') }}</a>
        </button>
        <button class="btn btn-secondary">
            <a href="{{ route('statistics.modules') }}" class="text-white" target="_blank">{{ __('General statistics modules') }}</a>
        </button>
    </div>
    <div>
        <table class="table-bordered table-striped">
            <thead>
            <tr>
                <th>№</th>
                <th>Позиция</th>
                <th>Уровни доступа</th>
                <th>icon</th>
                <th>Заголовок</th>
                <th>Описание</th>
                <th>Ссылка</th>
                <th>Показывать обычным пользователям</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row->position }}</td>
                    <td>{{ $row->access_as_string }}</td>
                    <td>{!! $row->icon !!}</td>
                    <td>{{ $row->title }}</td>
                    <td>{{ $row->description }}</td>
                    <td>
                        <a href="{{ $row->link }}" target="_blank">{{ $row->link }}</a>
                    </td>
                    <td>{{ $row->show ? __('Yes') : __('No') }}</td>
                    <td class="d-flex border-0">
                        <a href="{{ route('main-projects.edit', $row->id)}}" class="btn btn-default mr-1"
                           style="display: inline;">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button class="btn btn-default mr-1 remove-project" data-id="{{ $row->id }}">
                            <i class="fa fa-trash"></i>
                        </button>
                        @if(isset($row->controller))
                            <a href="{{ route('main-projects.statistics', $row->id)}}" class="btn btn-default"
                               style="display: inline;">
                                Статистика
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <a href="{{ route('main-projects.create')}}" class="btn btn-secondary mt-3 mb-5">{{ __('Create new') }}</a>
    </div>

    @slot('js')
        <script>
            $('.remove-project').on('click', function () {
                let id = $(this).attr('data-id')
                let parent = $(this).parents().eq(1)
                let bool = confirm('Вы дейсвительно хотите удалить проект?')

                if (bool) {
                    let url = 'main-projects/' + id

                    $.ajax({
                        type: 'DELETE',
                        url: url,
                        success: function () {
                            parent.remove()
                        }
                    })
                }
            })
        </script>
    @endslot
@endcomponent
