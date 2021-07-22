@extends('layouts.app')

@section('title', $title)

@section('css')
    {{ $css ?? null }}
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $title }}</h3>
            <div class="card-tools">
                <!-- This will cause the card to maximize when clicked -->
                <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            {{ $slot }}
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
@stop

@section('js')
    {{ $js ?? null }}
@stop
