<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active px-4 py-3 position-relative" data-toggle="pill" href="#yandex" role="tab" aria-selected="true">
                            <i class="fab fa-yandex fa-lg"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 position-relative" data-toggle="pill" href="#google" role="tab" aria-selected="false">
                            <i class="fab fa-google"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="yandex" role="tabpanel">
                        @include('monitoring.partials.stepper.inputs.regions.yandex')
                    </div>
                    <div class="tab-pane fade" id="google" role="tabpanel">
                        @include('monitoring.partials.stepper.inputs.regions.google')
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
