@component('component.card', ['title' => __('Projects')])
@section('content')
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-condensed table-striped">
                <thead>
                <tr>
                    <td>id</td>
                    <td>access</td>
                    <td>icon</td>
                    <td>title</td>
                    <td>description</td>
                    <td>link</td>
                    <td>show</td>
                    <td style="width: 130px">action</td>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row->access_as_string }}</td>
                        <td>{!! $row->icon !!}</td>
                        <td>{{ $row->title }}</td>
                        <td class="w-50">{{ $row->description }}</td>
                        <td>{{ $row->link }}</td>
                        <td>{{ $row->show }}</td>
                        <td>
                            <a href="{{ route('main-projects.edit', $row->id)}}" class="btn btn-default mr-1" style="display: inline;">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form action="{{ route('main-projects.destroy', $row->id)}}" method="post" style="display: inline;">
                                @csrf @method('DELETE')
                                <button class="btn btn-default" type="submit">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <a href="{{ route('main-projects.create')}}" class="btn btn-secondary mb-5">{{ __('Create new') }}</a>
        </div>
    </div>
@endsection
@endcomponent
