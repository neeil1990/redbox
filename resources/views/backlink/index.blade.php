@component('component.card', ['title' => __('My Projects')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
    @endslot
    <a href="{{ route('add.backlink.view') }}" class="btn btn-secondary mt-3 mb-3 mr-2">
        {{ __('Add link tracking') }}
    </a>
    @foreach($backlinks as $backlink)
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-bordered table-hover dataTable dtr-inline">
                    <thead>
                    <tr role="row">
                        <th>{{ __('Project name') }}</th>
                        <th>{{ __('Broken links/Total links') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="odd">
                        <td class="col-5">
                            <a href="{{ route('show.backlink', $backlink->id)}}">
                                {{ $backlink->project_name }}
                            </a>
                        </td>
                        <td class="col-5">
                            @if($backlink->total_broken_link != 0)
                                <span class="text-danger">
                                    {{ $backlink->total_broken_link }}/{{ $backlink->total_link }}
                                </span>
                            @else
                                <span class="text-info">
                                    {{ $backlink->total_broken_link }}/{{ $backlink->total_link }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('delete.backlink', $backlink->id)}}"
                                  class="d-flex justify-content-center"
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
@endcomponent
