@component('component.card', ['title' => __('Monitoring position')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    @endslot

    <div class="row">
        <div class="col-6">
            @include('monitoring.admin._btn')

            <form action="{{ route('monitoring-permissions.store') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Настройка прав</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div id="accordion">
                            @foreach ($roles as $role)
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h4 class="card-title w-100">
                                            <a class="d-block w-100" data-toggle="collapse" href="#collapse{{ $role->id }}" aria-expanded="true">
                                                {{ $role->title }}
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapse{{ $role->id }}" class="collapse @if ($loop->first) show @endif" data-parent="#accordion">
                                        <div class="card-body">
                                            @foreach ($permissions as $permission)
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" name="permissions[{{$role->name}}][{{$permission->name}}]" class="custom-control-input" id="{{ Str::slug(join([$role->name, $permission->name]), '-') }}" @if ($role->hasPermissionTo($permission)) checked @endif>
                                                        <label class="custom-control-label" for="{{ Str::slug(join([$role->name, $permission->name]), '-') }}">{{ $permission->title }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </form>
        </div>
    </div>

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
        <!-- Bootstrap 4 -->
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

        <script>
            toastr.options = {
                "timeOut": "1000"
            };

            $('input[type="checkbox"]').change(function () {
                let form = $(this).closest('form');
                let data = form.serialize();
                let action = form.attr('action');

                axios.post(action, data).then(function (response) {
                    if (response.data.status) {
                        toastr.success(response.data.message);
                    }
                });
            });

        </script>
    @endslot


@endcomponent
