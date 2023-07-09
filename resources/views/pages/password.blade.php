@component('component.card', ['title' => __('Password generator')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>

        <style>
            .PasswordGenerator {
                background: oldlace;
            }
        </style>
    @endslot
    <div id="toast-container" class="toast-top-right success-message" style="display: none">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message">{{ __('Success') }}</div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right error-message" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message error-message" id="toast-message">{{ __('Error') }}</div>
        </div>
    </div>

    <div class="password-generator">
        <div class="d-flex justify-content-between">
            <form class="col-6" action="{{ route('generate.password') }}" method="post">
                @csrf
                <div class="d-flex flex-column">
                    <h3>{{__('Generator settings')}}:</h3>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="checkbox1" class="checkbox custom-control-input" name="enums">
                        <label for="checkbox1" class="custom-control-label">
                            {{__('Enums')}}
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="checkbox2" class="checkbox custom-control-input" name="upperCase">
                        <label for="checkbox2" class="custom-control-label">
                            {{__('Upper case')}}
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="checkbox3" class="checkbox custom-control-input" name="lowerCase">
                        <label for="checkbox3" class="custom-control-label">
                            {{__('Lower case')}}
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="checkbox4" class="checkbox custom-control-input"
                               name="specialSymbols">
                        <label for="checkbox4" class="custom-control-label">
                            {{__('Special symbols')}} %, *, ), ?, @, #, $, ~
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="checkbox5" class="checkbox custom-control-input" name="savePassword">
                        <label for="checkbox5" class="custom-control-label">
                            {{__('Save password')}}?
                        </label>
                    </div>
                    <label>
                        {{__('Characters')}} :
                        <input type="number" class="number" name="countSymbols" value="6" max="50" min="1">
                    </label>
                </div>
                <input type="submit"
                       value="{{__('Generate password')}}"
                       class="btn btn-secondary click_tracking"
                       data-click="Generate password"
                       onclick="saveState()">
            </form>
            <div class="passwords col-6">
                @isset($passwords)
                    <h3>{{__('Generated passwords')}}: </h3>
                    @foreach($passwords as $password)
                        <p>{{ $password }}</p>
                    @endforeach
                @endisset
            </div>
        </div>
    </div>

    <div class="modal fade" id="removePasswordWindow" tabindex="-1" aria-labelledby="removePasswordWindowLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removePasswordWindowLabel">Подтвердите удаление</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button"
                            data-dismiss="modal"
                            id="success-remove-password"
                            class="btn btn-secondary">
                        {{ __('Remove') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="my-passwords mt-5">
        <h2>{{__('Your generated passwords')}}</h2>
        <table id="me-passwords-table" class="table table-bordered table-striped dataTable dtr-inline" role="grid"
               aria-describedby="example1_info">
            <thead>
            <tr role="row">
                <th>{{ __('Password') }}</th>
                <th>{{ __('Comment') }}</th>
                <th>{{ __('Created at') }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($user->passwords as $password)
                <tr id="tr-{{ $password->id }}">
                    <td class="align-baseline">{{ $password->password }}</td>
                    <td class="align-baseline">
                        <textarea class="form form-control password-comment" name="comment" id="{{$password->id}}"
                                  cols="5" rows="3">{{ $password->comment }}</textarea>
                    </td>
                    <td class="align-baseline col-2">
                        <div>
                            {{ $password->created_at }}
                        </div>
                    </td>
                    <td class="col-1 align-baseline">
                        <div class="__helper-link ui_tooltip_w btn btn-default click_tracking" data-click="Copy to Clipboard">
                            <span style="display: none" class="hidden-password">
                                {{ $password->password }}
                            </span>
                            <i aria-hidden="true" class="fa fa-clipboard"></i>
                            <span class="ui_tooltip __left __l">
                                <span class="ui_tooltip_content">{{ __('Copy to Clipboard') }}</span>
                            </span>
                        </div>
                        <button class="btn btn-default remove-password click_tracking" data-click="Remove" data-order="{{ $password->id }}"
                                data-toggle="modal" data-target="#removePasswordWindow">
                            <i aria-hidden="true" class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <input type="hidden" name="passwordId" id="passwordId">
    @slot('js')
        <script src="{{ asset('plugins/password-generator/js/password-generator.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script>
            $(document).ready(function () {
                $('.password-comment').change(function () {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('edit.password.comment') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: $(this).attr('id'),
                            comment: $(this).val(),
                        },
                        success: function (response) {
                            $('.success-message .toast-message').html("{{ __('Comment successfully changed') }}")
                            $('.toast-top-right.success-message').show(300)
                            setTimeout(() => {
                                $('.toast-top-right.success-message').hide(300)
                            }, 5000)
                        },
                        error: function () {
                            $('.toast-top-right.error-message').show(300)
                            setTimeout(() => {
                                $('.toast-top-right.error-message').hide(300)
                            }, 5000)
                        }
                    });
                })

                $('.__helper-link.ui_tooltip_w.btn.btn-default').click(function () {
                    let text = $(this).find(':first-child.hidden-password').text()
                    let textarea = document.createElement('textarea');
                    document.body.appendChild(textarea);
                    textarea.value = text.trim();
                    textarea.select();
                    document.execCommand("copy");
                    document.body.removeChild(textarea);

                    $('.success-message .toast-message').html("{{ __('Successfully copied') }}")
                    $('.success-message').show(300)
                    setTimeout(() => {
                        $('.success-message').hide(300)
                    }, 5000)
                });

                $('.remove-password').click(function () {
                    $('#passwordId').val($(this).attr('data-order'))
                })

                $('#success-remove-password').click(function () {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('remove.password') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: $('#passwordId').val(),
                        },
                        success: function (response) {
                            $('#tr-' + $('#passwordId').val()).remove()
                            $('.success-message .toast-message').html("{{ __('Successfully deleted') }}")
                            $('.success-message').show(300)
                            setTimeout(() => {
                                $('.success-message').hide(300)
                            }, 5000)
                        },
                        error: function () {

                        }
                    });
                })
            })
        </script>
    @endslot
@endcomponent
