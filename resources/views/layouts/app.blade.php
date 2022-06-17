<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="img/favicon.svg"/>
    <title>@yield('title')</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- IonIcons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    @yield('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light" id="header-nav-bar">
        @include('navigation.menu')
        <ul class="navbar-nav ml-auto">
            @include('navigation.menu-right')
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                {!! Form::open(['class' => '', 'method' => 'POST', 'route' => ['logout']]) !!}
                {!! Form::button( '<i class="fas fa-sign-out-alt"></i>', ['type' => 'submit', 'class' => 'nav-link border-0']) !!}
                {!! Form::close() !!}
            </li>
        </ul>
    </nav>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('home') }}" class="brand-link">
            <img src="/img/logo.svg" alt="RedBox" class="brand-image">
        </a>
        <div class="sidebar">
            @auth
                @include('users.panel')
            @endauth
            @include('navigation.sidebar')
        </div>
    </aside>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Main content -->
        <div class="content pt-3" id="app">
            <div class="container-fluid">
                @yield('content')
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer" id="main-footer">
        <strong>Copyright &copy; 2021-{{ date('Y') }} <a href="https://redbox.su/">redbox.su</a>.</strong>
        All rights reserved.
    </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- app -->
@unless(request()->path() == 'utm-marks')
    <script src="{{ asset('js/app.js') }}"></script>
@endunless
<!-- AdminLTE -->
<script src="{{ asset('js/adminlte.js') }}"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('js/demo.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('js/pages/dashboard3.js') }}"></script>

@yield('js')

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    $(function () {
        let visible = true;
        var token = $('meta[name="csrf-token"]').attr('content');
        getCountNewNews()
        getProjects()

        $('#show-and-hide').click(() => {
            if (visible) {
                visible = false;
                $('div.info').css({
                    'margin-top': '10px'
                })
                $('.brand-link').css({
                    'display': "none"
                })
            } else {
                visible = true;
                $('div.info').css({
                    'margin-top': '0'
                })
                $('.brand-link').css({
                    'display': "block"
                })
            }
        })

        function getProjects() {
            $.ajax({
                type: "post",
                dataType: "json",
                url: "{{ route('get.description.projects') }}",
                data: {
                    _token: token
                },
                success: function (response) {
                    response.forEach((el) => {
                        var item = '';
                        if (el.link === 'https://lk.redbox.su/') {
                            item = "<li class='nav-item menu-item' data-id='" + el.id + "'> " +
                                "<a href=" + el.link + " class='nav-link search-link'> " +
                                el.icon +
                                "<p class='ml-2'>" + el.title + "</p> " +
                                "</a></li>"
                        } else {
                            item = "<li class='nav-item menu-item' data-id='" + el.id + "'> " +
                                "<a href=" + el.link + " target='_blank' class='nav-link search-link'> " +
                                el.icon +
                                "<p class='ml-2'>" + el.title + "</p> " +
                                "</a></li>"
                        }

                        if (window.location.href === el.link) {
                            item = $(item).addClass('menu-open');
                        }

                        $(".nav.nav-pills.nav-sidebar.flex-column").append(item);
                    })
                },
            });
        }

        function getCountNewNews() {
            $.ajax({
                type: "post",
                dataType: "json",
                url: "{{ route('get.count.new.news') }}",
                data: {
                    _token: token
                },
                success: function (response) {
                    $('.badge.badge-warning.navbar-badge.news').text(response.count)
                },
            });

        }

        $(".x-drop-down__value").click(function (event) {
            toggleMenu();
            event.stopPropagation();
        });

        $('.xx1').click(function () {
            $('.x-drop-down__value').html($(this).text());
            toggleMenu();
        });

        function toggleMenu() {
            let menu = $(".x-drop-down__dropped");
            if (!menu.hasClass('active')) {
                window.addEventListener('click', closeMenu);
            } else {
                window.removeEventListener('click', closeMenu);
            }
            menu.toggleClass("active");
        }

        function closeMenu() {
            $(".x-drop-down__dropped").removeClass("active")
        }

        $('.x-drop-down__dropped').click(function (event) {
            event.stopPropagation();
        });


        $('.x-input__field').on('input', function () {
            let search = $(this).val();
            searchData(search);
        });

        function searchData(search) {
            let items = $('.nav-link.search-link');
            items.each(function () {
                if ($(this).text().toLowerCase().indexOf(search.toLowerCase()) === -1) {
                    $(this).addClass('item_hide');
                } else {
                    $(this).removeClass('item_hide');
                }
            });
        }
    });
</script>
@include('flash::message')
</body>
</html>
