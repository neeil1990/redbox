@component('component.card', ['title' => __('HTML editor')])
@section('content')
    @slot('css')
        <style>
            .fa {
                color: #adb5bd;
                font-weight: 600;
            }

            .fa:hover {
                color: #4b545c;
                cursor: pointer !important;
            }
        </style>
    @endslot
    <div class="d-flex mt-3 mb-3">
        <a class="btn btn-secondary mr-2" href="{{ route('create.project') }}">
            {{__('Create a project')}}
        </a>
        @if(count($projects) != 0)
            <a class="btn btn-default btn-flat" href="{{ route('create.description') }}">
                {{__('Add text to the project')}}
            </a>
        @endif
    </div>
    <div>
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover">
                    <tbody>
                    @foreach($projects as $project)
                        <div class="modal fade" id="remove-project-id-{{$project->id}}"
                             role="dialog"
                             aria-hidden="true">
                            <div class="modal-dialog w-25" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <p>{{__('Delete a project')}} {{$project->project_name}}</p>
                                        <p>{{__('Are you sure?')}}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="{{ route('delete.project', $project->id) }}" class="btn btn-secondary">
                                            {{__('Delete a project')}}
                                        </a>
                                        <button type="button" class="btn btn-default"
                                                data-dismiss="modal">{{__('Back')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <tr data-widget="expandable-table" aria-expanded="false">
                            <td class="d-flex justify-content-between">
                                <div class="w-75">
                                    @if(count($project->descriptions) != 0)
                                        <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                                    @else
                                        <i class="expandable-table-caret"></i>
                                    @endif
                                    {{$project->project_name}}
                                    <span class="short_project_description">
                                        {{$project->short_description}}
                                    </span>
                                </div>
                                <div>
                                    <a href="{{ route('edit.project', $project->id) }}"
                                       class="fa fa-edit mr-2">
                                    </a>
                                    <a class="fa fa-trash"
                                       data-toggle="modal"
                                       data-target="#remove-project-id-{{$project->id}}">
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr class="expandable-body d-none">
                            <td>
                                <div class="p-0">
                                    <table class="table table-hover">
                                        @foreach($project->descriptions as $description)
                                            <div class="modal fade"
                                                 id="remove-description-id-{{$description->id}}"
                                                 role="dialog"
                                                 aria-hidden="true">
                                                <div class="modal-dialog w-25" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-body">
                                                            <p>{{__('Delete a text')}}</p>
                                                            <p>{{__('Are you sure?')}}</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a href="{{ route('delete.description', $description->id) }}"
                                                               class="btn btn-secondary">
                                                                {{__('Delete a text')}}
                                                            </a>
                                                            <button type="button"
                                                                    class="btn btn-default"
                                                                    data-dismiss="modal">
                                                                {{__('Back')}}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <tbody>
                                            <tr data-widget="expandable-table" aria-expanded="false">
                                                <td class="d-flex justify-content-between pr-3 text-wrap">
                                                    <div class="w-50">
                                                        <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                                                        {{\Illuminate\Support\Str::words(\Illuminate\Support\Str::limit(strip_tags($description->description), 35), 4)}}
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('edit.description', $description->id) }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <i class="fa fa-trash mr-2 ml-2"
                                                           data-toggle="modal"
                                                           data-target="#remove-description-id-{{$description->id}}"></i>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="expandable-body d-none">
                                                <td>
                                                    <div class="p-0">
                                                        <table class="table table-hover">
                                                            <tbody>
                                                            <tr>
                                                                <td class="text-wrap">{!! $description->description !!}</td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        @endforeach
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@endcomponent
