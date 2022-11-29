<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Настройка времени снятия позиций</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="callout callout-warning">
                    <p class="mb-0">
                        Здесь Вы можете установить когда снимать позиции. Если Вы установите режим и не укажите время/дни, то проект будет обновляться только в ручном режиме. Обновить можно будет на главной странице с проектами или внутри самого проекта. Обновлять можно как все фразы сразу, так и выборочно.
                    </p>
                </div>

                <div class="form-group">
                    <label>Режимы</label>
                    <select id="mode-scan" class="custom-select">
                        <option value="times">Каждый день</option>
                        <option value="weeks">По дням</option>
                        <option value="ranges">Через определенное количество дней</option>
                    </select>
                </div>

                <div class="mode-scan"></div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
