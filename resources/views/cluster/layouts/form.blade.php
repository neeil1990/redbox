<div class="form-group required">
    <label>{{ __('Region') }}</label>
    {!! Form::select('region', array_unique([
      '213' => __('Moscow'),
       '1' => __('Moscow and the area'),
       '20' => __('Arkhangelsk'),
       '37' => __('Astrakhan'),
       '197' => __('Barnaul'),
       '4' => __('Belgorod'),
       '77' => __('Blagoveshchensk'),
       '191' => __('Bryansk'),
       '24' => __('Veliky Novgorod'),
       '75' => __('Vladivostok'),
       '33' => __('Vladikavkaz'),
       '192' => __('Vladimir'),
       '38' => __('Volgograd'),
       '21' => __('Vologda'),
       '193' => __('Voronezh'),
       '1106' => __('Grozny'),
       '54' => __('Ekaterinburg'),
       '5' => __('Ivanovo'),
       '63' => __('Irkutsk'),
       '41' => __('Yoshkar-ola'),
       '43' => __('Kazan'),
       '22' => __('Kaliningrad'),
       '64' => __('Kemerovo'),
       '7' => __('Kostroma'),
       '35' => __('Krasnodar'),
       '62' => __('Krasnoyarsk'),
       '53' => __('Kurgan'),
       '8' => __('Kursk'),
       '9' => __('Lipetsk'),
       '28' => __('Makhachkala'),
       '23' => __('Murmansk'),
       '1092' => __('Nazran'),
       '30' => __('Nalchik'),
       '47' => __('Nizhniy Novgorod'),
       '65' => __('Novosibirsk'),
       '66' => __('Omsk'),
       '10' => __('Eagle'),
       '48' => __('Orenburg'),
       '49' => __('Penza'),
       '50' => __('Perm'),
       '25' => __('Pskov'),
       '39' => __('Rostov-on-Don'),
       '11' => __('Ryazan'),
       '51' => __('Samara'),
       '42' => __('Saransk'),
       '2' => __('Saint-Petersburg'),
       '12' => __('Smolensk'),
       '239' => __('Sochi'),
       '36' => __('Stavropol'),
       '10649' => __('Stary Oskol'),
       '973' => __('Surgut'),
       '13' => __('Tambov'),
       '14' => __('Tver'),
       '67' => __('Tomsk'),
       '15' => __('Tula'),
       '195' => __('Ulyanovsk'),
       '172' => __('Ufa'),
       '76' => __('Khabarovsk'),
       '45' => __('Cheboksary'),
       '56' => __('Chelyabinsk'),
       '1104' => __('Cherkessk'),
       '16' => __('Yaroslavl'),
   ]), null, ['class' => 'custom-select rounded-0', 'id' => 'region']) !!}
</div>

<div class="form-group required">
    <label>{{ __('Top 10/20') }}</label>
    {!! Form::select('count', array_unique([
        '10' => 10,
        '20' => 20,
        '30' => 30,
    ]), null, ['class' => 'custom-select rounded-0', 'id' => 'count']) !!}
</div>

<div class="form-group required">
    <label>{{ __('Phrases') }}</label>
    {!! Form::textarea('phrases', null, ['class' => 'form-control', 'required', 'id'=>'phrases'] ) !!}
</div>

<div class="form-group required">
    <label>{{ __('clustering level') }}</label>
    {!! Form::select('clustering_level', [
        'light' => 'light - 40%',
        'soft' => 'soft - 50%',
        'hard' => 'hard - 70%',
        ], null, ['class' => 'custom-select rounded-0', 'id' => 'clusteringLevel']) !!}
</div>

<div class="form-group required">
    <label>Объединение кластеров</label>
    {!! Form::select('engine_version', [
        'old' => 'Формирование на основе первой попавшейся фразы (old)',
        'new' => 'Формирование на основе массива ссылок кластера (new)',
        ], null, ['class' => 'custom-select rounded-0', 'id' => 'engineVersion']) !!}
</div>

<div class="form-group required">
    <label>Сохранить результат</label>
    <span class="__helper-link ui_tooltip_w">
            <i class="fa fa-question-circle" style="color: grey"></i>
            <span class="ui_tooltip __right">
                <span class="ui_tooltip_content" style="width: 300px">
                Если вы сохраняете результаты, тогда вы сможете посмотреть результаты во вкладке "мои проекты" <br><br>
                Если вы не сохраняете результаты, тогда вы сможете посмотреть результат только по завершению анализа,
                при запуске следующего анализа изи перезагрузке страницы данные будут утеряны
                </span>
            </span>
        </span>
    {!! Form::select('save', [
        '1' => 'Сохранить',
        '0' => 'Не сохранять',
        ], null, ['class' => 'custom-select rounded-0', 'id' => 'save']) !!}
</div>

<div class="form-group required">
    <div>
        <label for="searchBased">Анализ базовой частотности</label>
        <input type="checkbox" name="searchBased" id="searchBased" checked disabled>
    </div>
    <div>
        <label for="searchPhrases">Анализ фразовой частотности</label>
        <input type="checkbox" name="searchPhrases" id="searchPhrases">
    </div>
    <div>
        <label for="searchTarget">Анализ точной частотности</label>
        <input type="checkbox" name="searchTarget" id="searchTarget">
    </div>
</div>

<input type="button" class="btn btn-secondary" id="start-analysis" data-dismiss="modal" value="{{ __('Analysis') }}">
