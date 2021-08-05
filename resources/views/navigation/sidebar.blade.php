<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-header">{{ __('Menu') }}</li>
        <li class="nav-item">
            <a href="/" class="nav-link">
                <i class="nav-icon far fa-image"></i>
                <p>{{ __('Home') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link">
                <i class="nav-icon far fa-image"></i>
                <p>{{ __('Users') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pages.keyword') }}" class="nav-link">
                <i class="nav-icon far fa-image"></i>
                <p>{{ __('Keyword generator') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pages.duplicates') }}" class="nav-link">
                <i class="nav-icon far fa-image"></i>
                <p>{{ __('Remove Duplicates') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pages.utm') }}" class="nav-link">
                <i class="nav-icon far fa-image"></i>
                <p>{{ __('UTM Marks') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pages.roi') }}" class="nav-link">
                <i class="nav-icon far fa-image"></i>
                <p>{{ __('ROI calculator') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pages.headers') }}" class="nav-link">
                <i class="nav-icon far fa-image"></i>
                <p>{{ __('Http headers') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="https://docs.google.com/document/d/1TpjCQjXYV_ZWyxD-c8plld7m2-dce4fSGuMz0fgytK4/edit" target="_blank" class="nav-link">
                <i class="nav-icon far fa-image"></i>
                <p>{{ __('Documentation') }}</p>
            </a>
        </li>

    </ul>
</nav>
