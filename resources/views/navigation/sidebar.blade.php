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
        @if(isset($modules))
            @foreach($modules as $key => $module)
                @if(!array_key_exists('configurationInfo', $module))
                    <li class="nav-item menu-item" data-id="{{ $module['id'] }}">
                        <a class="nav-link search-link" href="{{ $module['link'] }}"
                           style="white-space: inherit !important;">
                            <span class="ml-2">{!! $module['icon'] !!}
                                <span class="module-name">{{ $module['title'] }}
                                </span>
                            </span>
                        </a>
                    </li>
                @elseif(count($module) > 1)
                    <li class="nav-item menu-item ml-2 @if($module['configurationInfo']['show'] == 'true') menu-is-opening menu-open @endif">
                        <a href="#" class="nav-link">
                            <i class="fa-solid fa-folder"></i>
                            <p> {{ $key }} </p>
                        </a>
                        <ul class="nav nav-treeview" @if($module['configurationInfo']['show'] == 'false') style="display: none;" @endif>
                            @foreach($module as $k => $elem)
                                @if($k === 'configurationInfo')
                                    @continue
                                @endif
                                <li class="nav-item pl-2" data-id="{{ $elem['id'] }}">
                                    <a class="nav-link search-link" href="{{ $elem['link'] }}"
                                       style="white-space: inherit !important;">
                                        <span class="ml-3">{!! $elem['icon'] !!}
                                            <span class="module-name">{{ $elem['title'] }}</span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    <li class="nav-item menu-item">
                        <a href="#" class="nav-link">
                            <i class="fa-solid fa-folder"></i>
                            <p> {{ $key }} </p>
                        </a>
                    </li>
                @endif
            @endforeach
        @else
            <li class="nav-item menu-item">
                <a class="nav-link search-link" href="/login" style="white-space: inherit !important;">
                    <span> <i class="fa fa-users"></i>
                        <span class="module-name ml-2"> {{ __('Login page') }}</span>
                    </span>
                </a>
            </li>
        @endif
        {{-- Контроллер с CRUD DescriptionProjectForAdminController--}}
    </ul>
</nav>
<script>
    $('.x-input__field.form-control.form-control-sidebar').on('keyup', function () {
        let input = $(this).val().trim()
        if (input === '') {
            $('.nav-item.menu-item').show()
            $('.nav.nav-treeview').hide()
            setTimeout(() => {
                $('.nav-item.menu-item.ml-2.menu-is-opening.menu-open').removeClass('menu-is-opening menu-open')
            }, 310)
            return;
        }

        $.each($('.nav-item.menu-item.ml-2').children('ul'), function () {
            let mainBlock = $(this).parent()
            let showMain = false
            $.each(mainBlock.children('ul').eq(0).children('li'), function () {
                let html = $(this).children('a').eq(0).children('span').eq(0).children('span').eq(0).html().trim()
                if (html.includes(input)) {
                    showMain = true;
                }
            })

            if (showMain) {
                mainBlock.show()
                mainBlock.children('ul').eq(0).show(300)
            } else {
                mainBlock.hide()
                mainBlock.children('ul').eq(0).show(300)
            }
        })
    })

    let visible = true;
    $('#show-and-hide').click(() => {
        if (visible) {
            visible = false;
            $('div.info').css({
                'margin-top': '10px'
            })
            $('.brand-link').css({
                'display': "none"
            })

            $('.module-name').css({
                'display': 'none'
            })
        } else {
            visible = true;
            $('div.info').css({
                'margin-top': '0'
            })
            $('.brand-link').css({
                'display': "block"
            })

            $('.module-name').css({
                'display': 'inline'
            })
        }
    })
</script>
