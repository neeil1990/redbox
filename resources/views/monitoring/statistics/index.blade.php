@component('component.card', ['title' => __('Monitoring statistics')])

    @slot('css')

    @endslot

    @slot('tools')
        <div class="btn-group">
            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                {{ __('Widget settings') }}
            </button>
            <div class="dropdown-menu dropdown-menu-right" role="menu">
                <h6 class="dropdown-header text-left">{{ __('Widgets') }}</h6>
                <form class="px-3 widget-form">
                    @foreach($menu as $item)
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="{{ $item['code'] }}" class="custom-control-input widgets-menu" id="customSwitch{{ $item['code'] }}" @if($item['active']) checked="checked" @endif>
                            <label class="custom-control-label text-nowrap" for="customSwitch{{ $item['code'] }}" style="cursor: pointer">{{ $item['name'] }}</label>
                        </div>
                    </div>
                    @endforeach
                </form>
            </div>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-wrench"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" role="menu">
                <h6 class="dropdown-header text-left">Представление кнопки настроек</h6>
                <a href="#" class="dropdown-item">Action</a>
                <h6 class="dropdown-header text-left">Заголовок</h6>
                <a href="#" class="dropdown-item">Another action</a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">Something else here</a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">Separated link</a>
            </div>
        </div>
    @endslot

    <div class="row connectedSortable">
        @foreach($widgets as $widget)
        <div class="col-lg-3 col-6" id="{{ $widget['id'] }}">
            <!-- small box -->
            <div class="small-box {{ $widget['bg'] }}">
                <div class="inner">
                    <h3>{{ $widget['title'] }}</h3>
                    <p>{{ $widget['description'] }}</p>
                </div>
                <div class="icon">
                    <i class="{{ $widget['icon'] }}"></i>
                </div>
                @if($widget['link'])
                <a href="{{ $widget['link'] }}" class="small-box-footer">{{ __('More info') }} <i class="fas fa-arrow-circle-right"></i></a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    @slot('js')
        <!-- jQuery UI -->
        <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <script>
            $.widget.bridge('uibutton', $.ui.button)
        </script>

        <script>
            // Make the dashboard widgets sortable Using jquery UI
            $('.connectedSortable').sortable({
                placeholder: 'sort-highlight',
                connectWith: '.connectedSortable',
                handle: '.small-box',
                forcePlaceholderSize: true,
                zIndex: 999999,
                stop: function( event, ui ) {
                    let items = $(this).sortable("toArray");

                    axios.post('/monitoring/statistics/sort-widgets', {
                        ids: items,
                    });
                }
            });

            $('.connectedSortable .small-box').css('cursor', 'move');

            $('.widgets-menu').change(function () {
                let menu = $('.widgets-menu');
                const formData = new FormData(document.querySelector('.widget-form'));

                let fields = [];
                $.each(menu, function (i, el) {
                    let item = $(el);
                    let field = formData.get(item.attr('name'));

                    if(field)
                        fields.push({ name: item.attr('name'), active: true});
                    else
                        fields.push({ name: item.attr('name'), active: false});
                });

                axios.post('/monitoring/statistics/active-widgets', {
                    fields: fields
                }).then(function(){
                    window.location.reload();
                });
            });
        </script>

    @endslot

@endcomponent
