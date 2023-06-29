@component('component.card', ['title' => __('Behavior')])


    @slot('css')
        <link rel="stylesheet" href="{{ asset('plugins/ion-rangeslider/css/ion.rangeSlider.css') }}">

        <style>
            .behavior {
                background: oldlace;
            }
        </style>
    @endslot

    <div class="col-md-6">
        @if (session('adding_phrases'))
            <div class="alert alert-danger" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('adding_phrases') }}
            </div>
        @endif
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ __('Requests') }}</h3>
            </div>

            {!! Form::open(['method' => 'PATCH', 'route' => ['behavior.update', $behavior->id]]) !!}
            <div class="card-body">

                <div class="row">
                    <div class="col-12 mb-4">
                        <label>Диапазон повторений</label>
                        <input type="text" class="js-range-slider" name="my_range" value="" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group">
                            {!! Form::text('phrases[]', null, ['class' => 'form-control' . ($errors->has('phrases') ? ' is-invalid' : '')]) !!}
                            @error('phrases') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::number('count[]', 1, ['min' => 1, 'max' => 500, 'class' => 'form-control' . ($errors->has('count') ? ' is-invalid' : ''), 'placeholder' => __('Count')]) !!}
                            @error('count') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <button type="button" id="adding-request" class="btn btn-block btn-default">{{ __('Add key phrases request') }}</button>
                <button type="button" id="upload-request" class="btn btn-block btn-info">Добавить пакет запросов</button>
            </div>
            <div class="card-footer">
                {!! Form::submit(__('Save'), ['class' => 'btn btn-secondary float-right']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content"></div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->


    @slot('js')
        <!-- rangeSlider -->
        <script src="{{ asset('plugins/ion-rangeslider/js/ion.rangeSlider.js') }}"></script>
        <!-- Bootstrap 4 -->
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('plugins/bootstrap-modal-form-templates/bootstrap-modal-form-templates.js') }}"></script>

        <script>
            $('#adding-request').click(function(){
                let btn = $(this);
                let input = btn.prev();
                input.clone().insertAfter($('.row').last()).find('input[type="text"]').val('');
            });

            $(".js-range-slider").ionRangeSlider({
                type: "double",
                min: 0,
                max: 500,
                from: 200,
                to: 400,
                grid: true,
                onFinish: function(data) {
                    $('input[name="count[]"]').each(function(i, el){
                        let input = $(el);

                        let min = Math.ceil(data.from);
                        let max = Math.floor(data.to);

                        input.val(Math.floor(Math.random() * (max - min + 1) + min));
                    });
                }
            });

            $('#upload-request').click(function () {
                $('.modal').modal('show').BootstrapModalFormTemplates({
                    title: "Загрузка фраз",
                    fields: [
                        {
                            type: 'textarea',
                            name: "upload_phrases",
                            label: 'Вставьте фразы новую с каждой строки',
                            params: [{
                                val: "",
                                placeholder: "Ваши фразы...",
                            }]
                        },
                    ],
                    onAgree: function (m) {
                        let phrases = m.find('textarea').val().split('\n');
                        let orig = $('#adding-request').prev();

                        $.each(phrases, function(i, val){
                            let clone = orig.clone();

                            clone.find('input[name="phrases[]"]').val(val);
                            clone.insertAfter($('.row').last());
                        });

                        $('input[name="phrases[]"]').each(function(i, el){
                            if(!$(el).val())
                                $(el).closest('.row').remove();
                        });

                        m.modal('hide');
                    }
                });
            });
        </script>
    @endslot


@endcomponent
