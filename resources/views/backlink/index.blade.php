@component('component.card', ['title' => __('Link tracking')])
@section('content')
    <a href="{{ route('add.backlink') }}" class="btn btn-secondary mt-3 mb-3 mr-2">
        Add link tracking
    </a>
    @foreach($tracking as $track)
        <div class="row">
            <div class="col-sm-12">
                <table id="example2" class="table table-bordered table-hover dataTable dtr-inline"
                       role="grid" aria-describedby="example2_info">
                    <thead>
                    <tr role="row">
                        <th>Project name</th>
                        <th>Live links/Total links</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="odd">
                        <td class="col-5">
                            <a href="{{ route('show.backlink', $track->id)}}">
                                {{ $track->project_name }}
                            </a>
                        </td>
                        <td class="col-5">
                            @if($totalBrokenLinks > 0)
                                <span class="text-danger">
                                    {{ $track->total_link - $totalBrokenLinks }}/{{ $track->total_link }}
                                </span>
                            @else
                                <span class="text-info">
                                    {{ $track->total_link - $totalBrokenLinks }}/{{ $track->total_link }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('delete.backlink', $track->id)}}"
                                  class="pt-1"
                                  method="post">
                                @csrf @method('DELETE')
                                <button class="btn btn-default" type="submit">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endforeach
@endsection
@endcomponent
