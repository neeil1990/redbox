@component('component.card', ['title' => __('Tracking the domain registration period')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/domain-information/css/domain-information.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>

        <style>
            .DomainInformation {
                background: oldlace;
            }

            .dataTables_length > label {
                display: flex;
            }

            .dataTables_length > label > select {
                margin: 0 5px !important;
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
            <div
                class="toast-message error-msg">{{ __('The field cannot be empty') }}</div>
        </div>
    </div>
    <div id="toast-container" class="toast-top-right delete-error-message" style="display:none;">
        <div class="toast toast-error" aria-live="assertive">
            <div class="toast-message error-msg">{{ __('You need to select the projects you want to delete') }}</div>
        </div>
    </div>
    <a href="{{ route('add.domain.information.view') }}" class="btn btn-secondary mt-3 mb-3 mr-2">
        {{ __('Add tracking the registration period') }}
    </a>
    <a href="#" class="btn btn-default mt-3 mb-3 mr-2" id="selectedProjects">
        {{ __('Delete selected projects') }}
    </a>
    <input type="hidden" class="checked-projects">
    <div>{{ __('Count tracked projects') }}: <span id="count-projects">{{ $countProjects }}</span></div>
    <table id="table" class="table table-bordered table-striped dataTable dtr-inline">
        <thead>
        <tr>
            <th></th>
            <th class="col-3">{{ __('Domain') }}</th>
            <th class="col-1">{{ __('Track DNS changes') }}</th>
            <th class="col-1">{{ __('Track the registration period') }}</th>
            <th class="col-2">{{ __('Last check') }}</th>
            <th class="col-4">{{ __('Domain information') }}</th>
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
                            <a href="{{ route('delete.domain.information', $project->id) }}" class="btn btn-secondary">
                                {{__('Delete a domain')}}
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
                <td data-order="{{ $project->domain }}">
                    {!! Form::text('domain', $project->domain ,['class' => 'form-control information', 'rows' => 2, 'data-order' => $project->link]) !!}
                </td>
                <td data-order="{{ $project->check_dns }}">
                    <div class="__helper-link ui_tooltip_w check-dns">
                        <div
                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success d-flex justify-content-center">
                            <input type="checkbox"
                                   class="custom-control-input check-dns"
                                   @if($project->check_dns) checked @endif
                                   id="customSwitchDNS{{$project->id}}">
                            <label class="custom-control-label" for="customSwitchDNS{{$project->id}}"></label>
                        </div>
                        <span class="ui_tooltip __left __l">
                            <span class="ui_tooltip_content" style="width: 250px !important;">
                                {{__('Green - you will receive a notification about the DNS status change')}}
                                <br>
                                {{__('Red - you will not receive notifications')}}
                            </span>
                        </span>
                    </div>
                </td>
                <td data-order="{{ $project->check_registration_date }}">
                    <div class="__helper-link ui_tooltip_w check-registration-date">
                        <div
                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success d-flex justify-content-center">
                            <input type="checkbox"
                                   class="custom-control-input check-registration-date"
                                   @if($project->check_registration_date) checked @endif
                                   id="customSwitchDate{{$project->id}}">
                            <label class="custom-control-label" for="customSwitchDate{{$project->id}}"></label>
                        </div>
                        <span class="ui_tooltip __left __l">
                            <span class="ui_tooltip_content" style="width: 250px !important;">
                                {{__('Green - you will receive notifications when the domain registration expiration time is less than 10 days.')}}
                                <br>
                                {{__('Red - you will not receive notifications')}}
                            </span>
                        </span>
                    </div>
                </td>
                <td>
                    {{ $project->last_check }}
                </td>
                @if($project->broken)
                    <td data-order="{{ $project->domain_information }}">
                        <pre class="text-danger">{{ $project->domain_information }}</pre>
                    </td>
                @else
                    <td data-order="{{ $project->domain_information }}">
                        <pre class="text-info">{{ $project->domain_information }}</pre>
                    </td>
                @endif
                <td>
                    <form action="{{ route('check.domain.information', $project->id)}}" method="get"
                          class="__helper-link ui_tooltip_w  d-inline">
                        @csrf
                        <button class="btn btn-default __helper-link ui_tooltip_w" type="submit">
                            <i aria-hidden="true" class="fa fa-search"></i>
                            <span class="ui_tooltip __left __l">
                            <span class="ui_tooltip_content" style="width: 250px !important;">
                                {{__('Run the check manually')}}
                            </span>
                        </span>
                        </button>
                    </form>
                    <button class="btn btn-default __helper-link ui_tooltip_w d-inline" data-toggle="modal"
                            data-target="#remove-project-id-{{$project->id}}">
                        <i class="fa fa-trash"></i>
                        <span class="ui_tooltip __left __l">
                            <span class="ui_tooltip_content" style="width: 250px !important;">
                                {{__('Delete a domain')}}
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
        <script src="{{ asset('plugins/common/js/common.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatable`s/buttons/jszip.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/vfs_fonts.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/html5.min.js') }}"></script>
        <script defer>
            let oldValue = ''
            let oldProjectName = ''
            $('input.check-dns').click(function () {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('edit.domain.information') }}",
                    data: {
                        id: $(this).parent().parent().parent().parent().attr('id'),
                        name: 'check_dns',
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
            $('input.check-registration-date').click(function () {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('edit.domain.information') }}",
                    data: {
                        id: $(this).parent().parent().parent().parent().attr('id'),
                        name: 'check_registration_date',
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
            $(".information").focus(function () {
                oldValue = $(this).val()
            })
            $(".information").blur(function () {
                if (oldValue !== $(this).val()) {
                    var obj = $(this)
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('edit.domain.information') }}",
                        data: {
                            id: $(this).parent().parent().attr("id"),
                            name: $(this).attr('name'),
                            option: $(this).val(),
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            obj.val(response['message'])
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
                    url: "{{ route('delete.domain-information') }}",
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
                        console.log($('#count-projects').text())
                        if ($('#count-projects').text() == 0) {
                            window.location.replace('https://lk.redbox.su/add-domain-information');
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
        </script>
        <script defer src="{{ asset('plugins/domain-information/js/domain-information.js') }}"></script>
    @endslot
@endcomponent
