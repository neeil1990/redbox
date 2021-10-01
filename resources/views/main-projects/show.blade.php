@component('component.card', ['title' => __('Project')])
@section('content')
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-condensed table-striped">
                <thead>
                <th>ID</th>
                <th>NAME</th>
                <th>EMAIL</th>
                <th>PHONE</th>
                <th>ACTION</th>
                </thead>
                <tbody>
                @foreach($data as $row)
                    <tr>
                        <td>{{$row->id }}</td>
                        <td>{{$row->tittle }}</td>
                        <td>{{$row->description }}</td>
                        <td>{{$row->link }}</td>
                        <td>
                            <a href="{{ route('contacts.edit', $row->id)}}" class="btn btn-primary">Edit</a>
                            <form action="{{ route('contacts.destroy', $row->id)}}" method="post">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@endcomponent
