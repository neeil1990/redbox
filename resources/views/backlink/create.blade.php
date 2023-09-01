@component('component.card', ['title' => __('Add link tracking')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/backlink/css/backlink.css') }}">
        <style>
            .BacklinkProject, .BacklinkLinks {
                background: oldlace;
            }
        </style>
    @endslot
    {!! Form::open(['action' =>'BacklinkController@store', 'method' => 'POST', 'class' => 'express-form'])!!}
    <div class='col-md-6 mt-3'>
        <div class='form-group required'>
            {!! Form::label(__('Project name')) !!}
            {!! Form::text('project_name', null, ['class' => 'form form-control','required','placeholder' => __('Project name')]) !!}
        </div>
        <div class='form-group'>
            @include('backlink._monitoring_options', ['options' => $monitoring, 'value' => null, 'class' => ['form-control']])
        </div>
        <div class='form-group required'>
            {!! Form::label(__('Loading links with a list')) !!}
            {!! Form::textarea('params', null,[
            'class' => 'form-control',
            'required',
            'placeholder' => 'donor.ru/1::akceptor.ru/2::текст ссылки::1::1::1::1'
            ]) !!}
            <span class="__helper-link ui_tooltip_w">
                {{ __('Decoding of the design') }}
            <i class="fa fa-question-circle"></i>
                <span class="ui_tooltip __right __l">
                    <span class="ui_tooltip_content" style="width: 600px">
                        <p>
                            donor.ru/url/::akceptor.ru/another/url/::anchor::1::1
                        </p>
                        donor.ru/url/ - {{ __('The page of the site where the link will be searched') }}
                        akceptor.ru/another/url/ - {{ __('The link that the script will search for') }}<br>
                        текст ссылки - {{ __('Anchor') }}<br>
                        {{ __('Check that the rel attribute with the nofollow property is not present in the link - (0 - no/1 - yes)') }}<br>
                        {{ __('Check that the link is missing in the noindex tag - (0 - no/1 - yes)') }}<br>
                        {{ __('Separate the lines using Shift + Enter') }}
                    </span>
                </span>
            </span>
            <p>{{ __('You can') }} <a href="#" class="text-info">{{ __('use a simplified format') }}</a></p>
        </div>
        <div class='pt-3'>
            <button class='btn btn-secondary mr-2' type='submit'>{{ __('Add to Tracking') }}</button>
            <a href='{{ route('backlink') }}' class='btn btn-default'>{{ __('To my projects') }}</a>
        </div>
    </div>
    {!! Form::close() !!}
    <div style="display: none" class="simplified-form">
        <p>{{ __('You can') }} <a href="#" class="text-info express">{{ __('use the accelerated format') }}</a></p>
        {!! Form::open(['action' =>'BacklinkController@store', 'method' => 'POST'])!!}
        <div class='form-group required w-50'>
            {!! Form::label(__('Project name')) !!}
            {!! Form::text('project_name', null, [
            'class' => 'form form-control',
            'required'
            ]) !!}
        </div>
        <div class='form-group w-50'>
            @include('backlink._monitoring_options', ['options' => $monitoring, 'value' => null, 'class' => ['form-control']])
        </div>
        <input type="hidden" name="countRows" id="countRows" value="1">
        <table id="example2"
               class="table table-bordered table-hover dataTable dtr-inline">
            <thead>
            <tr>
                <th style="vertical-align: middle; text-align: center;">{{ __('Link to the page of the donor website') }}</th>
                <th style="vertical-align: middle; text-align: center;">{{ __('The link that the script will search for') }}</th>
                <th style="vertical-align: middle; text-align: center;">{{ __('Anchor') }}</th>
                <th style="vertical-align: middle; text-align: center;">{{ __('Check that the rel attribute with the nofollow property is not present in the link') }}</th>
                <th style="vertical-align: middle; text-align: center;">{{ __('Check that the link is missing in the noindex tag') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    {!! Form::text('site_donor_1', null, ['class' => 'form-control backlink','required']) !!}
                </td>
                <td>
                    {!! Form::text('link_1', null, ['class' => 'form-control backlink','required']) !!}
                </td>
                <td>
                    {!! Form::text('anchor_1', null, ['class' => 'form-control backlink','required']) !!}
                </td>
                <td>
                    {!! Form::select('nofollow_1', ['1' => __('Yes'), '0' => __('No')], null, ['class' => 'custom-select rounded-0']) !!}
                </td>
                <td>
                    {!! Form::select('noindex_1', ['1' => __('Yes'), '0' => __('No')], null, ['class' => 'custom-select rounded-0']) !!}
                </td>
            </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-between">
            <div class="buttons">
                <input type="submit" class="btn btn-secondary mr-2" value="{{ __('Add to Tracking') }}">
                <input type="button" class="btn btn-default mr-2" id="addRow" value="{{ __('Add row') }}">
                <input type="button" class="btn btn-default" id="removeRow" value="{{ __('Delete row') }}"
                       style="display: none">
            </div>
            <a href='{{ route('backlink') }}' class='btn btn-default'> {{ __('To my projects') }}</a>
        </div>
        {!! Form::close() !!}
    </div>
    @slot('js')
        <!-- Select2 -->
        <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

        <script>
            var countRows = 1
            $('.text-info').click(function () {
                $('.express-form').hide(300)
                $('.simplified-form').show(300)
            });
            $('.express').click(function () {
                $('.express-form').show(300)
                $('.simplified-form').hide(300)
            });
            $('#addRow').click(function () {
                $('#removeRow').show(100)
                countRows++
                $('#countRows').val(countRows)
                $('#example2 tbody').append(
                    '<tr id="tr-id-' + countRows + '">' +
                    '<td><input type="text" name="site_donor_' + countRows + '" class="form form-control" required></td>' +
                    '<td><input type="text" name="link_' + countRows + '" class="form form-control" required></td>' +
                    '<td><input type="text" name="anchor_' + countRows + '" class="form form-control" required></td>' +
                    '<td><select class="custom-select rounded-0" name="nofollow_' + countRows + '" id=""><option value="1">{{ __("Yes") }}</option><option value="0">{{ __("No") }}</option></select></td>' +
                    '<td><select class="custom-select rounded-0" name="noindex_' + countRows + '" id=""><option value="1">{{ __("Yes") }}</option><option value="0">{{ __("No") }}</option></select></td>' +
                    '</tr>'
                );
            });

            $('#removeRow').click(function () {
                $('#tr-id-' + countRows).remove();
                countRows--;
                $('#countRows').val(countRows)
                if (countRows == 1) {
                    $('#removeRow').hide(100)
                }
            });

            $('.monitoring-options').select2({
                theme: 'bootstrap4',
                selectOnClose: true,
                sorter: function(el){
                    return el.sort((a, b) => {
                        a = a.text.toLowerCase();
                        b = b.text.toLowerCase();
                        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
                    });
                },
            });
        </script>
    @endslot
@endcomponent
