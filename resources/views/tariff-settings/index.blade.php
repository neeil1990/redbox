@extends('layouts.app')

@section('css')
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Variable Table</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">

                    <table class="table table-bordered" align="center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th data-toggle="tooltip" data-placement="top" title="Необязательное название свойства.">
                                    Name
                                    <i class="fas fa-question-circle"></i>
                                </th>
                                <th data-toggle="tooltip" data-placement="top" title="Свойство которое мы будем использовать в коде.">
                                    Code
                                    <i class="fas fa-question-circle"></i>
                                </th>
                                <th data-toggle="tooltip" data-placement="top" title="Необязательное описание свойства.">
                                    Description
                                    <i class="fas fa-question-circle"></i>
                                </th>
                                <th style="width: 1%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($settings as $setting)
                            <tr>
                                <td>{{$setting->id}}</td>
                                <td>{{$setting->name}}</td>
                                <td class="copy">
                                    <code>{{$setting->code}}</code>
                                    <i class="far fa-copy text-muted"></i>
                                </td>

                                <td>{{$setting->description}}</td>

                                <td>
                                    <table width="100%" style="width: 365px">
                                        <tr>
                                            <th data-toggle="tooltip" data-placement="top" title="Тариф для которого нужно значение">
                                                Tariff
                                                <i class="fas fa-question-circle"></i>
                                            </th>
                                            <th data-toggle="tooltip" data-placement="top" title="Значение тарифа">
                                                Value
                                                <i class="fas fa-question-circle"></i>
                                            </th>
                                            <th data-toggle="tooltip" data-placement="top" title="Удаление значения для тарифа">
                                                Delete
                                                <i class="fas fa-question-circle"></i>
                                            </th>
                                        </tr>
                                        @foreach($setting->fields as $field)
                                            <tr>
                                                <td>{{$field->tariff}}</td>
                                                <td>{{$field->value}}</td>
                                                <td>
                                                    {!! Form::open(['class' => 'd-inline', 'method' => 'DELETE', 'route' => ['tariff-setting-values.destroy', $field->id]]) !!}
                                                    {!! Form::button( '<i class="fas fa-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-sm']) !!}
                                                    {!! Form::close() !!}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="3">
                                                <a class="btn btn-info btn-sm" href="{{ route('tariff-setting-values.create', $setting->id) }}">
                                                    <i class="fas fa-plus"></i>
                                                    Добавить значение
                                                </a>

                                                <a class="btn btn-info btn-sm" href="{{ route('tariff-settings.edit', $setting->id) }}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                    {{ __('Edit') }}
                                                </a>

                                                {!! Form::open(['class' => 'd-inline', 'method' => 'DELETE', 'route' => ['tariff-settings.destroy', $setting->id]]) !!}
                                                {!! Form::button( '<i class="fas fa-trash"></i> ' . __('Delete'), ['type' => 'submit', 'class' => 'btn btn-danger btn-sm']) !!}
                                                {!! Form::close() !!}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->

                <div class="card-footer clearfix">
                    <a href="{{ route('tariff-settings.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add</a>
                </div>

            </div>

        </div>
    </div>
@stop

@section('js')

    <!-- Toastr -->
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

    <script>

        toastr.options = {
            "timeOut": "1000"
        };

        $('.copy').click(function(){
            let str = $(this);
            let strFormat = str.find('code').text();
            copy(strFormat);
            toastr.success('Copied successfully!');
        });

        $('[data-toggle="tooltip"]').tooltip();

    </script>
@endsection
