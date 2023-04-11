@component('component.card', ['title' => __('Projects')])
    @slot('css')
        <style>
            table tr > th,
            table tr > td {
                padding: 10px;
            }
        </style>
    @endslot()
    @section('content')
        <div>
            <table class="table-bordered table-striped">
                <thead>
                <tr>
                    <th>id</th>
                    <th>Позиция</th>
                    <th>Уровни доступа</th>
                    <th>icon</th>
                    <th>Заголовок</th>
                    <th>Описание</th>
                    <th>Контроллер</th>
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
                        <td>{{ $row->controller }}</td>
                        <td>{{ $row->link }}</td>
                        <td>{{ $row->show ? __('Yes') : __('No') }}</td>
                        <td class="d-flex border-0">
                            <a href="{{ route('main-projects.edit', $row->id)}}" class="btn btn-default mr-1"
                               style="display: inline;">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form action="{{ route('main-projects.destroy', $row->id)}}" method="post"
                                  style="display: inline;">
                                @csrf @method('DELETE')
                                <button class="btn btn-default mr-1" type="submit">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
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
    @endsection
@endcomponent
