@component('component.card', ['title' => __('Monitored domains')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/site-monitoring/css/site-monitoring.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/site-monitoring/css/site-monitoring.css') }}"/>
        <style>
            .domainMonitoringProject {
                background: oldlace;
            }
        </style>
    @endslot
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div id="toast-container" class="toast-top-right success-message" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message">{{ __('Successfully changed') }}</div>
        </div>
    </div>
    <div id="toast-container" class="toast-top-right delete-success-message" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message">{{ __('Successfully deleted') }}</div>
        </div>
    </div>
    <div id="toast-container" class="toast-top-right error-message" style="display:none;">
        <div class="toast toast-error" aria-live="assertive">
            <div class="toast-message error-msg">{{ __('The field must contain more than 0 characters') }}</div>
        </div>
    </div>
    <div id="toast-container" class="toast-top-right delete-error-message" style="display:none;">
        <div class="toast toast-error" aria-live="assertive">
            <div class="toast-message error-msg">{{ __('You need to select the projects you want to delete') }}</div>
        </div>
    </div>
    <a href="{{ route('add.site.monitoring.view') }}" class="btn btn-secondary mt-3 mb-3 mr-2">
        {{ __('Add a monitored domain') }}
    </a>
    <a href="#" class="btn btn-default mt-3 mb-3 mr-2" id="selectedProjects">
        {{ __('Delete selected projects') }}
    </a>
    <input type="hidden" class="checked-projects">
    <div>{{ __('Count tracked projects') }}: <span id="count-projects">{{ $countProjects }}</span></div>
    <table id="example" class="table table-bordered table-striped dataTable dtr-inline">
        <thead>
        <tr>
            <th></th>
            <th class="col-2">{{ __('Project name') }} <i class="fa fa-sort"></i></th>
            <th class="col-2">{{ __('Link') }} <i class="fa fa-sort"></i></th>
            <th class="col-2">{{ __('Keyword') }} <i class="fa fa-sort"></i></th>
            <th class="col-1">{{ __('Frequency every') }} <i class="fa fa-sort"></i></th>
            <th class="col-1">{{ __('Response waiting time') }} <i class="fa fa-sort"></i></th>
            <th class="col-2">{{ __('Status') }}<i class="fa fa-sort"></i>
            </th>
            <th>{{ __('Receive notifications?') }}</th>
            <th class="col-1"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($projects as $project)
            <div class="modal fade" id="remove-project-id-{{$project->id}}" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <p>{{__('Delete a project')}} {{ $project->project_name }}</p>
                            <p>{{__('Are you sure?')}}</p>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('delete.site.monitoring', $project->id) }}" class="btn btn-secondary">
                                {{__('Delete a project')}}
                            </a>
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Back')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <tr id="{{ $project->id }}">
                <td>
                    <div class="custom-control custom-checkbox checbox-for-remove-project">
                        <input type="checkbox" id="project-{{ $project->id }}" class="checkbox custom-control-input"
                               name="enums">
                        <label for="project-{{ $project->id }}" class="custom-control-label">
                        </label>
                    </div>
                </td>
                <td>
                    {!! Form::textarea('project_name', __($project->project_name) ,['class' => 'form-control monitoring', 'rows' => 2, 'data-order' => $project->project_name]) !!}
                </td>
                <td>
                    {!! Form::textarea('link', __($project->link) ,['class' => 'form-control monitoring', 'rows' => 2, 'data-order' => $project->link]) !!}
                </td>
                <td>
                    {!! Form::textarea('phrase', __($project->phrase) ,['class' => 'form-control monitoring', 'rows' => 2,'placeholder' => __('If the phrase is not selected, the server will wait for the 200 response code'), 'data-order' => $project->phrase]) !!}</td>
                <td data-order="{{ $project->timing }}">
                    {!! Form::select('timing', [
                    '5' => __('5 minutes'),
                    '10' => __('10 minutes'),
                    '15' => __('15 minutes'),
                    '20' => __('20 minutes'),
                    '30' => __('30 minutes'),
                    '60' => __('60 minutes')],
                     $project->timing,
                     ['class' => 'form-control custom-select rounded-0 monitoring']) !!}
                </td>
                <td data-order="{{ $project->waiting_time }}">
                    {!! Form::select('waiting_time', [
                    '10' => '10 ' . __("sec"),
                    '15' => '15 ' . __("sec"),
                    '20' => '20 ' . __("sec")
                    ], $project->waiting_time, ['class' => 'form-control custom-select rounded-0 monitoring']) !!}
                </td>
                <td data-order="{{ $project->broken }}">
                    @isset($project->code)
                        @if($project->broken)
                            <span class="text-danger">
                            {{ __($project->status) }} <br>
                            {{ __('http code') }} {{ __($project->code) }} <br>
                            {{ __('Uptime') }} {{ $project->uptime_percent }}%
                        </span>
                        @else
                            <span class="text-info">
                            {{ __($project->status) }} <br>
                            {{ __('http code') }}: {{ __($project->code) }} <br>
                            {{ __('Uptime') }}: {{ $project->uptime_percent }}%
                        </span>
                        @endif
                    @endisset
                </td>
                <td data-order="{{ $project->send_notification }}">
                    <div class="__helper-link ui_tooltip_w send-notification-switch">
                        <div
                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success d-flex justify-content-center">
                            <input type="checkbox"
                                   class="custom-control-input send-notification-switch"
                                   @if($project->send_notification) checked @endif
                                   id="customSwitch{{$project->id}}">
                            <label class="custom-control-label" for="customSwitch{{$project->id}}"></label>
                        </div>
                    </div>
                </td>
                <td>

                    <button class="btn btn-default __helper-link ui_tooltip_w check" type="submit"
                            data-target="{{ $project->id }}">
                        <i aria-hidden="true" class="fa fa-search"></i>
                        <span class="ui_tooltip __left __l">
                            <span class="ui_tooltip_content" style="width: 250px !important;">
                                {{__('Run the check manually')}}
                            </span>
                        </span>
                    </button>
                    <button class="btn btn-default __helper-link ui_tooltip_w d-inline" data-toggle="modal"
                            data-target="#remove-project-id-{{$project->id}}">
                        <i class="fa fa-trash"></i>
                        <span class="ui_tooltip __left __l">
                            <span class="ui_tooltip_content" style="width: 250px !important;">
                                {{__('Delete a project')}}
                            </span>
                        </span>
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
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script defer>
            $(document).ready(function () {
                $('#example').DataTable();
            });
            var oldValue = ''
            var oldProjectName = ''
            $('input.send-notification-switch').click(function () {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('edit.domain') }}",
                    data: {
                        id: $(this).parent().parent().parent().parent().attr('id'),
                        name: 'send_notification',
                        option: $(this).is(':checked') ? 1 : 0,
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
            })
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
            $('.checbox-for-remove-project').change(function () {
                let selectedId = $(this).children(':first-child').attr('id').substr(8)
                let text = $('.checked-projects').text();
                if ($(this).children(':first-child').is(':checked')) {
                    $(this).parent().parent().attr('data-select', true)
                    $('.checked-projects').text(text + selectedId + ', ')
                } else {
                    $(this).parent().parent().attr('data-select', false)
                    text = text.replace(selectedId + ', ', '')
                    $('.checked-projects').text(text)
                }
            })
            $('#selectedProjects').click(function () {
                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: "{{ route('delete.sites.monitoring') }}",
                    data: {
                        ids: $('.checked-projects').text(),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        let iterator = 0;
                        $('[data-select=true]').each(function () {
                            iterator++
                            $(this).remove();
                        })
                        $('#count-projects').text($('#count-projects').text() - iterator)
                        if ($('#count-projects').text() == 0) {
                            window.location.replace('https://lk.redbox.su/add-site-monitoring');
                        }
                        $('.toast-top-right.delete-success-message').show(300)
                        setTimeout(() => {
                            $('.toast-top-right.delete-success-message').hide(300)
                        }, 4000)
                    },
                    error: function () {
                        $('.toast-top-right.delete-error-message').show()
                        setTimeout(() => {
                            $('.toast-top-right.delete-error-message').hide(300)
                        }, 4000)
                    }
                });
            });

            $('.check').on('click', function () {
                let parentRow = $(this).parents().eq(1)
                $.ajax({
                    type: "POST",
                    url: "{{ route('check.domain') }}",
                    data: {
                        projectId: $(this).attr('data-target'),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        let content

                        if (response.broken) {
                            content = '<span class="text-danger">' +
                                '<div> ' + response.status + '</div>' +
                                '<div> http code: ' + response.code + '</div>' +
                                '<div> {{ __('Uptime') }} : ' + response.uptime + '</div>' +
                                '</span>'
                        } else {
                            content = '<span class="text-info">' +
                                '<div> ' + response.status + '</div>' +
                                '<div> http code: ' + response.code + '</div>' +
                                '<div> {{ __('Uptime') }} : ' + response.uptime + '</div>' +
                                '</span>'
                        }

                        parentRow.children('td').eq(6).html(content)
                    },
                });

            })
        </script>
        <script defer src="{{ asset('plugins/site-monitoring/js/localstorage.js') }}"></script>
    @endslot
@endcomponent
