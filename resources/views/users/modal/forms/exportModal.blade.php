<div class="group group-required">
    <label for="countDays">{{ __('The day of the last online') }}</label>
    <input class="form form-control" type="datetime-local" name="lastOnline" required>
</div>

<div class="group group-required mt-3">
    <label for="verify">{{ __('File Type') }}</label>
    <select name="fileType" id="fileType" class="custom custom-select">
        <option value="xls">excel</option>
        <option value="csv">csv</option>
    </select>
</div>

<div class="group group-required mt-3">
    <label for="verify">{{ __('Verified user') }}</label>
    <input type="checkbox" name="verify" checked>
</div>
