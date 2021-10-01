@component('component.card', ['title' => __('Projects')])
@section('content')
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-condensed table-striped">
                <thead>
                <th>id</th>
                <th>title</th>
                <th>description</th>
                <th>link</th>
                <th>action</th>
                </thead>
                <tbody>
                @foreach($data as $row)
                    <tr>
                        <td>{{$row->id }}</td>
                        <td>{{$row->title }}</td>
                        <td>{{$row->description }}</td>
                        <td>{{$row->link }}</td>
                        <td>
                            <a href="{{ route('main-projects.edit', $row->id)}}" class="btn btn-default w-75">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form action="{{ route('main-projects.destroy', $row->id)}}" method="post">
                                @csrf @method('DELETE')
                                <button class="btn btn-default w-75" type="submit">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <a href="{{ route('main-projects.create')}}" class="btn btn-secondary">
                Create new
            </a>
        </div>
    </div>
@endsection
@endcomponent
