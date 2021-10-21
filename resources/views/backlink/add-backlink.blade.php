@component('component.card', ['title' => __('Add Link tracking')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
    @endslot
    {!! Form::open(['action' =>'BacklinkController@storeLink', 'method' => 'POST'])!!}
    <div class='col-md-6 mt-3 express-form'>
        <div class='form-group required'>
            <input type="hidden" name="id" value="{{ $id }}">
            {!! Form::label( __('Loading links with a list')) !!}
            {!! Form::textarea('params', null, [
            'class'=>'form-control',
            'required'=>'required',
            'placeholder' => 'donor.ru/1::akceptor.ru/2::текст ссылки::1::1::1::1'
            ]) !!}
            <span class="__helper-link ui_tooltip_w">
                {{ __('Decoding of the design') }}
            <i class="fa fa-question-circle"></i>
                <span class="ui_tooltip __right __l">
                    <span class="ui_tooltip_content" style="width: 600px">
                        <p>
                            donor.ru/1::akceptor.ru/2::текст ссылки::1::1::1::1
                        </p>
                        donor.ru/1 - {{ __('The page of the site where the link will be searched') }}
                        akceptor.ru/2 - {{ __('The link that the script will search for') }}<br>
                        текст ссылки - {{ __('Anchor') }}<br>
                        {{ __('Check that the rel attribute with the nofollow property is not present in the link - (0 - no/1 - yes)') }}<br>
                        {{ __('Check that the link is missing in the noindex tag - (0 - no/1 - yes)') }}<br>
                        {{ __('Checking that the link is indexed by Yandex - (0 - no/1 - yes)') }}<br>
                        {{ __('Checking that the link is indexed by Google - (0 - no/1 - yes)') }}<br><br>
                        {{ __('Separate the lines using Shift + Enter') }}
                    </span>
                </span>
            </span>
            <p>{{ __('You can') }} <a href="#" class="text-info">{{ __('use a simplified format') }}</a></p>
        </div>
        <div class='pt-3'>
            <button class='btn btn-secondary' title='Save' type='submit'>{{ __('Add to Tracking') }}</button>
            <a href='{{ route('backlink') }}' class='btn btn-default'>{{ __('To my projects') }}</a>
            <a href='{{ route('show.backlink', $id) }}' class='btn btn-default'>{{ __('Back') }}</a>
        </div>
    </div>
    {!! Form::close() !!}
    <div style="display: none" class="simplified-form">
        <p>{{ __('You can') }} <a href="#" class="text-info express">{{ __('use the accelerated format') }}</a></p>
        {!! Form::open(['action' =>'BacklinkController@storeLink', 'method' => 'POST'])!!}
        <input type="hidden" name="id" value="{{ $id }}">
        <input type="hidden" name="countRows" id="countRows" value="1">
        <table id="example2"
               class="table table-bordered table-hover dataTable dtr-inline">
            <thead>
            <tr>
                <th>{{ __('Link to the page of the donor website') }}</th>
                <th>{{ __('The link that the script will search for') }}</th>
                <th>{{ __('Anchor') }}</th>
                <th>{{ __('Check that the rel attribute with the nofollow property is not present in the link') }}</th>
                <th>{{ __('Check that the link is missing in the noindex tag') }}</th>
                <th>{{ __('Checking that the link is indexed by Yandex') }}</th>
                <th>{{ __('Checking that the link is indexed by Google') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    {!! Form::text('site_donor_1', null ,['class' => 'form-control backlink','required' => 'required']) !!}
                </td>
                <td>
                    {!! Form::text('link_1', null ,['class' => 'form-control backlink','required' => 'required']) !!}
                </td>
                <td>
                    {!! Form::text('anchor_1', null ,['class' => 'form-control backlink','required' => 'required']) !!}
                </td>
                <td>
                    {!! Form::select('nofollow_1', ['1' => __('Yes'), '0' => __('No')], null, ['class' => 'custom-select rounded-0']) !!}
                </td>
                <td>
                    {!! Form::select('noindex_1', ['1' => __('Yes'), '0' => __('No')], null, ['class' => 'custom-select rounded-0']) !!}
                </td>
                <td>
                    {!! Form::select('yandex_1', ['1' => __('Yes'), '0' => __('No')], null, ['class' => 'custom-select rounded-0']) !!}
                </td>
                <td>
                    {!! Form::select('google_1', ['1' => __('Yes'), '0' => __('No')], null, ['class' => 'custom-select rounded-0']) !!}
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
            <div>
                <a href='{{ route('backlink') }}' class='btn btn-default'>{{ __('To my projects') }}</a>
                <a href='{{ route('show.backlink', $id) }}' class='btn btn-default'>{{ __('Back') }}</a>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    @slot('js')
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
                    '<td><select class="custom-select rounded-0" name="yandex_' + countRows + '" id=""><option value="1">{{ __("Yes") }}</option><option value="0">{{ __("No") }}</option></select></td>' +
                    '<td><select class="custom-select rounded-0" name="google_' + countRows + '" id=""><option value="1">{{ __("Yes") }}</option><option value="0">{{ __("No") }}</option></select></td>' +
                    '</tr>'
                );
            });

            $('#removeRow').click(function () {
                $('#tr-id-' + countRows).remove();
                countRows--;
                $('#countRows').val(countRows)
                if (countRows === 1) {
                    $('#removeRow').hide(100)
                }
            });

        </script>
    @endslot
@endcomponent
