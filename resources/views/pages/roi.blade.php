@component('component.card', ['title' => __('ROI calculator')])

    @slot('css')

        <link rel="stylesheet" href="{{ asset('plugins/roi/css/AdminLTE.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/roi/css/style.css') }}">
    @endslot

    <section class="content">
        <!-- switch -->
        <div class="row centered">
            <div class="btn-group">
                <button type="button" class="btn btn-info active" data-id="calc">Калькулятор ROI</button>
                <button type="button" class="btn btn-info" data-id="prognoz">Прогноз трафика</button>
            </div>
        </div>

        <header class="box-result" id="calc">
            <div class="row">
                <div class="col-lg-4 col-sm-4 col-xs-12">
                    <div class="row">
                        <form style="display:contents">
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="zatrat">Стоимость РК</label>
                                <input type="number" class="form-control" name="zatrat" id="zatrat" placeholder="Затраты в рублях" required>
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="doxod">Доход от РК</label>
                                <input type="number"  class="form-control" name="doxod" id="doxod" placeholder="Доходы в рублях" required>
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="prosmotr">Просмотры</label>
                                <input type="number"  class="form-control" name="prosmotr" id="prosmotr" placeholder="Кол-во просмотров">
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="kliki">Клики</label>
                                <input type="number"  class="form-control" name="kliki" id="kliki" placeholder="Кол-во кликов">
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="zayavka">Заявки, звонки</label>
                                <input type="number"  class="form-control" name="zayavka" id="zayavka" placeholder="Кол-во действий">
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="pokupka">Продажи</label>
                                <input type="number"  class="form-control" name="pokupka" id="pokupka" placeholder="Кол-во продаж">
                            </div>
                        </form>
                    </div>

                    <div class="row">
                        <br/>
                        <div class="col-lg-6 col-lg-6 col-xs-6">
                            <a class="btn btn-block btn-primary btn-lg" id="go-calc"><i class="fa fa-check success"></i> Посчитать</a>
                        </div>
                        <div class="col-lg-6 col-xs-6">
                            <button type="reset" class="btn btn-block btn-default btn-lg" id="go-reset"><i class="fa fa-times reject"></i> Очистить</button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-sm-8 col-xs-12">

                    <div class="row boxes">

                        <?
                        $arRoi = array(
                            array("id_name" => "bg-change-roi", "id_value" => "rez-roi-roi", "theme" => "danger", "name" => "ROI", "text" => "Окупаемость инвестиций", "type" => "%"),
                            array("id_name" => "bg-change-ctr", "id_value" => "rez-roi-ctr", "theme" => "danger", "name" => "CTR", "text" => "Из показов в клики", "type" => "%"),
                            array("id_name" => "bg-change-ctc", "id_value" => "rez-roi-ctc", "theme" => "danger", "name" => "CTC", "text" => "Из кликов в действия", "type" => "%"),
                            array("id_name" => "bg-change-ctb", "id_value" => "rez-roi-ctb", "theme" => "danger", "name" => "CTB", "text" => "Из показов в покупки", "type" => "%"),
                            array("id_name" => "bg-change-cpm", "id_value" => "rez-roi-cpm", "theme" => "warning", "name" => "CPM", "text" => "Цена за 1000 показов", "type" => "₽"),
                            array("id_name" => "bg-change-cpc", "id_value" => "rez-roi-cpc", "theme" => "warning", "name" => "CPC", "text" => "Цена за 1 клик", "type" => "₽"),
                            array("id_name" => "bg-change-cpa", "id_value" => "rez-roi-cpa", "theme" => "warning", "name" => "CPA", "text" => "Цена за 1 действие", "type" => "₽"),
                            array("id_name" => "bg-change-cps", "id_value" => "rez-roi-cps", "theme" => "warning", "name" => "CPS", "text" => "Цена за 1 продажу", "type" => "₽"),
                            array("id_name" => "bg-change-apv", "id_value" => "rez-roi-apv", "theme" => "success", "name" => "APV", "text" => "Средний чек за 1 покупку", "type" => "₽"),
                            array("id_name" => "bg-change-apc", "id_value" => "rez-roi-apc", "theme" => "success", "name" => "APC", "text" => "Средний чек за 1 визит", "type" => "₽"),
                        );
                        ?>

                        <? foreach($arRoi as $roi): ?>
                        <div class="col-lg-6 col-sm-6 col-xs-12">
                            <div class="box box-solid box-<?=$roi['theme']?>">
                                <div class="box-header">
                                    <div class="box-name" id="<?=$roi['id_name']?>"><?=$roi['name']?></div>
                                    <div class="box-text"><?=$roi['text']?></div>
                                </div><!-- /.box-header -->
                                <div class="box-body text-center">
                                    <span id="<?=$roi['id_value']?>"></span><?=$roi['type']?>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                        <!-- /.col -->
                        <? endforeach; ?>

                    </div>
                    <!-- /.row -->

                </div>

            </div>

            <? foreach($arRoi as $roi): ?>
            <input  type="hidden" id="<?=$roi['id_value']?>-val">
            <? endforeach; ?>
        </header>


        <header class="box-result" id="prognoz">

            <div class="row">

                <div class="col-lg-4 col-sm-4 col-xs-12">
                    <div class="row">
                        <form style="display:contents">
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="budget">Бюджет РК</label>
                                <input type="number" class="form-control input-lg" name="budget" id="budget" placeholder="Затраты в рублях" required>
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="clickcost">Средняя цена клика</label>
                                <input type="number"  class="form-control input-lg" name="clickcost" id="clickcost" placeholder="Цена за клик в рублях" required>
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="convaction">Процент конверсий</label>
                                <input type="number"  class="form-control input-lg" name="convaction" id="convaction" placeholder="Процент целевых действий">
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="convsales">Процент продаж</label>
                                <input type="number"  class="form-control input-lg" name="convsales" id="convsales" placeholder="Процент продаж">
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="sredcheck">Средний чек</label>
                                <input type="number"  class="form-control input-lg" name="sredcheck" id="sredcheck" placeholder="Средний чек 1 покупки">
                            </div>
                        </form>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-lg-6 col-xs-6">
                            <a class="btn btn-block btn-primary btn-lg" id="go-prognoz"><i class="fa fa-check success"></i> Посчитать</a>
                        </div>
                        <div class="col-lg-6 col-xs-6">
                            <button type="reset" class="btn btn-block btn-default btn-lg" id="go-prreset"><i class="fa fa-times reject"></i> Очистить</button>
                        </div>
                    </div>

                </div>

                <div class="col-lg-8 col-sm-8 col-xs-12">

                    <div class="row boxes">
                        <?
                        $arRoiTraff = array(
                            array("id_name" => "bg-change-prcli", "id_value" => "perclicks", "theme" => "danger", "name" => "CLI", "text" => "Кликов", "type" => " "),
                            array("id_name" => "bg-change-pract", "id_value" => "peractions", "theme" => "danger", "name" => "ACT", "text" => "Целевых действий", "type" => " "),
                            array("id_name" => "bg-change-prsal", "id_value" => "persales", "theme" => "danger", "name" => "SAL", "text" => "Продаж", "type" => " "),
                            array("id_name" => "bg-change-prrev", "id_value" => "perrevenue", "theme" => "danger", "name" => "REV", "text" => "Доход", "type" => "₽"),
                            array("id_name" => "bg-change-prroi", "id_value" => "perroi", "theme" => "warning", "name" => "ROI", "text" => "Рентабельность инвестиций", "type" => "%"),
                        )
                        ?>

                        <? foreach($arRoiTraff as $key => $roi): ?>
                        <div class="col-lg-<?=($key == 4) ? 12 : 6?> col-sm-<?=($key == 4) ? 12 : 6?> col-xs-12">
                            <div class="box box-solid box-<?=$roi['theme']?>">
                                <div class="box-header">
                                    <div class="box-name" id="<?=$roi['id_name']?>"><?=$roi['name']?></div>
                                    <div class="box-text"><?=$roi['text']?></div>
                                </div><!-- /.box-header -->
                                <div class="box-body text-center">
                                    <span id="<?=$roi['id_value']?>"></span><?=$roi['type']?>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                        <!-- /.col -->
                        <? endforeach; ?>

                    </div>
                    <!-- /.row -->
                </div>
            </div>

            <? foreach($arRoiTraff as $roi): ?>
            <input  type="hidden" id="rez-<?=$roi['id_value']?>">
            <? endforeach; ?>

        </header>

    </section>

    @slot('js')
        <!-- Calc -->
        <script src="{{ asset('plugins/roi/js/calc.js') }}"></script>
    @endslot
@endcomponent
