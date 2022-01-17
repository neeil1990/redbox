
<li data-id="{{ $item->id }}" @if($id === 'role')class="root"@endif>

    @if($id === 'permission')
        <span class="handle">
          <i class="fas fa-ellipsis-v"></i>
          <i class="fas fa-ellipsis-v"></i>
        </span>
    @else
        <span>
            <i class="fas fa-user-tag"></i>
        </span>
    @endif

    <span class="text">{{ $item->name }}</span>

    @if(!in_array($item->name, ['admin', 'user', 'Super Admin']))
    <div class="tools">
        <i class="fas fa-edit update-item"></i>
        <i class="fas fa-trash delete-item"></i>
    </div>
    @endif
</li>
