@component('component.card', ['title' => __('Monitoring')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    @endslot

    <div class="row mb-1">
        @include('monitoring.partials._buttons')
    </div>

    <div class="row">
        @include('monitoring.partials._table')
    </div>

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

        <script>
            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "1500"
            };

            //Enable check and uncheck all functionality
            $('.checkbox-toggle').click(function () {
                var clicks = $(this).data('clicks');
                if (clicks) {
                    //Uncheck all checkboxes
                    $('.table input[type=\'checkbox\']').prop('checked', false);
                    $('.checkbox-toggle .far.fa-check-square').removeClass('fa-check-square').addClass('fa-square');
                } else {
                    //Check all checkboxes
                    $('.table input[type=\'checkbox\']').prop('checked', true);
                    $('.checkbox-toggle .far.fa-square').removeClass('fa-square').addClass('fa-check-square');
                }
                $(this).data('clicks', !clicks)
            });

            $('.checkbox-delete').click(function(){
                let checkbox = $('.checkbox-projects:checked');
                $.each(checkbox, function(index, val){
                    axios.delete(`monitoring/${val.value}`)
                    $(val).closest('tr').remove();
                });
            });

            $('[data-toggle="tooltip"]').tooltip();
        </script>
    @endslot


@endcomponent
