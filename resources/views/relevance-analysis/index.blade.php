@component('component.card', ['title' =>  __('Relevance analysis') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/relevance-analysis/css/style.css') }}"/>
        <style>
            #tab_1 > div.d-flex.flex-column > div:nth-child(3) > button.btn.btn-secondary.col-2 > span > span > span,
            #tab_1 > div.d-flex.flex-column > div:nth-child(2) > button.btn.btn-secondary.col-2 > span > span > span,
            #tab_1 > div.d-flex.flex-column > div:nth-child(1) > button.btn.btn-secondary.col-2 > span > span > span {
                width: 400px;
            }
        </style>
    @endslot
    <div id="toast-container" class="toast-top-right error-message empty" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message error-message" id="toast-message"></div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right success-message lock-word" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message" id="lock-word"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('relevance-analysis') }}">{{ __('Analyzer') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('create.queue.view') }}">
                        {{ __('Create page analysis tasks') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('relevance.history') }}">{{ __('History') }}</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('sharing.view') }}" class="nav-link">{{ __('Share your projects') }}</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('access.project') }}" class="nav-link">{{ __('Projects available to you') }}</a>
                </li>
                @if($admin)
                    <li class="nav-item">
                        <a class="nav-link admin-link"
                           href="{{ route('all.relevance.projects') }}">{{ __('Statistics') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link admin-link"
                           href="{{ route('show.config') }}">{{ __('Module administration') }}</a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="col-5 pb-3">
                        <div class="form-group required">
                            <label>{{ __('Your landing page') }}</label>
                            {!! Form::text("link", null ,["class" => "form-control link", "required"]) !!}
                        </div>

                        <div class="form-group required">
                            <label>{{ __('?????? ????????????????') }}</label>
                            {!! Form::select('type', [
                                'phrase' => __('Keyword'),
                                'list' => __('List of scanned sites'),
                                ], null, ['class' => 'custom-select rounded-0', 'id' => 'check-type']) !!}
                        </div>

                        <div class="form-group required">
                            <label>{{ __('Keyword') }}</label>
                            {!! Form::text("phrase", null ,["class" => "form-control phrase", "required"]) !!}
                        </div>

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
                                   ]), null, ['class' => 'custom-select rounded-0 region']) !!}
                        </div>


                        <div id="site-list" style="display: none">
                            <div class="form-group required">
                                <label>{{ __('List of scanned sites') }}</label>
                                {!! Form::textarea("siteList", null ,["class" => "form-control", 'id'=>'siteList'] ) !!}
                            </div>
                        </div>

                        <div id="key-phrase">

                            <div class="form-group required">
                                <label>{{ __('Top 10/20') }}</label>
                                {!! Form::select('count', array_unique([
                                        $config->count_sites => $config->count_sites,
                                        '10' => 10,
                                        '20' => 20,
                                        ]), null, ['class' => 'custom-select rounded-0 count']) !!}
                            </div>

                            <div class="form-group required" id="ignoredDomainsBlock">
                                <label id="ignoredDomains">{{ __('Ignored domains') }}</label>
                                {!! Form::textarea("ignoredDomains", $config->ignored_domains,["class" => "form-control ignoredDomains"] ) !!}
                            </div>
                        </div>

                        <div class="form-group required d-flex align-items-center">
                            <span>{{ __('Cut the words shorter') }}</span>
                            <input type="number" class="form form-control col-2 ml-1 mr-1" name="separator"
                                   id="separator"
                                   value="{{ $config->separator }}">
                            <span>{{ __('symbols') }}</span>
                        </div>

                        <div class="switch mt-3 mb-3">
                            <div class="d-flex">
                                <div class="__helper-link ui_tooltip_w">
                                    <div
                                        class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="switchNoindex"
                                               name="noIndex"
                                               @if($config->noindex == 'yes') checked @endif>
                                        <label class="custom-control-label" for="switchNoindex"></label>
                                    </div>
                                </div>
                                <p>{{ __('Track the text in the noindex tag') }}</p>
                            </div>
                            <div class="d-flex">
                                <div class="__helper-link ui_tooltip_w">
                                    <div
                                        class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="switchAltAndTitle"
                                               name="hiddenText"
                                               @if($config->meta_tags == 'yes') checked @endif>
                                        <label class="custom-control-label" for="switchAltAndTitle"></label>
                                    </div>
                                </div>
                                <p>{{ __('Track words in the alt, title, and data-text attributes') }}</p>
                            </div>
                            <div class="d-flex">
                                <div class="__helper-link ui_tooltip_w">
                                    <div
                                        class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="switchConjunctionsPrepositionsPronouns"
                                               name="conjunctionsPrepositionsPronouns"
                                               @if($config->parts_of_speech == 'yes') checked @endif>
                                        <label class="custom-control-label"
                                               for="switchConjunctionsPrepositionsPronouns"></label>
                                    </div>
                                </div>
                                <p>{{ __('Track conjunctions, prepositions, pronouns') }}</p>
                            </div>
                            <div class="d-flex">
                                <div class="__helper-link ui_tooltip_w">
                                    <div
                                        class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="switchMyListWords"
                                               name="switchMyListWords"
                                               @if($config->remove_my_list_words == 'yes') checked @endif>
                                        <label class="custom-control-label" for="switchMyListWords"></label>
                                    </div>
                                </div>
                                <span>{{ __('Exclude') }}<span
                                        class="text-muted">{{ __('(your own list of words)') }}</span></span>
                            </div>
                            <div class="form-group required list-words mt-1"
                                 @if($config->remove_my_list_words == 'no') style="display:none;" @endif >
                                {!! Form::textarea('listWords', $config->my_list_words,['class' => 'form-control listWords', 'cols' => 8, 'rows' => 5]) !!}
                            </div>
                            <div class="d-flex mt-3" @if(!$admin) style="display: none" @endif>
                                <div class="__helper-link ui_tooltip_w">
                                    <div
                                        class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="exp"
                                               name="exp">
                                        <label class="custom-control-label" for="exp"></label>
                                    </div>
                                </div>
                                <p>{{ __('Experimental mode') }} </p>
                                <span class="__helper-link ui_tooltip_w">
                                    <i class="fa fa-question-circle" style="color: grey"></i>
                                    <span class="ui_tooltip __bottom">
                                        <span class="ui_tooltip_content" style="width: 300px">
                                            ???????????????????? ?????????? ???????????? 50???? ???????????? ??????????????????????<br>
                                            <span class="text-primary">???????????????? ???????????? ??
                                                <b>"???????????? ????????????????"</b> <br>
                                                ?? ?? ?????????? ???????????????? <b>"???????????????? ??????????"</b>
                                            </span>
                                            <br>
                                            <span class="text-primary">???????? ???????????? ???????????????? ???????????? ??????????????</span>
                                        </span>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column">
                        <div class="btn-group col-lg-3 col-md-5 mb-2">
                            <button class="btn btn-secondary" id="full-analyse">
                                {{ __('Full analysis') }}
                            </button>
                            <button type="button" class="btn btn-secondary col-2">
                                <span class="__helper-link ui_tooltip_w">
                                    <i class="fa fa-question-circle"></i>
                                    <span class="ui_tooltip __right">
                                        <span class="ui_tooltip_content">
                                            {{ __('A survey of the xml service will be conducted in order to get the relevant top sites of competitors. The landing page will also be parsed.') }} <br>
                                            {{ __('Based on all the data received, an analysis will be performed.') }} <br>
                                        </span>
                                    </span>
                                </span>
                            </button>
                        </div>
                        <div class="btn-group col-lg-3 col-md-5 mb-2">
                            <button type="button" class="btn btn-secondary" id="repeat-relevance-analyse" disabled>
                                {{ __('Repeated analysis of competitor sites') }}
                            </button>
                            <button type="button" class="btn btn-secondary col-2">
                                <span class="__helper-link ui_tooltip_w">
                                    <i class="fa fa-question-circle"></i>
                                    <span class="ui_tooltip __right">
                                        <span class="ui_tooltip_content">
                                            {{ __('Updating the content of competitors that was received as a result of the last request') }}
                                        </span>
                                    </span>
                                </span>
                            </button>
                        </div>
                        <div class="btn-group col-lg-3 col-md-5 mb-2">
                            <button class="btn btn-secondary" id="repeat-main-page-analyse" disabled>
                                {{ __('Repeated analysis of the landing page') }}
                            </button>
                            <button type="button" class="btn btn-secondary col-2">
                                <span class="__helper-link ui_tooltip_w">
                                    <i class="fa fa-question-circle"></i>
                                    <span class="ui_tooltip __right">
                                        <span
                                            class="ui_tooltip_content">{{ __('We re-poll the landing page and take data from competitors websites that were received as a result of the last request') }}</span>
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>

                    <div id="progress-bar" style="display: none">
                        <div class="progress-bar mt-3 mb-3" role="progressbar"></div>
                        <span class="text-muted" id="progress-bar-state">???????????????????? ????????????..</span>
                        <img src="/img/1485.gif" alt="preloader_gif" width="20">
                    </div>

                    <div class="pb-3 pt-3 text" style="display:none">
                        <h3>{{ __('Comparing the amount of text') }}</h3>
                        <table class="table table-bordered table-striped dataTable dtr-inline">
                            <thead>
                            <tr>
                                <th class="col-3"></th>
                                <th>{{ __('Average values of competitors') }}</th>
                                <th>{{ __('Landing Page Values') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <b>{{ __('Number of words') }}</b>
                                </td>
                                <td id="avgCountWords"></td>
                                <td id="mainPageCountWords"></td>
                            </tr>
                            <tr>
                                <td><b>{{ __('Number of characters') }}</b></td>
                                <td id="avgCountSymbols"></td>
                                <td id="mainPageCountSymbols"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="pb-3 clouds" style="display:none;">
                        <h3>{{ __('The clouds') }}</h3>
                        <div class="d-flex flex-column pb-3">
                            <button id="tf-idf-clouds" class="btn btn-secondary col-lg-3 col-md-5 mb-3"
                                    style="cursor: pointer">
                                "{{ __('TF-idf clouds of sites from the top and landing page') }}"
                            </button>
                            <div class="tf-idf-clouds" style="display: none">
                                <div class="d-lg-flex mt-4 justify-content-around">
                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('Average tf-idf values of links and competitor text') }}</span>
                                        <div style="height: 350px" id="competitorsTfCloud"
                                             class="generated-cloud"></div>
                                    </div>

                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('TF-idf values of links and landing page text') }}</span>
                                        <div style="height: 350px" id="mainPageTfCloud" class="generated-cloud"></div>
                                    </div>

                                </div>
                                <div class="d-lg-flex mt-4 justify-content-around">

                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('Average tf-idf values of competitors text') }}</span>
                                        <div style="height: 350px" id="competitorsTextTfCloud"
                                             class="generated-cloud"></div>
                                    </div>

                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('TF-idf values of the landing page text') }}</span>
                                        <div style="height: 350px" id="mainPageTextTfCloud"
                                             class="generated-cloud"></div>
                                    </div>

                                </div>
                                <div class="d-lg-flex mt-4 justify-content-around">

                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('Average tf-idf values of competitor links') }}</span>
                                        <div style="height: 350px" id="competitorsLinksTfCloud"
                                             class="generated-cloud"></div>
                                    </div>

                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('TF-idf values of landing page links') }}</span>
                                        <div style="height: 350px" id="mainPageLinksTfCloud"
                                             class="generated-cloud"></div>
                                    </div>

                                </div>
                            </div>
                            <button id="text-clouds" class="btn btn-secondary col-lg-3 col-md-5"
                                    style="cursor: pointer;">
                                {{ __("Clouds of site text from the top and landing page") }}
                            </button>
                            <div class="text-clouds" style=" display: none">
                                <div class="d-lg-flex mt-4 justify-content-around">
                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('Competitors Link Zone') }}</span>
                                        <div style="height: 350px" id="competitorsLinksCloud"
                                             class="generated-cloud"></div>
                                    </div>
                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('The link zone of your page') }}</span>
                                        <div style="height: 350px" id="mainPageLinksCloud"
                                             class="generated-cloud"></div>
                                    </div>
                                </div>
                                <div class="d-lg-flex mt-4 justify-content-around">
                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('Competitors text area') }}</span>
                                        <div style="height: 350px" id="competitorsTextCloud"
                                             class="generated-cloud"></div>
                                    </div>
                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('The text area of your page') }}</span>
                                        <div style="height: 350px" id="mainPageTextCloud" class="generated-cloud"></div>
                                    </div>
                                </div>
                                <div class="d-lg-flex mt-4 justify-content-around">
                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('Competitors Link and Text area') }}</span>
                                        <div style="height: 350px" id="competitorsTextAndLinksCloud"
                                             class="generated-cloud"></div>
                                    </div>
                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('The zone of links and text of your page') }}</span>
                                        <div style="height: 350px" id="mainPageTextWithLinksCloud"
                                             class="generated-cloud"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="rec" style="display: none" class="mb-3">
                        <h2>{{ __('TLP Recommendations and Spam check') }}</h2>
                        <button class="btn btn-secondary" id="recButton">{{ __('Show') }}</button>
                    </div>

                    <div class="pb-3 recommendations" style="display:none;">
                        <table id="recommendations" class="table table-bordered table-hover dataTable dtr-inline"
                               style="width: 100% !important;">
                            <thead>
                            <tr style="position: relative; z-index: 100">
                                <th class="??ol-1">
                                    <span class="text-muted" style="font-weight: 400">
                                        {{ __("You can delete a word from the table if it has been worked out") }}
                                    </span>
                                </th>
                                <th>{{ __('Word') }}</th>
                                <th>Tf</th>
                                <th>{{ __('Average number of repetitions of competitors') }}</th>
                                <th>{{ __('The number you have on the page') }}</th>
                                <th>{{ __('Recommended range') }}</th>
                                <th>{{ __('Spam level') }}</th>
                                <th>{{ __('Add') }}</th>
                                <th>{{ __('Remove') }}</th>
                            </tr>
                            </thead>
                            <tbody id="recommendationsTBody">
                            </tbody>
                        </table>
                    </div>

                    <div class="pb-3 unigram" style="display: none; margin-top: 50px">
                        <h2>{{ __('Top list of phrases (TLP)') }}</h2>
                        <table id="unigram" class="table table-bordered table-hover dataTable dtr-inline"
                               style="width: 100% !important;">
                            <thead>
                            <tr>
                                <th></th>
                                <th class="font-weight-normal text-muted">{{ __('Ranges for filtering the table') }}</th>
                                <th>
                                    <div style="width: 90px">
                                        <input class="w-100" type="number" name="minTF" id="minTF" placeholder="min">
                                        <input class="w-100" type="number" name="maxTF" id="maxTF" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div style="width: 90px">
                                        <input class="w-100" type="number" name="minIdf" id="minIdf" placeholder="min">
                                        <input class="w-100" type="number" name="maxIdf" id="maxIdf" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minInter" id="minInter"
                                               placeholder="min">
                                        <input class="w-100" type="number" name="maxInter" id="maxInter"
                                               placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minReSpam" id="minReSpam"
                                               placeholder="min">
                                        <input class="w-100" type="number" name="maxReSpam" id="maxReSpam"
                                               placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minAVG" id="minAVG" placeholder="min">
                                        <input class="w-100" type="number" name="maxAVG" id="maxAVG" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minAVGText" id="minAVGText"
                                               placeholder="min">
                                        <input class="w-100" type="number" name="maxAVGText" id="maxAVGText"
                                               placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minInYourPage" id="minInYourPage"
                                               placeholder="min">
                                        <input class="w-100" type="number" name="maxInYourPage" id="maxInYourPage"
                                               placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minTextIYP" id="minTextIYP"
                                               placeholder="min">
                                        <input class="w-100" type="number" name="maxTextIYP" id="maxTextIYP"
                                               placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minAVGLink" id="minAVGLink"
                                               placeholder="min">
                                        <input class="w-100" type="number" name="maxAVGLink" id="maxAVGLink"
                                               placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minLinkIYP" id="minLinkIYP"
                                               placeholder="min">
                                        <input class="w-100" type="number" name="maxLinkIYP" id="maxLinkIYP"
                                               placeholder="max">
                                    </div>
                                </th>
                            </tr>
                            <tr style="position: relative; z-index: 100">
                                <th></th>
                                <th>
                                    {{ __('Words') }}
                                    <span class="__helper-link ui_tooltip_w">
                                    <i class="fa fa-question-circle"></i>
                                    <span class="ui_tooltip __right">
                                        <span class="ui_tooltip_content" style="text-align: right">{{ __('Words and their word forms that are present on competitors websites.') }}
                                        </span>
                                    </span>
                                </span>
                                </th>
                                <th>Tf<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The weight of the phrase relative to others.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>Idf<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The weight of the phrase relative to others.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>
                                    {{ __('Intersection') }}<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The number of sites in which the word is present.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>{{ __('Re - spam') }}<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The maximum number of repetitions found on the competitors website.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>{{ __('Average number of repetitions in the text and links') }}<span
                                        class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The average value of the number of repetitions in the text and links of your competitors.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>{{ __('The total number of repetitions in the text and links') }}<span
                                        class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The total number of repetitions on your page in links and text.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>{{ __('Average number of repetitions in the text') }}<span
                                        class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The average value of the number of repetitions in the text of your competitors.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>{{ __('Number of repetitions in text') }}<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The number of repetitions in the text on your page.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>{{ __('Average number of repetitions in links') }}<span
                                        class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The average value of the number of repetitions in the links of your competitors.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>{{ __('Number of repetitions in links') }}<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The number of repetitions in the links on your page.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="unigramTBody">
                            </tbody>
                        </table>
                    </div>

                    <div class="phrases" style="display:none;">
                        <h3>{{ __('Top list of phrases (TLPs)') }}</h3>
                        <table id="phrases" class="table table-bordered table-hover dataTable dtr-inline w-100">
                            <thead>
                            <tr>
                                <th class="font-weight-normal text-muted">{{ __('Ranges for filtering the table') }}</th>
                                <th>
                                    <div style="width: 90px">
                                        <input class="w-100" type="number" id="phrasesMinTF" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxTF" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div style="width: 90px">
                                        <input class="w-100" type="number" id="phrasesMinIdf" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxIdf" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinInter" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxInter" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinReSpam" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxReSpam" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinAVG" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxAVG" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinAVGText" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxAVGText" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinInYourPage"
                                               placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxInYourPage"
                                               placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinTextIYP" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxTextIYP" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinAVGLink" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxAVGLink" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinLinkIYP" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxLinkIYP" placeholder="max">
                                    </div>
                                </th>
                            </tr>
                            <tr style="position: relative; z-index: 100;">
                                <th>{{ __('Phrase') }}</th>
                                <th>tf</th>
                                <th>idf</th>
                                <th>{{ __('Intersection') }}</th>
                                <th>{{ __('Re - spam') }}</th>
                                <th>{{ __('Average number of repetitions in the text and links') }}</th>
                                <th>{{ __('The total number of repetitions in the text and links') }}</th>
                                <th>{{ __('Average number of repetitions in the text and links') }}</th>
                                <th>{{ __('The number of repetitions in the text on your page.') }}</th>
                                <th>{{ __('Average number of repetitions in links') }}</th>
                                <th>{{ __('Number of repetitions in links') }}</th>
                            </tr>
                            </thead>
                            <tbody id="phrasesTBody">
                            </tbody>
                        </table>
                    </div>

                    <div class="sites" style="display:none; margin-top:50px;">
                        <h3>{{ __('Analyzed sites') }}</h3>
                        <table id="scanned-sites" class="table table-bordered table-hover dataTable dtr-inline w-100">
                            <thead>
                            <tr id="scanned-sites-row" style="position: relative; z-index: 100">
                                <th>{{ __('Position in the top') }}</th>
                                <th>{{ __('Domain') }}</th>
                                <th>
                                    {{ __('Total score') }}
                                    @if($admin)
                                        <span class="__helper-link ui_tooltip_w">
                                            <i class="fa fa-question-circle" style="color: grey"></i>
                                            <span class="ui_tooltip __bottom">
                                                <span class="ui_tooltip_content" style="width: 300px">
                                                    ?????????? ???????? ???????????????????????????? ?????????????????? ??????????????: ?????????? ???? ???????????? ???????????? + ?????????? ???? tf + ??????????????????<br>
                                                    ???????????????????? ?????????? ?????????????? ?????????????? ???? 3, ?????????? ???????????????????? ???? 2<br>
                                                    - <br>
                                                    ???????? ???????????????????? ??????-???? ???????????? ???????????? 100, ???? ???? ???????????????????????? ?????? ?? 100.<br>
                                                    <span class="text-primary">?????? ?????????????????? ?????????? ???????????? ??????????????</span>
                                                </span>
                                            </span>
                                        </span>
                                    @endif
                                </th>
                                <th>{{ __('coverage for all important words') }}
                                    @if($admin)
                                        <span class="__helper-link ui_tooltip_w">
                                        <i class="fa fa-question-circle" style="color: grey"></i>
                                        <span class="ui_tooltip __bottom">
                                            <span class="ui_tooltip_content" style="width: 300px">
                                                ???? ?????????????? ?????????????? ?????????????? ?????? ?????????? (?????????? ?????? ?????????? ?????????????????? "???????????? ??????????") <br>
                                                ?????? ?????????????? ???????????????? ?????????????? ?????????? ???????????????????? ???????????????? ?????????????? ?? ?????? ????????, ?????????????? ?????????????????? ?????????????? <br>
                                                ???????? ???????????? ?????????? ?????????????????????? ?? ?????????????????????? ??????????, ???? ???? ???????????????? ???? ???????? 1 ????????<br>
                                                ?????????????????? ?????????????? ?????????? ?????????? ?????????????????? ???????????? ???????????????? ???? 600
                                                <br>
                                                <span class="text-primary">?????? ?????????????????? ?????????? ???????????? ??????????????</span>
                                            </span>
                                        </span>
                                    </span>
                                    @endif
                                </th>
                                <th>{{ __('Coverage by tf') }}
                                    @if($admin)
                                        <span class="__helper-link ui_tooltip_w">
                                        <i class="fa fa-question-circle" style="color: grey"></i>
                                        <span class="ui_tooltip __bottom">
                                            <span class="ui_tooltip_content" style="width: 300px">
                                                ???? ?????????????? ?????????????? ?????????????? ?????? ?????????? ?? ???? ???????????????? tf(?????????? ?????? ?????????? ?????????????????? "???????????? ??????????") <br>
                                                ?????? ?????????????? ???????????????? ?????????????? ?????????? ???????????????????? ???????????????? ?????????????? ?? ?????? ????????, ?????????????? ?????????????????? ?????????????? <br>
                                                ???????? ???????????? ?????????? ?????????????????????? ?? ?????????????????????? ??????????, ???? ???? ???????????????? ???? ???????? ???????? ???????????? tf ???? ?????????????? ?????????????? <br>
                                                ?????????? ?????????? ???????????? ?????????????? ?????????????????????? ?????????? ???????????????? ???? ?????????? ?????????? tf ???? ?????????????? ??????????????, ?????????? ?????????????? ???? ???????????????? % ????????????
                                                <br>
                                                <span class="text-primary">?????? ?????????????????? ?????????? ???????????? ??????????????</span>
                                            </span>
                                        </span>
                                    </span>
                                    @endif
                                </th>
                                <th>{{ __('Width') }}
                                    @if($admin)
                                        <span class="__helper-link ui_tooltip_w">
                                            <i class="fa fa-question-circle" style="color: grey"></i>
                                            <span class="ui_tooltip __bottom">
                                                <span class="ui_tooltip_content" style="width: 300px">
                                                    ?????? ????????????????????  ????????????, ???????????????? ???????????? 10 ???? ???????????????????????? ???????????? (?????????????? ?? ????????) <br>
                                                    ???? ?????????? ???? ???????? ????????????(%) ?????????????????? ?? ???????????????? ???? 10, ?????? ???????? ?????????? ?????????????? 100% ???????????? <br>
                                                    ?? ???????????????????????? ?? ?????????? 100% ?????? ?????????????? ?????????? ???????????? ????????????????????????????  ???????????????? <br>
                                                    <span class="text-primary">?????? ?????????????????? ?????????? ???????????? ??????????????</span>
                                                </span>
                                            </span>
                                        </span>
                                    @endif
                                </th>
                                <th>
                                    {{ __('Density') }}
                                    @if($admin)
                                        <span class="__helper-link ui_tooltip_w">
                                            <i class="fa fa-question-circle" style="color: grey"></i>
                                            <span class="ui_tooltip __bottom">
                                                <span class="ui_tooltip_content" style="width: 300px">
                                                    ?????????????????? ?????????????????????????? ???? ???????????????? ?????????????? ???? ???????? ?????? ???????????? ???????????????? ??????????. <br>
                                                    ???????? ?? ?????????????? 20, ?? ?? ?????? 5, ???? ?????? 25 ????????????. <br>
                                                    ???????????? ?????? ?????????? ?????? ???????? ???????? ???????????????????????? ?? ?????????????? ???? ?????????? ???????????????????? ????????. <br>
                                                    - <br>
                                                    ???????? ???? ??????????????????????, ???? ???????? ?? ???????? ???????????????? ???? ?????????? ???? ?????????????????? ???????? ????????????, ?????????? ???????????? ???????????????? 100 ???????????? ???? ??????????????????. <br>
                                                    <br>
                                                    <span class="text-primary">?????? ?????????????????? ?????????? ???????????? ??????????????</span>
                                                </span>
                                            </span>
                                        </span>
                                    @endif
                                </th>
                                <th>{{ __('Total number of characters') }}</th>
                                <th>{{ __('Result') }}</th>
                            </tr>
                            </thead>
                            <tbody id="scanned-sites-tbody">
                            </tbody>
                        </table>
                    </div>

                    <div class="pb-3" id="competitorsTfClouds" style="display: none !important;">
                        <div class="align-items-end clouds-div">
                            <button class="btn btn-secondary col-lg-3 col-md-5" id="coverage-clouds-button">
                                {{ __('Clouds of the first 200 important (tf-idf) words from competitors') }}
                            </button>
                        </div>
                        <div style="display: none" id="coverage-clouds" class="pt-2">
                            <div class='d-flex w-100'>
                                <div class='__helper-link ui_tooltip_w'>
                                    <div
                                        class='custom-control custom-switch custom-switch-off-danger custom-switch-on-success'>
                                        <input type='checkbox'
                                               class='custom-control-input'
                                               id='showOrHideIgnoredClouds'>
                                        <label class='custom-control-label' for='showOrHideIgnoredClouds'></label>
                                    </div>
                                </div>
                                <p>{{ __('hide ignored domains') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <input type="hidden" name="hash" id="hiddenHash">
    @slot('js')
        <script src="{{ asset('plugins/canvasjs/js/canvasjs.js') }}"></script>
        <script src="{{ asset('plugins/jqcloud/js/jqcloud-1.0.4.min.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/scriptsV6/renderClouds.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/scriptsV6/renderUnigramTable.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/scriptsV6/renderScannedSitesList.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/scriptsV6/renderTextTable.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/scriptsV6/renderPhrasesTable.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/scriptsV6/renderRecommendationsTable.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script>
            $('#recButton').click(function () {
                if ($('.pb-3.recommendations').is(':visible')) {
                    $('.pb-3.recommendations').hide()
                    $('#recButton').html("{{ __('Show') }}")
                } else {
                    $('.pb-3.recommendations').show()
                    $('#recButton').html("{{ __('Hide') }}")
                }
            });

            $('#check-type').on('change', function () {
                if ($(this).val() === 'list') {
                    $('#key-phrase').hide()
                    $('#site-list').show(300)
                } else {
                    $('#site-list').hide()
                    $('#key-phrase').show(300)
                }
            });

            $('input#switchMyListWords').click(function () {
                if ($(this).is(':checked')) {
                    $('.form-group.required.list-words.mt-1').show(300)
                } else {
                    $('.form-group.required.list-words.mt-1').hide(300)
                }
            })

            window.onbeforeunload = function () {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('remove.page.history') }}",
                    data: {
                        pageHash: window.session,
                    },
                });
            };
        </script>
        <script>
            String.prototype.shuffle = function () {
                var a = this.split(""),
                    n = a.length;

                for (var i = n - 1; i > 0; i--) {
                    var j = Math.floor(Math.random() * (i + 1));
                    var tmp = a[i];
                    a[i] = a[j];
                    a[j] = tmp;
                }
                return a.join("").replaceAll(" ", "");
            }

            window.session = String(new Date()).shuffle();
            localStorage.setItem("session", window.session);

            var onStorage = function (e) {
                if (e.key === 'session' && e.newValue !== window.session)
                    localStorage.setItem("multitab", window.session);
                if (e.key === "multitab" && e.newValue && e.newValue !== window.session) {
                    window.removeEventListener("storage", onStorage);
                    localStorage.setItem("session", localStorage.getItem("multitab"));
                    localStorage.removeItem("multitab");
                }
            };
            window.addEventListener('storage', onStorage);

            var generatedTfIdf = false
            var generatedText = false
            var generatedCompetitorCoverage = false
            var progressInterval

            function endProgress(progressInterval) {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('end.relevance.progress') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        hash: $('#hiddenHash').val()
                    },
                    success: function () {
                        $('#hiddenHash').val('empty')
                        clearInterval(progressInterval)
                    },
                });
            }

            function getProgress() {
                let interval = setInterval(() => {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('get.relevance.progress') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            hash: $('#hiddenHash').val()
                        },
                        success: function (response) {
                            if (response.progress == null) {
                                clearInterval(interval)
                                return stopProgressBar()
                            } else {
                                setProgressBarStyles(response.progress.progress)
                            }
                        },
                    });
                }, 1000);
            }

            function startProgress(type) {
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: "{{ route('start.relevance.progress') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function (response) {
                        $('#hiddenHash').val(response.hash)
                        if (type === 'full') {
                            startAnalyse()
                        } else if (type === 'repeatMainPage') {
                            repeatMainPageAnalyse()
                        } else if (type === 'repeatRelevance') {
                            repeatRelevance()
                        }
                    },
                });
            }

            // ----------------------------

            $('#full-analyse').click(() => {
                startProgress('full')
            })

            $('#repeat-main-page-analyse').click(() => {
                startProgress('repeatMainPage')
            })

            $('#repeat-relevance-analyse').click(() => {
                startProgress('repeatRelevance')
            })

            // ----------------------------

            function startAnalyse() {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('analysis.relevance') }}",
                    data: getData(),
                    beforeSend: function () {
                        refreshAllRenderElements()
                        $('#full-analyse').prop("disabled", true);
                        $('#repeat-main-page-analyse').prop("disabled", true);
                        $('#repeat-relevance-analyse').prop("disabled", true);
                        $("#progress-bar").show(300)
                        getProgress()
                    },
                    success: function (response) {
                        endProgress()
                        successRequest(response)
                    },
                    error: function (response) {
                        endProgress()
                        let message = ''
                        if (response.responseText) {
                            let messages = JSON.parse(response.responseText);
                            $.each(messages['errors'], function (key, value) {
                                message += value + "<br>"
                            });

                            if (message === '') {
                                message = "{{ __('An error has occurred, repeat the request.') }}"
                            }

                            $('.toast-message.error-message').html(message)
                        } else {
                            $('.toast-message.error-message').html("{{ __('An error has occurred, repeat the request.') }}")
                        }

                        $('.toast-top-right.error-message.empty').show(300)
                        setTimeout(() => {
                            $('.toast-top-right.error-message.empty').hide(300)
                        }, 5000)

                        errorRequest()
                    }
                });
            }

            function repeatMainPageAnalyse() {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('repeat.main.page.analysis') }}",
                    data: getData(),
                    beforeSend: function () {
                        refreshAllRenderElements()
                        $('#full-analyse').prop("disabled", true);
                        $('#repeat-main-page-analyse').prop("disabled", true);
                        $('#repeat-relevance-analyse').prop("disabled", true);
                        $("#progress-bar").show(300)
                        getProgress()
                    },
                    success: function (response) {
                        endProgress()
                        successRequest(response)
                    },
                    error: function (response) {
                        endProgress()
                        if (response.responseText) {
                            prepareMessage(response)
                        } else {
                            $('.toast-message.error-message').html("{{ __('An error has occurred, repeat the request.') }}")
                        }

                        $('.toast-top-right.error-message.empty').show(300)
                        setTimeout(() => {
                            $('.toast-top-right.error-message.empty').hide(300)
                        }, 5000)

                        errorRequest()
                    }
                });
            }

            function repeatRelevance() {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('repeat.relevance.analysis') }}",
                    data: getData(),
                    beforeSend: function () {
                        refreshAllRenderElements()
                        $('#full-analyse').prop("disabled", true);
                        $('#repeat-main-page-analyse').prop("disabled", true);
                        $('#repeat-relevance-analyse').prop("disabled", true);
                        $("#progress-bar").show(300)
                        getProgress()
                    },
                    success: function (response) {
                        endProgress()
                        successRequest(response)
                    },
                    error: function (response) {
                        endProgress()
                        if (response.responseText) {
                            prepareMessage(response)
                        } else {
                            $('.toast-message.error-message').html("{{ __('An error has occurred, repeat the request.') }}")
                        }

                        $('.toast-top-right.error-message.empty').show(300)
                        setTimeout(() => {
                            $('.toast-top-right.error-message.empty').hide(300)
                        }, 5000)

                        errorRequest()
                    }
                });
            }

            // ----------------------------

            function successRequest(response) {
                sessionStorage.setItem('hideDomains', response.hide_ignored_domains)
                stopProgressBar()
                renderTextTable(response.avg, response.mainPage)
                renderRecommendationsTable(response.recommendations, response.recommendations_count)
                renderUnigramTable(response.unigramTable, response.ltp_count);
                renderPhrasesTable(response.phrases, response.ltps_count)
                renderScannedSitesList(
                    response.sites,
                    response.avgCoveragePercent,
                    response.scanned_sites_count,
                    response.hide_ignored_domains,
                    response.boostPercent,
                    response.sitesAVG,
                );
                renderClouds(response.clouds.competitors, response.clouds.mainPage, response.tfCompClouds, response.hide_ignored_domains);
                $("#full-analyse").prop("disabled", false);
                $("#repeat-main-page-analyse").prop("disabled", false);
                $("#repeat-relevance-analyse").prop("disabled", false);
            }

            function errorRequest() {
                stopProgressBar()
                $("#full-analyse").prop("disabled", false);
                $("#repeat-main-page-analyse").prop("disabled", false);
                $("#repeat-relevance-analyse").prop("disabled", false);
            }

            function refreshAllRenderElements() {
                $('#recButton').html('????????????????')
                if (generatedCompetitorCoverage) {
                    $('#coverage-clouds-button').trigger('click')
                    if (sessionStorage.getItem('hideDomains') === 'yes') {
                        $("#showOrHideIgnoredClouds").prop("checked", false);
                    }
                }
                generatedTfIdf = false
                generatedText = false
                generatedCompetitorCoverage = false
                $(".generated-cloud").html("")
                $("#clouds").html("")
                $("#recommendations").dataTable().fnDestroy();
                $("#unigram").dataTable().fnDestroy();
                $("#scanned-sites").dataTable().fnDestroy();
                $("#phrases").dataTable().fnDestroy();
                $('.render').remove();
                $('.text').hide()
                $('.unigram').hide()
                $('.sites').hide()
                $('.clouds').hide()
                $('.phrases').hide()
                $('#rec').hide()
                $('.pb-3.recommendations').hide()
                $('#competitorsTfClouds').hide()
            }

            function setProgressBarStyles(percent) {
                let bar = $('.progress-bar')
                bar.css({
                    width: percent + '%'
                })
                bar.html(percent + '%');

                if (percent < 40) {
                    $('#progress-bar-state').html('?????????????? ????????????..')
                } else {
                    $('#progress-bar-state').html('?????????????????? ???????????????????? ????????????..')
                }
            }

            function stopProgressBar() {
                $('#progress-bar-state').html('???????????????????? ????????????..')
                setProgressBarStyles(100)
                setTimeout(() => {
                    $('.progress-bar').css({
                        width: 0 + '%'
                    });
                    $("#progress-bar").hide(300)
                }, 3000)
            }

            function prepareMessage(response) {
                let message = ''
                if (response.responseText) {
                    let messages = JSON.parse(response.responseText);
                    $.each(messages['errors'], function (key, value) {
                        message += value + "<br>"
                    });

                    if (message === '') {
                        message = '?????????????????? ???????????????????????????? ????????????, ???????????????????? ?? ????????????????????????????'
                    }

                    $('.toast-message.error-message').html(message)
                } else {
                    $('.toast-message.error-message').html("{{ __('An error has occurred, repeat the request.') }}")
                }
            }

            function getData() {
                return {
                    pageHash: window.session,
                    type: $('#check-type').val(),
                    hash: $('#hiddenHash').val(),
                    siteList: $('#siteList').val(),
                    separator: $('#separator').val(),
                    link: $('.form-control.link').val(),
                    phrase: $('.form-control.phrase').val(),
                    noIndex: $('#switchNoindex').is(':checked'),
                    listWords: $('.form-control.listWords').val(),
                    count: $('.custom-select.rounded-0.count').val(),
                    region: $('.custom-select.rounded-0.region').val(),
                    hiddenText: $('#switchAltAndTitle').is(':checked'),
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    ignoredDomains: $('.form-control.ignoredDomains').val(),
                    switchMyListWords: $('#switchMyListWords').is(':checked'),
                    conjunctionsPrepositionsPronouns: $('#switchConjunctionsPrepositionsPronouns').is(':checked'),
                    exp: $('#exp').is(':checked')
                }
            }
        </script>
    @endslot
@endcomponent
