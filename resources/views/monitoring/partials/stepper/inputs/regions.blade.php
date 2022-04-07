
<div class="card card-primary card-outline card-outline-tabs">
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active px-4 py-3 position-relative" data-toggle="pill" href="#yandex" role="tab" aria-selected="true">
                    <i class="fab fa-yandex fa-lg"></i>
                    <span class="badge badge-secondary navbar-badge d-none">0</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 position-relative" data-toggle="pill" href="#google" role="tab" aria-selected="false">
                    <i class="fab fa-google"></i>
                    <span class="badge badge-secondary navbar-badge d-none">0</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane fade active show" id="yandex" role="tabpanel">
                <div class="col-md-6">
                    @include('monitoring.partials.stepper.inputs.regions.yandex')
                </div>
            </div>
            <div class="tab-pane fade" id="google" role="tabpanel">
                <div class="col-md-6">
                    @include('monitoring.partials.stepper.inputs.regions.google')
                </div>
            </div>
        </div>
    </div>
    <!-- /.card -->
</div>
