<div class="group group-required">
    <label for="countDays">{{ __('The day of the last online') }}</label>
    <div class="row">
        <div class="col-6">
            <select name="dateType" id="dateType" class="custom custom-select">
                <option value="all">От начала регистрации до выбранного дня</option>
                <option value="only">Только выбранный день</option>
            </select>
        </div>
        <div class="col-6">
            <input class="form form-control" type="datetime-local" name="lastOnline">
        </div>
    </div>
</div>


<div class="group group-required mt-3">
    <label for="verify">{{ __('File Type') }}</label>
    <select name="fileType" id="fileType" class="custom custom-select">
        <option value="xls">excel</option>
        <option value="csv">csv</option>
    </select>
</div>

<div class="group group-required mt-3">
    <label for="verify">{{ __('Type user') }}</label>
    <select name="verify" id="verify" class="custom custom-select">
        <option value="verify">{{ __('Verified user') }}</option>
        <option value="noVerify">{{ __('No verified user') }}</option>
        <option value="all">{{ __('Any user') }}</option>
    </select>
</div>
