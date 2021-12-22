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
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light" id="header-nav-bar">
        <!-- Left navbar links -->
    @include('navigation.menu')

    <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Navbar Search -->
        @include('component.search')

        <!-- Messages Dropdown Menu -->
        @include('component.messages')

        <!-- Notifications Dropdown Menu -->
            @include('component.notifications')

            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="index3.html" class="brand-link">
            <img src="/img/logo.svg" alt="RedBox" class="brand-image">
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
        @auth
            @include('users.panel')
        @endauth
        <!-- Sidebar Menu -->
        @include('navigation.sidebar')
        <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
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
    <footer class="main-footer">
        <strong>Copyright &copy; 2014-2021 <a href="#">AdminLTE.io</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 3.1.0
        </div>
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

{{-- Delete after testing --}}
<button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="Tooltip on top">
    Tooltip on top
</button>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
</script>
{{-- END Delete after testing --}}

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script defer src="{{ asset('plugins/jquery-ui/custom-jquery-ui.js') }}"></script>
{{--<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>--}}
<script>
    $(function () {
        var token = $('meta[name="csrf-token"]').attr('content');
        getCountNewNews()
        getProjects()

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
                        let item = "<li class='nav-item menu-item' data-id='" + el.id + "'> " +
                            "<a href=" + el.link + " target='_blank' class='nav-link search-link'> " +
                            el.icon +
                            "<p class='ml-2'>" + el.title + "</p> " +
                            "</a></li>"
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

        $("#tablecontents").sortable({
            items: 'div.card',
            cursor: 'move',
            opacity: 0.6,
            update: function () {
                movingProject();
            }
        });

        $('.nav.nav-pills.nav-sidebar.flex-column').sortable({
            items: 'li.nav-item',
            cursor: 'move',
            opacity: 0.6,
            update: function () {
                movingMenuItem()
            }
        });

        function movingMenuItem() {
            var orders = [];

            $('li.menu-item').each(function () {
                orders.push({
                    id: $(this).attr('data-id'),
                })
            })
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ url('menu-item-sortable') }}",
                data: {
                    orders: orders,
                    _token: token
                },
            });
        }

        function movingProject() {
            var orders = [];
            $('div.card').each(function () {
                orders.push({
                    id: $(this).attr('data-id'),
                });
            });

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ url('project-sortable') }}",
                data: {
                    orders: orders,
                    _token: token
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
