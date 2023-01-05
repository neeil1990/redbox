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
            <div class="toast-message">{{ __('Comment successfully changed') }}</div>
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
                       class="btn btn-secondary"
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
                <tr>
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
                        <div class="__helper-link ui_tooltip_w btn btn-default">
                            <span style="display: none" class="hidden-password">
                                {{ $password->password }}
                            </span>
                            <i aria-hidden="true" class="fa fa-clipboard"></i>
                            <span class="ui_tooltip __left __l">
                                <span class="ui_tooltip_content">{{ __('Copy to Clipboard') }}</span>
                            </span>
                        </div>
                        <div class="__helper-link ui_tooltip_w btn btn-default">
                            <i aria-hidden="true" class="fa fa-trash"></i>
                            <span class="ui_tooltip __left __l">
                                <span class="ui_tooltip_content">{{ __('Remove') }}</span>
                            </span>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @slot('js')
        <script src="{{ asset('plugins/password-generator/js/password-generator.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script>
            $(document).ready(function () {
                $('#me-passwords-table').dataTable({
                    pageLength: 10,
                })
            })

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
        </script>
    @endslot
@endcomponent
