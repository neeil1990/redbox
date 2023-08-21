@component('component.card', ['title' => __('Keyword generator')])

    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}" />

        <style>
            .GeneratorWords {
                background: oldlace;
            }
        </style>
    @endslot

    <div id="keyword-generator">

        <div class="listContainer">
            <div class="addList">
                <div class="generatorAddPluse">+</div>
                <div class="generatorAddText">{{ __('Add') }}<br>{{ __('word list') }}</div>
            </div>
        </div>

        <div class="popup popup-result-content">
            <p><a>{{ __('Leave phrases containing') }} </a><input class="filter_word" type="text" size="50" style="width: 100%;"/></p>
            <p><a class="generatedCountText">{{ __('Phrases received') }}: <a class="generatedCount"></a></a><br></p>
            <p><textarea title="" cols="50" rows="23" class="result_word_generator" readonly></textarea></p>
            <button type="button" class="save-result-word-generator btn btn-secondary click_tracking" data-click="Save">
                <i class="fas fa-save"></i>
                {{ __('Save') }}
            </button>
            <button type="button" class="copy-result-word-generator btn btn-secondary click_tracking" data-click="Copy">
                <i class="fas fa-copy"></i>
                {{ __('Copy') }}
            </button>
        </div>
        <div class="optionsHeader click_tracking" data-click="Additional settings">
            <a href="#" class="arrow arrowDown additionalGlobalOptions __dashed">{{ __('Additional settings') }}</a></div>
        <br>
        <div class="globalOptions">
            <div class="globalOptionsList">
                <label class="ui_label __no-select click_tracking" data-click="Conclude in quotation marks">
                    <span class="ui_checkbox">
                        <input type="checkbox" class="globalCheckboxOption ui_checkbox_input"
                               value="surroundWithQuotes"/>
                        <span class="ui_checkbox_fake-input"></span>
                    </span>
                    {{ __('Conclude in') }} &quot; &quot;
                </label>

                <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                    <span class="ui_tooltip __right __l">
                        <span class="ui_tooltip_content">
                            {{ __('Phrase match operator.') }}<br /> {{ __('Works in Yandex.Direct and Google AdWords differently.') }}
                        </span>
                    </span>
                </span>
                <br>

                <label class="ui_label __no-select click_tracking" data-click="Conclude in staples">
                    <span class="ui_checkbox">
                        <input type="checkbox" class="globalCheckboxOption ui_checkbox_input"
                               value="surroundWithBrackets"/>
                        <span class="ui_checkbox_fake-input"></span>
                    </span>
                    {{ __('Conclude in') }}&nbsp;&laquo;[ ]&raquo;
                </label>
                <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                    <span class="ui_tooltip __right __l">
                        <span class="ui_tooltip_content">
                            {{ __('In Yandex.Direct, it fixes the order of words, taking into account word forms and stop words. In Google AdWords, restricts impressions to a keyword and its related variants.') }}
                        </span>
                    </span>
                </span>
                <br>

                <label class="ui_label __no-select click_tracking" data-click="Add combinations without operators">
                    <span class="ui_checkbox">
                        <input type="checkbox" class="globalCheckboxOption ui_checkbox_input" value="addToResult"/>
                        <span class="ui_checkbox_fake-input"></span>
                    </span>
                    {{ __('Add combinations without operators') }}
                </label>
                <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                    <span class="ui_tooltip __right">
                        <span class="ui_tooltip_content">
                            {{ __('Variants without operators are added to combinations with "" or [].') }}
                        </span>
                    </span>
                </span>
                <br>

                <label class="ui_label __no-select click_tracking" data-click="Add to stop words">
                    <span class="ui_checkbox">
                        <input type="checkbox" class="globalCheckboxOption ui_checkbox_input" value="addPlus"/>
                        <span class="ui_checkbox_fake-input"></span>
                    </span>
                    {{ __('Add "+" to stop words') }}
                </label>
                <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                    <span class="ui_tooltip __top">
                        <span class="ui_tooltip_content">
                            {{ __('Allows you to take into account stop words in Yandex.Direct.') }}
                        </span>
                    </span>
                </span>
                <br>

                <label class="ui_label __no-select click_tracking" data-click="Split into phrases">
                    <span class="ui_checkbox">
                        <input type="checkbox" class="globalCheckboxOption ui_checkbox_input"
                               value="getAllPhrasesByLength"/>
                        <span class="ui_checkbox_fake-input"></span>
                    </span>
                    {{ __('Split into phrases from') }}
                    <select class="from-words">
                        <option value="1">1</option>
                        <option value="2" selected>2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                    </select>
                    {{ __('before') }}
                    <select class="to-words">
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7" selected>7</option>
                    </select>
                    {{ __('Words Crop') }}
                    <select class="left-right">
                        <option value="right" selected>{{ __('right') }}</option>
                        <option value="left">{{ __('left') }}</option>
                        <option value="both">{{ __('at both sides') }}</option>
                    </select>
                </label>

                <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                    <span class="ui_tooltip __top">
                        <span class="ui_tooltip_content">
                            {{ __('Each phrase from the combined is divided into smaller ones with the specified number of words. Extra words are cut off.') }}
                        </span>
                    </span>
                </span>

            </div>
        </div>
        <button type="button" class="get btn btn-secondary click_tracking" data-click="Get combinations">{{ __('Get combinations') }}</button>
        <small>{{ __('You get combinations') }}:</small>
        <small class="combinationsQuantity"></small>

        <span class="__helper-link ui_tooltip_w">
            <i class="fa fa-question-circle"></i>
            <span class="ui_tooltip __right __l">
                <span class="ui_tooltip_content">
                    {{ __('If you select Split into phrases, the number of combinations may change.') }}
                </span>
            </span>
        </span>
    </div>

    <div class="words-localized">
        <input type="hidden" id="Additionally" value="{{__('Additionally')}}">
        <input type="hidden" id="Combine-without" value="{{__('Combine without these words')}}">
        <input type="hidden" id="Variants-without-words" value="{{__('Variants without words from this list are added to combinations from other lists.')}}">
        <input type="hidden" id="Phrases-are-being-generated" value="{{__('Phrases are being generated')}}">
        <input type="hidden" id="This-may-take-some-time" value="{{__('This may take some time.')}}">
        <input type="hidden" id="Words" value="{{__('Words')}}">
        <input type="hidden" id="Leave-phrases" value="{{__('Leave phrases containing')}}">
        <input type="hidden" id="Words-from-this-list" value="{{__('Words from this list will be added, including without combining with others.')}}">
        <input type="hidden" id="Add-source-list" value="{{__('Add source list')}}">
        <input type="hidden" id="Add" value="{{__('Add')}}">
        <input type="hidden" id="Broad-match" value="{{__('Broad match modifier for Google AdWords. A "+" is added before each word.')}}">
        <input type="hidden" id="Use-to-set" value="{{__('Use to set impressions in the specified word form in Yandex.Direct. Before each word a "!" Is added, before the stop words "+".')}}">
        <input type="hidden" id="word-list" value="{{__('Word list')}}">
        <input type="hidden" id="word-placeholder" placeholder="{{ __('Enter or paste keywords, one per line. Blank lines are ignored. To combine all lists except the selected one, click Add combinations without these words') }}" value="">
    </div>

    @slot('js')
        <script type="text/javascript" src="{{ asset('plugins/keyword-generator/js/require.js') }}"></script>
        <script type="text/javascript" src="{{ asset('plugins/keyword-generator/js/require-config.js') }}"></script>

        <script>
            require(['keywordGenerator/word_generator', 'jquery'], function (WordGenerator, $) {
                WordGenerator.keywordGeneratorStart(
                    $('#keyword-generator'),
                    "{{ asset('plugins/keyword-generator/js/apps/keywordGenerator/') }}"
                );
            });
        </script>
    @endslot
@endcomponent
