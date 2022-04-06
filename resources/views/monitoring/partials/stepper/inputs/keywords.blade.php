<div class="form-group">
    <select class="duallistbox-keywords" multiple="multiple" data-model="#modal-keywords">
        <option>Alaska</option>
        <option>California</option>
        <option>Delaware</option>
        <option>Tennessee</option>
        <option>Texas</option>
        <option>Washington</option>
    </select>
</div>

@include('monitoring.partials._modal', ['id' => 'modal-keywords', 'title' => 'Добавьте ваш список запросов', 'placeholder' => 'Введите запросы, каждый с новой строки'])

