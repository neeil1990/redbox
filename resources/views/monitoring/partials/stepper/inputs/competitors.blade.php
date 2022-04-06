<div class="form-group">
    <select class="duallistbox-competitors" multiple="multiple" data-model="#modal-competitors">
        <option>Alaska</option>
        <option>California</option>
        <option>Delaware</option>
        <option>Tennessee</option>
        <option>Texas</option>
        <option>Washington</option>
    </select>
</div>

@include('monitoring.partials._modal', ['id' => 'modal-competitors', 'title' => 'Добавьте ваш список конкурентов', 'placeholder' => 'Введите URL конкурентов, каждый с новой строки'])


