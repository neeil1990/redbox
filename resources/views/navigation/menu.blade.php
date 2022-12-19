<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link" id="show-and-hide" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li>
        <a class="nav-link" href="{{ route('news') }}">
            <span>{{ __('News and updates') }}</span>
            <span class="badge badge-warning navbar-badge news" style="right: 0 !important;">{{ $count }}</span>
        </a>
    </li>
    <li>
        <a class="nav-link" href="mailto:sv@prime-ltd.su">
            <span>{{ __('Support') }}</span>
            <i class="fas fa-headset"></i>
        </a>
    </li>
</ul>
