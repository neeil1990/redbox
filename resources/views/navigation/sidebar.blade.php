<nav class="mt-2">
    <div class="js-toggle x-drop-down" data-qaid="dd_widget">
        <div class="x-drop-down__dropped">
            <div class="x-drop-down__list js-dropdown">
                <div class="x-drop-down__search">
                    <div class="x-input x-input_size_s">
                        <div class="input-group">
                            <input type="text"
                                   class="x-input__field form-control form-control-sidebar"
                                   autocomplete="off"
                                   placeholder="{{ __('Search') }}"
                                   value="">
                            <div class="input-group-append">
                                <button class="btn btn-sidebar">
                                    <i class="fas fa-search fa-fw"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <ul class="nav nav-pills nav-sidebar flex-column mt-3" data-widget="treeview" role="menu" data-accordion="false"
        style="min-height: 70vh; overflow-x: hidden !important; overflow-y: auto; padding-bottom: 50px; white-space: inherit !important;">
        @foreach($modules as $module)
            <li class="nav-item menu-item" data-id="{{ $module['id'] }}">
                <a class="nav-link search-link" href="{{ $module['link'] }}" style="white-space: inherit !important;">
                    {!! $module['icon'] !!}
                    <span class="ml-1 module-name">{{ $module['title'] }}</span>
                </a>
            </li>
        @endforeach
        {{-- Контроллер с CRUD DescriptionProjectForAdminController--}}
    </ul>
</nav>
