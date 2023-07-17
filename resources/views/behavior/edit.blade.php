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

                <div class="callout callout-info">
                    <p>После добавления фраз  вы можете отсортировать их рандомно используя <i class="fas fa-random"></i> в таблице фраз.</p>
                </div>

                <div class="row">
                    <div class="col-12 mb-4">
                        <label>Диапазон повторений</label>
                        <input type="text" class="js-range-slider" name="my_range" value="" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-4">
                        <button type="button" id="js-range-slider-click" class="btn btn-danger">Применить диапазон повторений</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group mb-0">
                            <label>Запрос</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label>Кол-во повторений</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group">
                            {!! Form::text('phrases[]', null, ['class' => 'form-control' . ($errors->has('phrases') ? ' is-invalid' : ''), 'minlength' => 2, 'required' => true]) !!}
                            @error('phrases') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::number('count[]', 1, ['min' => 1, 'max' => 500, 'class' => 'form-control' . ($errors->has('count') ? ' is-invalid' : ''), 'placeholder' => __('Count')]) !!}
                            @error('count') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-1">
                        <a class="btn btn-danger deleteCurrent"><i class="fas fa-trash"></i></a>
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

            let rangeSlider = $(".js-range-slider").ionRangeSlider({
                type: "double",
                min: 1,
                max: 500,
                from: 200,
                to: 400,
                grid: true,
            });

            $('#js-range-slider-click').on("click", function(){
                let slider = rangeSlider.data("ionRangeSlider");

                let min = Math.ceil(slider.result.from);
                let max = Math.floor(slider.result.to);

                $('input[name="count[]"]').each(function(i, el){
                    let input = $(el);
                    input.val(Math.floor(Math.random() * (max - min + 1) + min));
                });
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
                                placeholder: "Фразы менее 2 символов будут проигнорированы",
                            }]
                        },
                    ],
                    onAgree: function (m) {
                        let phrases = m.find('textarea').val().split('\n');
                        let orig = $('#adding-request').prev();

                        $.each(phrases, function(i, val){
                            if(val.length < 2)
                                return;

                            let clone = orig.clone();

                            clone.find('input[name="phrases[]"]').val(val);
                            clone.insertAfter($('.row').last());
                        });

                        $('input[name="phrases[]"]').each(function(i, el){
                            if($('input[name="phrases[]"]').length > 1){
                                if(!$(el).val())
                                    $(el).closest('.row').remove();
                            }
                        });

                        m.modal('hide');
                    }
                });
            });

            $('.card-body').on("click", ".deleteCurrent", function () {
                if($(".card-body").find('input[name="phrases[]"]').length > 1){
                    $(this).closest('.row').remove();
                }
            });
        </script>
    @endslot


@endcomponent
