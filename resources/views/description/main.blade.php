@role('admin')
    <a href="{{ route('description.edit', $code) }}" class="btn btn-secondary mb-4">{{ __('Add description') }}</a>
@endrole

@if($description)
    <div class="card card-widget">
        <div class="card-header">
            <div class="user-block">
                <img class="img-circle" src="/html/dist/img/user1-128x128.jpg" alt="User Image">
                <span class="username"><a href="#">{{ $description->user->name }} {{ $description->user->last_name }}</a></span>
                <span class="description">{{ __('Publicly') }} - {{ $description->updated_at->diffForHumans() }}</span>
            </div>
            <!-- /.user-block -->
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">{!! $description->description !!}</div>
    </div>
@endif
