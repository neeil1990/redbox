<tr>
    <th>
        <input class="w-100 form form-control" type="date" name="dateMin"
               id="dateMin"
               value="{{ Carbon\Carbon::parse('2022-03-01')->toDateString() }}">
        <input class="w-100 form form-control" type="date" name="dateMax" id="dateMax"
               value="{{ Carbon\Carbon::now()->toDateString() }}">
    </th>
    <th>
        <input class="w-100 form form-control search-input" type="text"
               name="projectComment" id="projectComment" placeholder="comment">
    </th>
    <th>
        <input class="w-100 form form-control search-input" type="text"
               name="phraseSearch" id="phraseSearch" placeholder="phrase">
    </th>
    <th>
        <input class="w-100 form form-control search-input" type="text"
               name="regionSearch" id="regionSearch" placeholder="region">
    </th>
    <th>
        <input class="w-100 form form-control search-input" type="text"
               name="mainPageSearch" id="mainPageSearch" placeholder="link">
    </th>
    <th>
        <input class="w-100 form form-control search-input" type="number"
               name="minPosition" id="minPosition" placeholder="min">
        <input class="w-100 form form-control search-input" type="number"
               name="maxPosition" id="maxPosition" placeholder="max">
    </th>
    <th>
        <input class="w-100 form form-control search-input" type="number"
               name="minPoints" id="minPoints" placeholder="min">
        <input class="w-100 form form-control search-input" type="number"
               name="maxPoints" id="maxPoints" placeholder="max">
    </th>
    <th>
        <input class="w-100 form form-control search-input" type="number"
               name="minCoverage" id="minCoverage" placeholder="min">
        <input class="w-100 form form-control search-input" type="number"
               name="maxCoverage" id="maxCoverage" placeholder="max">
    </th>
    <th>
        <input class="w-100 form form-control search-input" type="number"
               name="minCoverageTf" id="minCoverageTf" placeholder="min">
        <input class="w-100 form form-control search-input" type="number"
               name="maxCoverageTf" id="maxCoverageTf" placeholder="max">
    </th>
    <th>
        <input class="w-100 form form-control search-input" type="number" name="minWidth"
               id="minWidth" placeholder="min">
        <input class="w-100 form form-control search-input" type="number"
               name="maxWidth" id="maxWidth" placeholder="max">
    </th>
    <th>
        <input class="w-100 form form-control search-input" type="number"
               name="minDensity" id="minDensity" placeholder="min">
        <input class="w-100 form form-control search-input" type="number"
               name="maxDensity" id="maxDensity" placeholder="max">
    </th>
    <th>
        <div>
            {{ __('Switch everything') }}
            <div class='d-flex w-100'>
                <div class='__helper-link ui_tooltip_w'>
                    <div
                        class='custom-control custom-switch custom-switch-off-danger custom-switch-on-success changeAllState'>
                        <input type='checkbox' class='custom-control-input'
                               id='changeAllState'>
                        <label class='custom-control-label' for='changeAllState'></label>
                    </div>
                </div>
            </div>
        </div>

    </th>
    <th></th>
</tr>
<tr>
    <th class="table-header">{{ __('Date of last check') }}</th>
    <th class="table-header" style="min-width: 200px">
        {{ __('Comment') }}
    </th>
    <th class="table-header" style="min-width: 160px; height: 83px">
        {{ __('Phrase') }}
    </th>
    <th class="table-header" style="min-width: 160px; height: 83px">
        {{ __('Region') }}
    </th>
    <th class="table-header" style="min-width: 160px; max-width:160px; height: 83px">
        {{ __('Landing page') }}
    </th>
    <th class="table-header" style="height: 83px; min-width: 69px">
        {{ __('Position in the top') }}
    </th>
    <th class="table-header" style="height: 83px; min-width: 69px">
        {{ __('Scores') }}
    </th>
    <th class="table-header" style="height: 83px; min-width: 69px">
        {{ __('Coverage of important words') }}
    </th>
    <th class="table-header" style="height: 83px; min-width: 69px">
        {{ __('TF coverage') }}
    </th>
    <th class="table-header" style="height: 83px; min-width: 69px">
        {{ __('Width') }}
    </th>
    <th class="table-header" style="height: 83px; min-width: 69px">
        {{ __('Density') }}
    </th>
    <th class="table-header" style="height: 83px; min-width: 69px">
        {{ __('Take into account when calculating the total score') }}
    </th>
    <th class="table-header"></th>
</tr>
