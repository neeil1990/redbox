@component('component.card', ['title' => __('ROI calculator')])

    @slot('css')

        <link rel="stylesheet" href="{{ asset('plugins/roi/css/AdminLTE.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/roi/css/style.css') }}">

        <style>
            .ROI {
                background: oldlace;
            }
        </style>

    @endslot

    <section class="content">
        <!-- switch -->
        <div class="row centered">
            <div class="btn-group">
                <button type="button" class="btn btn-info active click_tracking" data-click="ROI calculator" data-id="calc">{{ __('ROI calculator') }}</button>
                <button type="button" class="btn btn-info click_tracking" data-click="Traffic forecast" data-id="prognoz">{{ __('Traffic forecast') }}</button>
            </div>
        </div>

        <header class="box-result" id="calc">
            <div class="row">
                <div class="col-lg-4 col-sm-4 col-xs-12">
                    <div class="row">
                        <form style="display:contents">
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="zatrat">{{ __('RK cost') }}</label>
                                <input type="number" class="form-control" name="zatrat" id="zatrat"
                                       placeholder="{{ __('Costs in rubles') }}" required>
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="doxod">{{ __('Income from RK') }}</label>
                                <input type="number" class="form-control" name="doxod" id="doxod"
                                       placeholder="{{ __('Income in rubles') }}" required>
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="prosmotr">{{ __('Views') }}</label>
                                <input type="number" class="form-control" name="prosmotr" id="prosmotr"
                                       placeholder="{{ __('Number of views') }}">
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="kliki">{{ __('Clicks') }}</label>
                                <input type="number" class="form-control" name="kliki" id="kliki"
                                       placeholder="{{ __('Number of clicks') }}">
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="zayavka">{{ __('Applications, calls') }}</label>
                                <input type="number" class="form-control" name="zayavka" id="zayavka"
                                       placeholder="{{ __('Number of actions') }}">
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="pokupka">{{ __('Sales') }}</label>
                                <input type="number" class="form-control" name="pokupka" id="pokupka"
                                       placeholder="{{ __('Number of sales') }}">
                            </div>
                        </form>
                    </div>

                    <div class="row">
                        <br/>
                        <div class="col-lg-6 col-lg-6 col-xs-6">
                            <a class="btn btn-block btn-secondary click_tracking" data-click="Calculate" id="go-calc"><i
                                    class="fa fa-check success"></i> {{ __('Calculate') }}</a>
                        </div>
                        <div class="col-lg-6 col-xs-6">
                            <button type="reset" class="btn btn-block btn-secondary disabled click_tracking" data-click="Clear" style="cursor: pointer"
                                    id="go-reset"><i class="fa fa-times reject"></i> {{ __('Clear') }}</button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-sm-8 col-xs-12">

                    <div class="row boxes">

                        <?
                        $arRoi = array(
                            array("id_name" => "bg-change-roi", "id_value" => "rez-roi-roi", "theme" => "danger", "name" => "ROI", "text" => __('Return on investment'), "type" => "%"),
                            array("id_name" => "bg-change-ctr", "id_value" => "rez-roi-ctr", "theme" => "danger", "name" => "CTR", "text" => __('From impressions to clicks'), "type" => "%"),
                            array("id_name" => "bg-change-ctc", "id_value" => "rez-roi-ctc", "theme" => "danger", "name" => "CTC", "text" => __('From clicks to actions'), "type" => "%"),
                            array("id_name" => "bg-change-ctb", "id_value" => "rez-roi-ctb", "theme" => "danger", "name" => "CTB", "text" => __('From impressions to purchases'), "type" => "%"),
                            array("id_name" => "bg-change-cpm", "id_value" => "rez-roi-cpm", "theme" => "warning", "name" => "CPM", "text" => __('Price per 1000 impressions'), "type" => "₽"),
                            array("id_name" => "bg-change-cpc", "id_value" => "rez-roi-cpc", "theme" => "warning", "name" => "CPC", "text" => __('Price per click'), "type" => "₽"),
                            array("id_name" => "bg-change-cpa", "id_value" => "rez-roi-cpa", "theme" => "warning", "name" => "CPA", "text" => __('Price per action'), "type" => "₽"),
                            array("id_name" => "bg-change-cps", "id_value" => "rez-roi-cps", "theme" => "warning", "name" => "CPS", "text" => __('Price per sale'), "type" => "₽"),
                            array("id_name" => "bg-change-apv", "id_value" => "rez-roi-apv", "theme" => "success", "name" => "APV", "text" => __('Average check for 1 purchase'), "type" => "₽"),
                            array("id_name" => "bg-change-apc", "id_value" => "rez-roi-apc", "theme" => "success", "name" => "APC", "text" => __('Average check for 1 visit'), "type" => "₽"),
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
            <input type="hidden" id="<?=$roi['id_value']?>-val">
            <? endforeach; ?>
        </header>


        <header class="box-result" id="prognoz">

            <div class="row">

                <div class="col-lg-4 col-sm-4 col-xs-12">
                    <div class="row">
                        <form style="display:contents">
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="budget">{{ __('RK budget') }}</label>
                                <input type="number" class="form-control input-lg" name="budget" id="budget"
                                       placeholder="{{ __('Costs in rubles') }}" required>
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="clickcost">{{ __('Average cost per click') }}</label>
                                <input type="number" class="form-control input-lg" name="clickcost" id="clickcost"
                                       placeholder="{{ __('Cost per click in rubles') }}" required>
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="convaction">{{ __('Conversion rate') }}</label>
                                <input type="number" class="form-control input-lg" name="convaction" id="convaction"
                                       placeholder="{{ __('Percentage of targeted actions') }}">
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="convsales">{{ __('Percentage of sales') }}</label>
                                <input type="number" class="form-control input-lg" name="convsales" id="convsales"
                                       placeholder="{{ __('Percentage of sales') }}">
                            </div>
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <label for="sredcheck">{{ __('Average check') }}</label>
                                <input type="number" class="form-control input-lg" name="sredcheck" id="sredcheck"
                                       placeholder="{{ __('Average check of 1 purchase') }}">
                            </div>
                        </form>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-lg-6 col-xs-6">
                            <a class="btn btn-block btn-secondary" id="go-prognoz"><i
                                    class="fas fa-check success"></i> {{ __('Calculate') }}</a>
                        </div>
                        <div class="col-lg-6 col-xs-6">
                            <button type="reset" class="btn btn-block btn-secondary disabled" style="cursor: pointer"
                                    id="go-prreset"><i class="fa fa-times reject"></i> {{ __('Clear') }}</button>
                        </div>
                    </div>

                </div>

                <div class="col-lg-8 col-sm-8 col-xs-12">

                    <div class="row boxes">
                        <?
                        $arRoiTraff = array(
                            array("id_name" => "bg-change-prcli", "id_value" => "perclicks", "theme" => "danger", "name" => "CLI", "text" => __('Clicks'), "type" => " "),
                            array("id_name" => "bg-change-pract", "id_value" => "peractions", "theme" => "danger", "name" => "ACT", "text" => __('Targeted actions'), "type" => " "),
                            array("id_name" => "bg-change-prsal", "id_value" => "persales", "theme" => "danger", "name" => "SAL", "text" => __('Sales'), "type" => " "),
                            array("id_name" => "bg-change-prrev", "id_value" => "perrevenue", "theme" => "danger", "name" => "REV", "text" => __('Income'), "type" => "₽"),
                            array("id_name" => "bg-change-prroi", "id_value" => "perroi", "theme" => "warning", "name" => "ROI", "text" => __('Return on investment'), "type" => "%"),
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
            <input type="hidden" id="rez-<?=$roi['id_value']?>">
            <? endforeach; ?>

        </header>

    </section>

    @slot('js')
        <!-- Calc -->
        <script src="{{ asset('plugins/roi/js/calc.js') }}"></script>
    @endslot
@endcomponent
