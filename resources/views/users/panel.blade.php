<div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="image" style="padding-left: 0.4rem !important;">
        <img src="{{ $user->image }}" class="img-circle elevation-2" alt="{{ $user->fullName }}">
    </div>
    <div class="info d-flex justify-content-center align-items-center">
        <a href="{{ route('profile.index') }}" class="d-block">{{ $user->fullName }}</a>
    </div>
</div>
