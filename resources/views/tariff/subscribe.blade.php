<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Управление подпиской</h3>
    </div>

    <div class="card-body">
        <h5>Тарифный план на который вы подписаны.</h5>
        @include('tariff.partials._table', ['id' => '', 'total' => $actual['info']])
    </div>

    <div class="card-footer">
        <a href="javascript:void(0)" class="btn btn-danger" id="unsubscribe">Отменить подписку</a>
    </div>
</div>
