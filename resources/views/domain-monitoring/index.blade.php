@component('component.card', ['title' => __('Отслеживаемые домены')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
    @endslot
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <a href="{{ route('add.domain.monitoring.view') }}" class="btn btn-secondary mt-3 mb-3 mr-2">
        {{ __('Добавить отслеживаемый домен') }}
    </a>
    <div id="toast-container" class="toast-top-right success-message" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message">{{ __('Successfully changed') }}</div>
        </div>
    </div>
    <div id="toast-container" class="toast-top-right error-message" style="display:none;">
        <div class="toast toast-error" aria-live="assertive">
            <div class="toast-message error-msg">{{ __('The field must contain more than 0 characters') }}</div>
        </div>
    </div>
    <table id="example1" class="table table-bordered table-striped dataTable dtr-inline">
        <thead>
        <tr role="row">
            <th>{{ __('Project name') }}</th>
            <th>{{ __('Link') }}</th>
            <th>{{ __('Keyword') }}</th>
            <th class="col-2">{{ __('Frequency') }}</th>
            <th>{{ __('Status / Status code') }}</th>
            <th>{{ __('Uptime') }}</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($projects as $project)
            <div class="modal fade" id="remove-project-id-{{$project->id}}" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <p>{{__('Delete a project')}} {{$project->project_name}}</p>
                            <p>{{__('Are you sure?')}}</p>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('delete.domain.monitoring', $project->id) }}" class="btn btn-secondary">
                                {{__('Delete a project')}}
                            </a>
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Back')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <tr id="{{ $project->id }}">
                <td>
                    {!! Form::textarea('project_name', $project->project_name ,['class' => 'form-control monitoring', 'rows' => 2]) !!}
                </td>
                <td>
                    {!! Form::textarea('link', $project->link ,['class' => 'form-control monitoring', 'rows' => 2]) !!}
                </td>
                <td>
                    {!! Form::textarea('phrase', $project->phrase ,['class' => 'form-control monitoring', 'rows' => 2,'placeholder' => 'Если фраза не выбрана, то сервер будет ожидать 200 код ответа']) !!}</td>
                <td>
                    {!! Form::select('timing', [
                        '1' => __('once a minute'),
                        '5' => __('every 5 minutes'),
                        '10' => __('every 10 minutes'),
                        '15' => __('every 15 minutes'),
                        ], $project->timing , ['class' => 'form-control custom-select rounded-0 monitoring']) !!}
                </td>
                <td>
                    @if($project->broken)
                        <span class="text-danger">{{ $project->status }} / {{ $project->code }}</span>
                    @else
                        <span class="text-info">{{ $project->status }} / {{ $project->code }}</span>
                    @endif
                </td>
                <td>{{ $project->uptime_percent }}%
                </td>
                <td class="d-flex justify-content-around m-auto border-bottom-0 border-left-0 border-right-0">
                    <form action="{{ route('check.domain', $project->id)}}" method="get">
                        @csrf
                        <button class="btn btn-default" type="submit">
                            <i aria-hidden="true" class="fa fa-search"></i>
                        </button>
                    </form>
                    <button class="btn btn-default" data-toggle="modal"
                            data-target="#remove-project-id-{{$project->id}}">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if(!\Illuminate\Support\Facades\Auth::user()->telegram_bot_active)
        <span>
            {{ __('Want to') }}
                <a href="{{ route('profile.index') }}" target="_blank">
                    {{ __('receive notifications from our telegram bot') }}
                </a> ?
            </span>
    @endif
    @slot('js')
        <script>
            var oldValue = ''
            var oldProjectName = ''
            $(".monitoring").focus(function () {
                oldValue = $(this).val()
            })
            $(".monitoring").blur(function () {
                if (oldValue !== $(this).val() || $(this).attr('name') === 'phrase' && oldValue !== $(this).val()) {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('edit.domain') }}",
                        data: {
                            id: $(this).parent().parent().attr("id"),
                            name: $(this).attr('name'),
                            option: $(this).val(),
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            $('.toast-top-right.success-message').show(300)
                            setTimeout(() => {
                                $('.toast-top-right.success-message').hide(300)
                            }, 4000)
                        },
                        error: function () {
                            $('.toast-top-right.error-message').show()
                            setTimeout(() => {
                                $('.toast-top-right.error-message').hide(300)
                            }, 4000)
                        }
                    });
                }
            });
        </script>
    @endslot
@endcomponent
