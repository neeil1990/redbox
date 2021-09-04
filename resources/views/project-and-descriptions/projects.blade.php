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
                {{__('Add a description to the project')}}
            </a>
        @endif
    </div>
    <div>
        @if(\Illuminate\Support\Facades\Session::has('countError'))
            <div class="alert alert-default-danger col-lg-10 col-sm-12">
                {{ \Illuminate\Support\Facades\Session::get('countError') }}
            </div>
        @endif
        @foreach($projects as $project)
            <div class="your-projects col-lg-12 col-sm-12 pl-0 pr-0 ml-0 pt-3 card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex">
                        <div class="mr-5 project_name">
                            <p>{{ $project->project_name }}</p>
                            <p class="short_project_description">{!! $project->short_description !!}</p>
                        </div>
                    </div>
                    <div class="card-tools d-flex">
                        @if(count($project->descriptions) > 0)
                            <i class="fas fa-eye pr-0" data-toggle="collapse"
                               data-target="#project-id-{{$project->id}}"></i>
                        @endif
                    </div>
                </div>
                <div class="collapse"
                     id="project-id-{{$project->id}}">
                    @foreach($project->descriptions as $description)
                        <div class="d-flex mt-3 ml-3">
                            <div class="project_description col-10 mr-auto pl-0">
                                <p>{!! $description->description !!}</p>
                            </div>
                            <div class="d-flex col-1 align-items-start justify-content-end pr-1">
                                <a href="{{ route('edit.description', $description->id) }}" class="mr-2">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="{{ route('delete.description', $description->id) }}" class="mr-2">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex card-footer">
                    <a href="{{ route('edit.project', $project->id) }}"
                       class="mr-2 btn btn-secondary btn-flat">
                        {{__('Edit')}}
                    </a>
                    <a href="{{ route('delete.project', $project->id) }}"
                       class="mr-2 btn btn-default btn-flat">
                        {{__('Delete a project')}}
                    </a>
                </div>
            </div>
        @endforeach
    </div>
    @slot('js')
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/summernote-bs4.css') }}"></script>
    @endslot
@endcomponent
