@extends('layouts.app')

@section('title', $title)

@section('css')
    {{ $css ?? null }}
@stop

@section('content')

    @if(\App\User::isUserAdmin() && count(explode('/', $code)) == 1)
        <a href="{{ route('description.edit', [$code, 'top']) }}"
           class="btn btn-secondary mb-4">{{ __('Add description') }}
        </a>
    @endif

    @if(isset($description['top']))
        @include('description.main', ['description' => $description['top']])
    @endif
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $title }}</h3>
            <div class="card-tools">
                {{ $tools ?? null }}
                <!-- This will cause the card to maximize when clicked -->
                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>
        <!-- /.card-header -->

        <div class="card-body">
            {{ $slot }}
        </div>
        <!-- /.card-body -->

        @if(isset($footer))
            <div class="card-footer">
                {{ $footer ?? null }}
            </div>
            <!-- /.card-footer -->
        @endif
    </div>
    <!-- /.card -->
    @if(isset($description['bottom']))
        @include('description.main', ['description' => $description['bottom']])
    @endif
@stop

@section('js')
    {{ $js ?? null }}

    <script>
        let name = window.location.pathname;
        $('.card-widget').on('collapsed.lte.cardwidget', function () {
            cookies.set(name, 'collapse');
        });
        $('.card-widget').on('expanded.lte.cardwidget', function () {
            cookies.set(name, 'expand');
        });

        try {
            if (cookies.get(name) == 'collapse') {
                $('.card-widget').addClass('collapsed-card');
            } else {
                $('.card-widget').removeClass('collapsed-card');
            }
        } catch (e) {

        }

    </script>
@stop
