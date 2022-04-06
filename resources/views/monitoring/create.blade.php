@component('component.card', ['title' => __('Monitoring')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- BS Stepper -->
        <link rel="stylesheet" href="{{ asset('plugins/bs-stepper/css/bs-stepper.min.css') }}">
        <!-- Bootstrap4 Duallistbox -->
        <link rel="stylesheet" href="{{ asset('plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css') }}">
    @endslot

    <div class="bs-stepper">
        <div class="bs-stepper-header" role="tablist">
            <!-- your steps here -->
            @include('monitoring.partials.stepper._titles', ['item' => '<i class="fas fa-user"></i>', 'target' => 'project', 'name' => 'Создать проект'])

            <div class="line"></div>

            @include('monitoring.partials.stepper._titles', ['item' => 2, 'target' => 'keywords', 'name' => 'Добавление запросов'])

            <div class="line"></div>

            @include('monitoring.partials.stepper._titles', ['item' => 3, 'target' => 'competitors', 'name' => 'Настройка конкурентов'])

            <div class="line"></div>

            @include('monitoring.partials.stepper._titles', ['item' => 4, 'target' => 'regions', 'name' => 'Поисковые системы и регионы'])

            <div class="line"></div>

            @include('monitoring.partials.stepper._titles', ['item' => '<i class="fas fa-save"></i>', 'target' => 'save', 'name' => 'Сохранить!'])

        </div>
        <div class="bs-stepper-content">
            <!-- your steps content here -->
            @include('monitoring.partials.stepper._content', ['target' => 'project', 'buttons' => ['next']])

            @include('monitoring.partials.stepper._content', ['target' => 'keywords', 'buttons' => ['previous', 'next']])

            @include('monitoring.partials.stepper._content', ['target' => 'competitors', 'buttons' => ['previous', 'next']])

            @include('monitoring.partials.stepper._content', ['target' => 'regions', 'buttons' => ['previous', 'next']])

            @include('monitoring.partials.stepper._content', ['target' => 'save', 'buttons' => ['previous', 'action']])
        </div>
    </div>

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
        <!-- BS-Stepper -->
        <script src="{{ asset('plugins/bs-stepper/js/bs-stepper.min.js') }}"></script>
        <!-- Bootstrap4 Duallistbox -->
        <script src="{{ asset('plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>

        <script>
            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "1500"
            };

            document.addEventListener('DOMContentLoaded', function () {
                window.stepper = new Stepper(document.querySelector('.bs-stepper'))
            });

            var duallistBoxes = [];

            duallistBoxes.push($('.duallistbox-keywords'));
            duallistBoxes.push($('.duallistbox-competitors'));

            $.each(duallistBoxes, function (index, box) {

                var target = box.data('model');

                //Bootstrap Duallistbox
                var duallistbox = box.bootstrapDualListbox({
                    nonSelectedListLabel: 'Рекомендованные',
                    selectedListLabel: 'Выбранные [<a href="#" data-toggle="modal" data-target="'+ target +'">Добавить свой список.</a>]',
                    moveOnSelect: false,
                    filterOnValues: true,
                    preserveSelectionOnMove: 'moved',
                    moveSelectedLabel: 'Добавить выбранные',
                    moveAllLabel: 'Добавить все',
                    removeSelectedLabel: 'Удалить выбранные',
                    removeAllLabel: 'Удалить все',
                });

                $(target).on('show.bs.modal', function (event) {
                    var modal = $(this);

                    modal.find('.btn.add').click(function () {
                        var textarea = modal.find('textarea').val();
                        var list = _.compact(textarea.split(/[\r\n]+/));

                        if(list.length){

                            $.each(list, function (index, value) {
                                let option = $('<option />').val(value).attr('selected', '').text(value);
                                duallistbox.append(option);
                            });

                            duallistbox.bootstrapDualListbox('refresh');

                            modal.modal('hide');
                        }else{
                            toastr.error('Что-то не так...');
                        }
                    });
                });

                $(target).on('hide.bs.modal', function (event) {
                    var modal = $(this);
                    modal.find('.btn.add').off("click");
                });
            });
        </script>
    @endslot


@endcomponent
