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
    <ul class="nav nav-pills nav-sidebar flex-column mt-3" data-widget="treeview" role="menu" data-accordion="false" style="min-height: 70vh; overflow-x: auto !important; padding-bottom: 50px">
        {{-- Cписок пунктов меню подргружается при помощи скрипта app.blade.php -> js -> getProjects() --}}
        {{-- Это нужно для того, чтобы пользователь мог сортировать пункты меню так, как ему удобно --}}
        {{-- Добавить пункт меню и вывод сервиса на главную можно тут /main-projects --}}
        {{-- Контроллер с CRUD DescriptionProjectForAdminController--}}
    </ul>
</nav>
