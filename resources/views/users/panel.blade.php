<div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="image" style="padding-left: 0.4rem !important;">
        @if(isset($user->image))
            <img src="{{ Storage::url($user->image) }}" class="img-circle elevation-2" alt="{{ $user->name }}">
        @else
            <img src="img\profile-image.jpg" class="img-circle elevation-2" alt="avatar">
        @endif
    </div>
    <div class="info d-flex justify-content-center align-items-center">
        <a href="{{ route('profile.index') }}" class="d-block">{{ $user->name }} {{ $user->last_name }}</a>
    </div>
</div>
