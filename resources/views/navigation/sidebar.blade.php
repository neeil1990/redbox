<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-header">{{ __('Menu') }}</li>
        <li class="nav-item">
            <a href="/" class="nav-link">
                <ion-icon name="home-outline" class="nav-icon"></ion-icon>
                <p>{{ __('Home') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link">
                <ion-icon name="people-outline" class="nav-icon"></ion-icon>
                <p>{{ __('Users') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('behavior.index') }}" class="nav-link">
                <ion-icon name="home-outline" class="nav-icon"></ion-icon>
                <p>{{ __('Behavior') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pages.keyword') }}" class="nav-link">
                <ion-icon name="text-outline" class="nav-icon"></ion-icon>
                <p>{{ __('Keyword generator') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pages.password') }}" class="nav-link">
                <ion-icon name="home-outline" class="nav-icon"></ion-icon>
                <p>{{ __('Password generator') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pages.length') }}" class="nav-link">
                <ion-icon name="home-outline" class="nav-icon"></ion-icon>
                <p>{{ __('Counting text length') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('list.comparison') }}" class="nav-link">
                <ion-icon name="home-outline" class="nav-icon"></ion-icon>
                <p>{{ __('List comparison') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('unique.words') }}" class="nav-link">
                <ion-icon name="home-outline" class="nav-icon"></ion-icon>
                <p>{{__("Highlighting unique words in the text")}}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('HTML.editor') }}" class="nav-link">
                <ion-icon name="home-outline" class="nav-icon"></ion-icon>
                <p>{{__('HTML editor')}}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pages.duplicates') }}" class="nav-link">
                <ion-icon name="copy-outline" class="nav-icon"></ion-icon>
                <p>{{ __('Remove Duplicates') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pages.utm') }}" class="nav-link">
                <ion-icon name="bookmarks-outline" class="nav-icon"></ion-icon>
                <p>{{ __('UTM Marks') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pages.roi') }}" class="nav-link">
                <ion-icon name="calculator-outline" class="nav-icon"></ion-icon>
                <p>{{ __('ROI calculator') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pages.headers') }}" class="nav-link">
                <ion-icon name="code-slash-outline" class="nav-icon"></ion-icon>
                <p>{{ __('Http headers') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('backlink') }}" class="nav-link">
                <ion-icon name="code-slash-outline" class="nav-icon"></ion-icon>
                <p>{{ __('Link tracking') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="https://docs.google.com/document/d/1TpjCQjXYV_ZWyxD-c8plld7m2-dce4fSGuMz0fgytK4/edit"
               target="_blank" class="nav-link">
                <ion-icon name="document-text-outline" class="nav-icon"></ion-icon>
                <p>{{ __('Documentation') }}</p>
            </a>
        </li>
    </ul>
</nav>
