@component('component.card', ['title' =>  __('Create page analysis tasks') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/relevance-analysis/css/style.css') }}"/>
    @endslot
    <div id="toast-container" class="toast-top-right success-message" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message"
                 id="toast-message">{{ __('Your tasks have been successfully added to the queue') }}</div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right error-message empty" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message error-message"
                 id="toast-message">{{ __('Something went wrong, try again later.') }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('relevance-analysis') }}">{{ __('Analyzer') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('create.queue.view') }}">
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
                        <a class="nav-link" href="{{ route('all.relevance.projects') }}">{{ __('Statistics') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('show.config') }}">{{ __('Module administration') }}</a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="col-6 pt-3 pb-3">
                        <label for="params">{{ __('Landing pages and links') }}</label>
                        <textarea name="params" id="params" cols="30"
                                  rows="10" class="form form-control"
                                  placeholder="{{ __('Keyword') }};{{ __('Your landing page') }}{{ "\n" }}{{ __('Keyword') }};{{ __('Your landing page') }}"></textarea>
                        <span class="text-muted mb-3">{{ __('How it works') }}
                        <span class="__helper-link ui_tooltip_w">
                                <i class="fa fa-question-circle"></i>
                                <span class="ui_tooltip __bottom ">
                                    <span class="ui_tooltip_content">
                                        {{ __('You need to enter the data in the format:') }}
                                        <br><br>
                                        {{ __('Keyword;Link to landing page') }}<br>
                                        {{ __('Keyword 2;Link to landing page2;') }}
                                        <br><br>

                                        {{ __('And change the configuration of the form as you need.') }} <br>
                                        {{ __("After clicking the 'Add to Queue' button, your tasks will be placed in a queue, which consists of a queue of your tasks and the tasks of other users.") }}
                                        {{ __('When the turn comes to you, your projects will be analyzed and placed on the history page.') }}
                                        <a href="{{ route('relevance.history') }}" target="_blank">тут</a>.
                                    </span>
                                </span>
                            </span>
                        </span>

                        <div class="form-group required pt-3">
                            <label>{{ __('Top 10/20') }}</label>
                            {!! Form::select('count', array_unique([
                                    $config->count_sites => $config->count_sites,
                                    '10' => 10,
                                    '20' => 20,
                                    ]), null, ['class' => 'custom-select rounded-0 count']) !!}
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
                                   ]), null, ['class' => 'custom-select rounded-0 region']) !!}
                        </div>

                        <div class="form-group required" id="ignoredDomainsBlock">
                            <label id="ignoredDomains">{{ __('Ignored domains') }}</label>
                            {!! Form::textarea("ignoredDomains", $config->ignored_domains,["class" => "form-control ignoredDomains"] ) !!}
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
                        </div>

                        <input type="hidden" name="countTasks" id="countTasks" value="1">
                        <input type="hidden" name="type" id="type" value="phrase">
                    </div>

                    <div class="d-flex flex-column">
                        <div class="btn-group col-lg-3 col-md-5 mb-2">
                            <input type="submit" class="btn btn-secondary" id="add-in-queue" value="Добавить в очередь">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @slot('js')
        <script>
            $('#add-in-queue').click(() => {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('create.queue') }}",
                    data: {
                        params: $('#params').val(),
                        type: 'phrase',
                        separator: $('#separator').val(),
                        link: $('.form-control.link').val(),
                        listWords: $('.form-control.listWords').val(),
                        count: $('.custom-select.rounded-0.count').val(),
                        region: $('.custom-select.rounded-0.region').val(),
                        ignoredDomains: $('.form-control.ignoredDomains').val(),

                        noIndex: $('#switchNoindex').is(':checked'),
                        hiddenText: $('#switchAltAndTitle').is(':checked'),
                        switchMyListWords: $('#switchMyListWords').is(':checked'),
                        conjunctionsPrepositionsPronouns: $('#switchConjunctionsPrepositionsPronouns').is(':checked')
                    },
                    success: function () {
                        $('#params').val('')
                        $('.toast-top-right.success-message').show(300)
                        setTimeout(() => {
                            $('.toast-top-right.success-message').hide(300)
                        }, 3500)
                    },
                    error: function () {
                        $('#params').val('')
                        $('.toast-top-right.error-message.empty').show(300)
                        setTimeout(() => {
                            $('.toast-top-right.error-message.empty').hide(300)
                        }, 3500)
                    }
                });
            })
        </script>
    @endslot
@endcomponent
