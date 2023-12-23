<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Название проекта</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <div class="callout callout-warning">
                    <ul class="mb-0">
                        <li class="text-success">Заполните название проекта</li>
                        <li class="text-danger">URL в формате domain.com</li>
                    </ul>
                </div>

                <div class="form-group">
                    {!! Form::label('name', 'Имя проекта') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Проект']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('url', 'URL домена') !!}
                    {!! Form::text('url', null, ['class' => 'form-control', 'placeholder' => 'domain.com']) !!}
                </div>

            </div>
            <!-- /.card-body -->

        </div>
        <!-- /.card -->
    </div>
</div>
