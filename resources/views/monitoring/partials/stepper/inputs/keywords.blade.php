

<div class="row">

    <div class="col-6">
        <div class="row">
            <div class="col-12 clearfix">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Добавить список запросов</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">

                        <div class="callout callout-warning">
                            <ul class="mb-0">
                                <li class="text-success">Заполните или загрузите список запросов.</li>
                                <li class="text-danger">Нажмите кнопку "Добавить запросы".</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <label>Добавьте ваш список запросов</label>
                            <textarea id="textarea-keywords" class="form-control" rows="10" placeholder="Введите ваш список запросов, каждый с новой строки"></textarea>
                        </div>

                        <div class="form-group">
                            <input type="file" id="csv-keywords">
                            <p class="text-sm text-muted">Вы можете загрузить csv файл, где в первой колонке будут запросы, а во второй релевантаная страница.</p>
                        </div>

                        <div class="form-group">
                            <label>Релевантный URL</label>
                            <input type="text" class="form-control" id="relevant-url" placeholder="URL">
                        </div>

                        <div class="form-group">
                            <label>Цель</label>
                            <select class="custom-select" name="target">
                                <option value="1">1</option>
                                <option value="3">3</option>
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="remove-duplicates" value="1" checked="">
                                <label for="remove-duplicates" class="custom-control-label">Проверка на дубли</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Выбрать группу</label>
                            <select class="form-control" name="group" id="keyword-groups" style="width: 100%;"></select>
                        </div>

                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Название группы">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-success" id="create-group">Добавить новую группу</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" id="add-keywords" class="btn btn-success float-right">Добавить запросы</button>
                    </div>
                </div>
                <!-- /.card -->
            </div>

        </div>
    </div>

    <div class="col-6">
        <div class="row">
            <div class="col-12 clearfix">
                <div class="card">
                    <table id="myTable" class="table table-striped" style="width:100%"></table>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>

</div>

<div class="input-keywords"></div>


