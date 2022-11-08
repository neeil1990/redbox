<div id="toast-container" class="toast-top-right success-message dont-worry-notification" style="display:none;">
    <div class="toast toast-info" aria-live="polite">
        <div class="toast-message">
            {{ __("If your analysis is \"hanging\" at 50% for a long time, don't worry, it's just waiting in line to process xml requests river") }}
        </div>
    </div>
</div>

<div id="toast-container" class="toast-top-right success-message history-notification" style="display: none">
    <div class="toast toast-info" aria-live="polite" style="top: 140px;">
        <div class="toast-message">
            {{ __('You can close the page or start a new analysis, when your results are ready, you can view them') }}
            <a href="{{ route('cluster.configuration') }}"><u>{{ __('here') }}</u></a>
        </div>
    </div>
</div>

<div class="form-group required">
    <label>{{ __('Region') }}</label>
    {!! Form::select('region', array_unique([
        $config->region => $config->region,
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
    <label>{{ __('TOP') }}</label>
    {!! Form::select('count', array_unique([
       $config->count => $config->count,
        '10' => 10,
        '20' => 20,
        '30' => 30,
    ]), null, ['class' => 'custom-select rounded-0', 'id' => 'count']) !!}
</div>

<div class="form-group required" id="phrases-form-block">
    <label>{{ __('Key phrases') }}</label>
    {!! Form::textarea('phrases', null, ['class' => 'form-control', 'required', 'id'=>'phrases'] ) !!}
</div>

<div class="form-group required">
    <label>{{ __('clustering level') }}</label>
    {!! Form::select('clustering_level', [
        $config->clustering_level => $config->clustering_level,
        'light' => 'light - 40%',
        'soft' => 'soft - 50%',
        'hard' => 'hard - 70%',
        ], null, ['class' => 'custom-select rounded-0', 'id' => 'clusteringLevel']) !!}
</div>

<div class="form-group required">
    <label>{{ __('Merging Clusters') }}</label>
    {!! Form::select('engine_version', [
            $config->engine_version => $config->engine_version,
            'old' => __('Formation based on the first available phrase (old)'),
            'new' => __('Forming a cluster based on an array of links (new)'),
    ], null, ['class' => 'custom-select rounded-0', 'id' => 'engineVersion']) !!}
</div>

<div class="form-group required">
    <label>{{ __('Save results') }}</label>
    <span class="__helper-link ui_tooltip_w">
            <i class="fa fa-question-circle" style="color: grey"></i>
            <span class="ui_tooltip __right">
                <span class="ui_tooltip_content" style="width: 300px">
                {{ __("If you save the results then you can view the results in the 'my projects' tab") }} <br><br>
                {{ __('If you do not save the results, then you can view the result only after the analysis is completed,') }}
                    {{ __('data will be lost when starting the next analysis or when reloading the page') }}
                </span>
            </span>
        </span>
    {!! Form::select('save', [
        $config->save_results => $config->save_results,
        '1' => __('Save'),
        '0' => __('Do not save'),
        ], null, ['class' => 'custom-select rounded-0', 'id' => 'save']) !!}
</div>

<div class="form-group required" id="extra-block">
    <div class="row">
        <div class="col-6 d-flex flex-column">
            <label for="domain-textarea">{{ __('Domain') }}</label>
            <textarea name="domain-textarea" id="domain-textarea" rows="5" class="form-control w-100"></textarea>
        </div>
        <div class="col-6 d-flex flex-column">
            <label for="comment-textarea">{{ __('Comment') }}</label>
            <textarea name="comment-textarea" id="comment-textarea" rows="5" class="form-control w-100"></textarea>
        </div>
    </div>
    @if(!\Illuminate\Support\Facades\Auth::user()->telegram_bot_active)
        <div class="col-md-6 mt-2">
            <a href="{{ route('profile.index') }}" target="_blank">
                {{ __('Want to') }}  {{ __('receive notifications from our telegram bot') }}
            </a>
        </div>
    @else
        <label for="sendMessage" class="pt-1">{{ __('Notify in a telegram upon completion?') }}</label>
        {!! Form::select('sendMessage', [
            $config->send_message => $config->send_message,
            true => __('Yes'),
            false => __('No'),
        ], null, ['class' => 'custom-select rounded-0', 'id' => 'sendMessage']) !!}
    @endif

</div>

<div class="form-group required">
    <div>
        <label for="searchBased">{{ __('Base frequency analysis') }}</label>
        <input type="checkbox" name="searchBased" id="searchBased" checked disabled>
    </div>
    <div>
        <label for="searchPhrases">{{ __('Phrase frequency analysis') }}</label>
        <input type="checkbox" name="searchPhrases" id="searchPhrases" @if($config->search_phrased) checked @endif>
    </div>
    <div>
        <label for="searchTarget">{{ __('Accurate frequency analysis') }}</label>
        <input type="checkbox" name="searchTarget" id="searchTarget" @if($config->search_target) checked @endif>
    </div>
</div>

<input type="button" class="btn btn-secondary" id="start-analysis" data-dismiss="modal" value="{{ __('Analyse') }}">
