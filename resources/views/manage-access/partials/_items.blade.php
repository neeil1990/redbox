
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

        <i class="fas fa-external-link-alt" onclick="window.open('/{{ str_replace(' ', '-', strtolower($item->name)) }}', '_blank')" title="{{ __('Open') }}"></i>
        <i class="far fa-copy copy-item" data-copy="{{ $item->name }}" title="{{ __('Copied') }}"></i>

        <i class="fas fa-edit update-item" title="{{ __('Edit') }}"></i>
        <i class="fas fa-trash delete-item" title="{{ __('Delete') }}"></i>
    </div>
    @endif
</li>
