<li class="nav-item">
    <a class="nav-link" href="{{ route('balance.index') }}" role="button">
        {{ __('Your balance') }}: {{ $user->balance }}
    </a>
</li>

@if($name)
    <li class="nav-item">
        <a class="nav-link" href="{{ route('tariff.index') }}" role="button">
            {{ __('Your tariff') }}: {{ $name }}
        </a>
    </li>
@endif

<li class="nav-item">
    <a class="nav-link" href="{{ route('profile.limits') }}" role="button">
        {{ __('Your limits') }}
    </a>
</li>

<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
