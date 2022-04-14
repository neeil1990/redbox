

<div class="row">
    <div class="col-md-6">
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
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" id="add-keywords" class="btn btn-default float-right">Добавить запросы</button>
            </div>
        </div>
        <!-- /.card -->
    </div>

    <div class="col-md-6">
        <div class="card" id="keywords">
            <div class="card-header">
                <h3 class="card-title">Ваш список запросов</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0" >
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Запрос</th>
                        <th>Страница</th>
                        <th style="width: 40px"></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr align="center">
                            <td colspan="4">Not found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-right"></ul>
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>


