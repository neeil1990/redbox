@component('component.card', ['title' => __('Projects')])

    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/summernote.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/summernote/style.css') }}"/>
        <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
    @endslot
    <div class="d-flex">
        <a class="btn btn-secondary mt-3 mb-3 mr-2" href="{{ route('create.project') }}">
            {{__('Create a project')}}
        </a>
        @if(count($projects) != 0)
            <a class="btn btn-default mt-3 mb-3" href="{{ route('create.description') }}">
                {{__('Add text to the project')}}
            </a>
        @endif
    </div>
    <div id="accordion">
        @foreach($projects as $project)
            <div class="modal fade" id="remove-project-id-{{$project->id}}"
                 tabindex="-1"
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
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Back')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-default card-outline">
                <div class="w-100 collapsed">
                    <div class="card-header">
                        <h4 class="card-title w-100 d-flex flex-column">
                            <span class="d-flex justify-content-between pb-2">
                                <a data-toggle="collapse" class="subject_project_name"
                                   href="#project-id-{{$project->id}}"
                                   aria-expanded="false">
                                {{ $project->project_name }}
                                </a>
                                <a data-toggle="collapse" href="#project-id-{{$project->id}}"
                                   aria-expanded="false">
                                <i class="fa fa-eye"></i>
                                </a>
                            </span>
                            <p class="short_description_project">
                                {{ $project->short_description }}
                            </p>
                        </h4>
                    </div>
                </div>
                <div id="project-id-{{$project->id}}" class="collapse" data-parent="#accordion" style="">
                    <div class="card-body">
                        @foreach($project->descriptions as $description)
                            <div class="accordion" id="accordion{{$description->id}}">
                                <div class="modal fade"
                                     id="remove-description-id-{{$description->id}}"
                                     tabindex="-1"
                                     role="dialog"
                                     aria-hidden="true">
                                    <div class="modal-dialog w-25" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <p>{{__('Delete a description')}}</p>
                                                <p>{{__('Are you sure?')}}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="{{ route('delete.description', $description->id) }}"
                                                   class="btn btn-secondary">
                                                    {{__('Delete a description')}}
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
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <div class="d-flex justify-content-between">
                                                <span class="font-weight-lighter pointer"
                                                      data-toggle="collapse"
                                                      data-target="#description-id-{{$description->id}}"
                                                      aria-expanded="false"
                                                      aria-controls="description-id-{{$description->id}}">
                                                    {{\Illuminate\Support\Str::limit(strip_tags($description->description),50)}}
                                                </span>
                                                <div>
                                                    <a href="{{ route('edit.description', $description->id) }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <i class="fa fa-trash mr-2 ml-2"
                                                       data-toggle="modal"
                                                       data-target="#remove-description-id-{{$description->id}}"></i>
                                                    <i class="fa fa-eye"
                                                       data-toggle="collapse"
                                                       data-target="#description-id-{{$description->id}}"
                                                       aria-expanded="false"
                                                       aria-controls="description-id-{{$description->id}}"></i>
                                                </div>
                                            </div>
                                        </h5>
                                    </div>

                                    <div id="description-id-{{$description->id}}" class="collapse"
                                         aria-labelledby="headingOne" data-parent="#accordion{{$description->id}}"
                                         style="">
                                        <div class="d-flex border-top">
                                            <div class="project_description mr-auto pl-4 pr-4">
                                                <p>{!! $description->description !!}</p>
                                            </div>
                                            <div class="d-flex align-items-start pt-4">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex collapse card-footer">
                        <a href="{{ route('edit.project', $project->id) }}"
                           class="mr-2 btn btn-secondary btn-flat">
                            {{__('Edit')}}
                        </a>
                        <button type="button"
                                class="mr-2 btn btn-default btn-flat"
                                data-toggle="modal"
                                data-target="#remove-project-id-{{$project->id}}">
                            {{__('Delete a project')}}
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @slot('js')
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/summernote-bs4.css') }}"></script>
    @endslot
@endcomponent
