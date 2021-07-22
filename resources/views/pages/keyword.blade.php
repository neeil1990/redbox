@component('component.card', ['title' => __('Keyword generator')])

    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}" />
    @endslot

    <div id="keyword-generator">

        <div class="listContainer">
            <div class="addList">
                <div class="generatorAddPluse">+</div>
                <div class="generatorAddText">Добавить<br>список слов</div>
            </div>
        </div>

        <div class="popup popup-result-content">
            <p><a>Оставить фразы, содержащие </a><input class="filter_word" type="text" size="50" style="width: 100%;"/></p>
            <p><a class="generatedCountText">Получено фраз: <a class="generatedCount"></a></a><br></p>
            <p><textarea title="" cols="50" rows="23" class="result_word_generator" readonly></textarea></p>
            <button type="button" class="save-result-word-generator ui_btn __accent"><i class="fa fa-save"></i>
                Сохранить
            </button>
            <button type="button" class="copy-result-word-generator ui_btn"><i class="fa fa-copy"></i> Скопировать
            </button>
        </div>
        <div class="optionsHeader">
            <a href="#" class="arrow arrowDown additionalGlobalOptions __dashed">Дополнительные настройки</a></div>
        <br>
        <div class="globalOptions">
            <div class="globalOptionsList">
                <label class="ui_label __no-select">
                    <span class="ui_checkbox">
                        <input type="checkbox" class="globalCheckboxOption ui_checkbox_input"
                               value="surroundWithQuotes"/>
                        <span class="ui_checkbox_fake-input"></span>
                    </span>
                    Заключить в &quot; &quot;
                </label>

                <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                    <span class="ui_tooltip __right __l">
                        <span class="ui_tooltip_content">
                            Оператор фразового соответствия.<br /> В&nbsp;Яндекс.Директе и&nbsp;Google AdWords работает <nobr>по-разному</nobr>.
                        </span>
                    </span>
                </span>
                <br>

                <label class="ui_label __no-select">
                    <span class="ui_checkbox">
                        <input type="checkbox" class="globalCheckboxOption ui_checkbox_input"
                               value="surroundWithBrackets"/>
                        <span class="ui_checkbox_fake-input"></span>
                    </span>
                    Заключить в&nbsp;&laquo;[ ]&raquo;
                </label>
                <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                    <span class="ui_tooltip __right __l">
                        <span class="ui_tooltip_content">
                            В&nbsp;Яндекс.Директе фиксирует порядок слов с&nbsp;учетом словоформ и&nbsp;<nobr>стоп-слов</nobr>.
                            В&nbsp;Google AdWords ограничивает показы ключевым словом и&nbsp;его близкими вариантами.
                        </span>
                    </span>
                </span>
                <br>

                <label class="ui_label __no-select">
                    <span class="ui_checkbox">
                        <input type="checkbox" class="globalCheckboxOption ui_checkbox_input" value="addToResult"/>
                        <span class="ui_checkbox_fake-input"></span>
                    </span>
                    Добавить комбинации без операторов
                </label>
                <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                    <span class="ui_tooltip __right">
                        <span class="ui_tooltip_content">
                            К&nbsp;комбинациям с &quot; &quot; или &laquo;[ ]&raquo; добавляются варианты без операторов.
                        </span>
                    </span>
                </span>
                <br>

                <label class="ui_label __no-select">
                    <span class="ui_checkbox">
                        <input type="checkbox" class="globalCheckboxOption ui_checkbox_input" value="addPlus"/>
                        <span class="ui_checkbox_fake-input"></span>
                    </span>
                    Добавить &laquo;+&raquo; к&nbsp;стоп-словам
                </label>
                <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                    <span class="ui_tooltip __top">
                        <span class="ui_tooltip_content">
                            Позволяет учитывать <nobr>стоп-слова</nobr> в&nbsp;Яндекс.Директе.
                        </span>
                    </span>
                </span>
                <br>

                <label class="ui_label __no-select">
                    <span class="ui_checkbox">
                        <input type="checkbox" class="globalCheckboxOption ui_checkbox_input"
                               value="getAllPhrasesByLength"/>
                        <span class="ui_checkbox_fake-input"></span>
                    </span>
                    Разбить на&nbsp;фразы длиной от
                    <select class="from-words">
                        <option value="1">1</option>
                        <option value="2" selected>2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                    </select>
                    до
                    <select class="to-words">
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7" selected>7</option>
                    </select>
                    слов. Обрезать
                    <select class="left-right">
                        <option value="right" selected>справа</option>
                        <option value="left">слева</option>
                        <option value="both">с&nbsp;обеих сторон</option>
                    </select>
                </label>

                <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                    <span class="ui_tooltip __top">
                        <span class="ui_tooltip_content">
                            Каждая фраза из&nbsp;скомбинированных делится на&nbsp;более мелкие с&nbsp;указанным количеством слов. Лишние слова обрезаются.
                        </span>
                    </span>
                </span>

            </div>
        </div>
        <button type="button" class="get ui_btn __accent">Получить комбинации</button>
        <small>Получится комбинаций:</small>
        <small class="combinationsQuantity"></small>

        <span class="__helper-link ui_tooltip_w">
            <i class="fa fa-question-circle"></i>
            <span class="ui_tooltip __right __l">
                <span class="ui_tooltip_content">
                    При выборе &laquo;Разбить на&nbsp;фразы&nbsp;...&raquo; количество
                    комбинаций может измениться.
                </span>
            </span>
        </span>
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
