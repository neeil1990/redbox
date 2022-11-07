<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('Global settings') }}</h3>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    {!! Form::label('Количество элементов в постраничной навигации') !!}
                    <div class="input-group">
                        {!! Form::text('pagination_items', $settings['pagination_items'], ['class' => 'form-control', 'placeholder' => '10,20,30,50,100,200,500,1000']) !!}
                        <div class="input-group-append">
                            <a href="{{ route('monitoring.admin.settings.delete', 'pagination_items') }}" class="input-group-text"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    {!! Form::label('Количество элементов на странице проекты') !!}
                    <div class="input-group">
                        {!! Form::number('pagination_project', $settings['pagination_project'], ['class' => 'form-control', 'placeholder' => '10']) !!}
                        <div class="input-group-append">
                            <a href="{{ route('monitoring.admin.settings.delete', 'pagination_project') }}" class="input-group-text"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    {!! Form::label('Количество элементов на странице запросы') !!}
                    <div class="input-group">
                        {!! Form::number('pagination_query', $settings['pagination_query'], ['class' => 'form-control', 'placeholder' => '100']) !!}
                        <div class="input-group-append">
                            <a href="{{ route('monitoring.admin.settings.delete', 'pagination_query') }}" class="input-group-text"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer">
        {!! Form::submit(__('Save'), ['class' => 'btn btn-success']) !!}
    </div>
</div>

