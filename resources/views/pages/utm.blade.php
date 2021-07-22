@component('component.card', ['title' => __('UTM Marks')])

    @slot('css')
        <link rel='stylesheet' id='swpc-main-css'  href='{{ asset('plugins/utm-marks/css/style.css') }}' type='text/css' media='all' />
    @endslot

    <div class="grid urlBuilder mt-base">
        <div class="grid_c grid_c-1of2">
            <div class="urlBuilder_form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="urlBuilder_step">Шаг 1. Целевая страница</div>
                        <div class="urlBuilder_el urlBuilder_el-url">
                            <div class="urlBuilder_el_label">
                                Введите адрес страницы:
                                <span class="urlBuilder_toogleUrls">Несколько URL</span>
                            </div>
                            <div class="urlBuilder_el_input">
                                <input id="urlBuilderUrl" type="text">					</div>
                            <div class="urlBuilder_el_tip">Пример: https://prime-ltd.su/nashi-servisyi/</div>
                        </div>

                        <div class="urlBuilder_el urlBuilder_el-urls hide">
                            <div class="urlBuilder_el_label">
                                Введите адреса страниц:
                                <span class="urlBuilder_toogleUrls">Один URL</span>
                            </div>
                            <div class="urlBuilder_el_input">
                                <textarea id="urlBuilderUrls"></textarea>
                            </div>
                            <div class="urlBuilder_el_tip">
                                Пример: https://prime-ltd.su/nashi-servisyi/
                                По одному на каждую строку.
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">

                        <div class="urlBuilder_tpl">
                            <div class="urlBuilder_tpl_title">Шаблон:</div>
                            <div class="urlBuilder_tpl_items">
                                <span class="active" data-id="custom">Произвольный</span>
                                <span data-id="direct">Яндекс.Директ</span>
                                <span data-id="adwords">Google AdWords</span>
                                <span data-id="vk">Таргетинг ВКонтакте</span>
                                <span data-id="mailru">myTarget</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="urlBuilder_step">Шаг 2. Основные параметры</div>
                    </div>
                </div>

                <div class="urlBuilder_el">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="urlBuilder_el_label">
                                Источник кампании
                                <i>utm_source</i>
                                <b class="urlBuilder_el_helpToggle" data-toggle-label="Скрыть описание">Показать описание</b>
                            </div>
                        </div>
                    </div>

                    <div class="urlBuilder_el_input">
                        <div class="row">

                            <div class="col-md-6">
                                <input type="text" id="urlBuilderUtmSource">
                            </div>

                            <div class="col-md-4">
                                <div class="urlBuilder_el_input_items">
                                    <span>yandex</span>
                                    <span>google</span>
                                    <span>vk</span>
                                    <span>target-mail</span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="urlBuilder_help">
                        <p>
                            Метка <b class="urlBuilder_help_term">utm_source</b> обозначает источник рекламной кампании.
                            <br>
                            Как правило, используют следующие варианты:
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">yandex</b> — Яндекс.Директ
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">google</b> — Google AdWords
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">vk</b> — Таргетинг ВКонтакте
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">target-mail</b> — myTarget
                        </p>
                    </div>

                </div>

                <div class="urlBuilder_el">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="urlBuilder_el_label">
                                Канал кампании
                                <i>utm_medium</i>
                                <b class="urlBuilder_el_helpToggle" data-toggle-label="Скрыть описание">Показать описание</b>
                            </div>
                        </div>
                    </div>
                    <div class="urlBuilder_el_input">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" id="urlBuilderUtmMedium">
                            </div>

                            <div class="col-md-4">
                                <div class="urlBuilder_el_input_items">
                                    <span>cpc</span>
                                    <span>cpv</span>
                                    <span>cpm</span>
                                    <span>email</span>
                                    <span>banner</span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="urlBuilder_help">
                        <p>
                            Метка <b class="urlBuilder_help_term">utm_medium</b> обозначает используемый канал рекламной
                            кампании (средство
                            маркетинга, канал трафика или способ взаимодействия с клиентом).
                            <br>
                            Как правило, используют следующие варианты:
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">cpc</b> — Реклама с оплатой за клик
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">cpv</b> — Реклама с оплатой за визит
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">cpm</b> — Реклама с оплатой за показы
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">email</b> — Почтовая рассылка
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">banner</b> — Баннер на сайте
                        </p>
                    </div>
                </div>

                <div class="urlBuilder_el">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="urlBuilder_el_label">
                                Название кампании
                                <i>utm_campaign</i>
                                <b class="urlBuilder_el_helpToggle" data-toggle-label="Скрыть описание">Показать описание</b>
                            </div>
                        </div>
                    </div>

                    <div class="urlBuilder_el_input">

                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" id="urlBuilderUtmCampaign">
                            </div>

                            <div class="col-md-4">
                                <div class="urlBuilder_el_input_items">
                                    <span>{campaign_id}</span>
                                    <span>{campaignid}</span>
                                    <span>@{{campaign_id}}</span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="urlBuilder_help">
                        <p>
                            Метка <b class="urlBuilder_help_term">utm_campaign</b> обозначает конкретную рекламную
                            кампанию
                            (например, реклама определенного товара или специальная акция). Можно вписать произвольное
                            значение,
                            либо воспользоваться динамическими параметрами соответствующей рекламной системы:
                            <br>
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">{campaign_id}</b> — ID рекламной кампании в
                            Яндекс.Директ и таргетинге ВКонтакте
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">{campaignid}</b> — ID рекламной кампании в Google
                            Adwords
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">@{{campaign_id}}</b> — ID рекламной кампании в
                            myTarget
                        </p>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="urlBuilder_step">Шаг 3. Дополнительные параметры</div>
                    </div>
                </div>

                <div class="urlBuilder_el">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="urlBuilder_el_label">
                                Содержание кампании
                                <i>utm_content</i>
                                <b class="urlBuilder_el_helpToggle" data-toggle-label="Скрыть описание">Показать описание</b>
                            </div>
                        </div>
                    </div>

                    <div class="urlBuilder_el_input">
                        <div class="row">

                            <div class="col-md-6">
                                <input type="text" id="urlBuilderUtmContent">
                            </div>

                            <div class="col-md-4">
                                <div class="urlBuilder_el_input_items">
                                    <span>{ad_id}</span>
                                    <span>{creative}</span>
                                    <span>@{{banner_id}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="urlBuilder_help">
                        <p>
                            Метка <b class="urlBuilder_help_term">utm_content</b> содержит дополнительную информацию.
                            Может использоваться при A/Б-тестировании или для объявлений с таргетингом.
                            Можно вписать произвольное значение, либо воспользоваться динамическими параметрами
                            соответствующей рекламной системы:
                        </p>
                        <div class="urlBuilder_help_tabs">
                            <div class="urlBuilder_help_tab active" data-tab="yandex">Яндекс.Директ</div>
                            <div class="urlBuilder_help_tab" data-tab="google">Google AdWords</div>
                            <div class="urlBuilder_help_tab" data-tab="vk">Таргетинг ВКонтакте</div>
                            <div class="urlBuilder_help_tab" data-tab="target-mail">myTarget</div>
                        </div>
                        <div class="urlBuilder_help_tabBody urlBuilder_help_tabBody-yandex">
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{ad_id}</b> — Идентификатор объявления</p><p><b class="urlBuilder_help_term active" data-add-term="1">{addphrases}</b> — Инициирован ли этот показ автоматически добавленными фразами</p><p><b class="urlBuilder_help_term active" data-add-term="1">{addphrasestext}</b> — Текст автоматически добавленной фразы</p><p><b class="urlBuilder_help_term active" data-add-term="1">{campaign_type}</b> — Тип кампании</p><p><b class="urlBuilder_help_term active" data-add-term="1">{campaign_id}</b> — Идентификатор рекламной кампании</p><p><b class="urlBuilder_help_term active" data-add-term="1">{device_type}</b> — Тип устройства, на котором произведен показ</p><p><b class="urlBuilder_help_term active" data-add-term="1">{gbid}</b> — Идентификатор группы</p><p><b class="urlBuilder_help_term active" data-add-term="1">{keyword}</b> — Ключевая фраза, по которой было показано объявление</p><p><b class="urlBuilder_help_term active" data-add-term="1">{phrase_id}</b> — Идентификатор ключевой фразы для текстово-графических объявлений или рекламы мобильных приложений</p><p><b class="urlBuilder_help_term active" data-add-term="1">{retargeting_id}</b> — Идентификатор условия ретаргетинга</p><p><b class="urlBuilder_help_term active" data-add-term="1">{adtarget_name}</b> — Условие нацеливания, по которому было показано динамическое объявление</p><p><b class="urlBuilder_help_term active" data-add-term="1">{adtarget_id}</b> — Идентификатор условия нацеливания динамического объявления</p><p><b class="urlBuilder_help_term active" data-add-term="1">{position}</b> — Точная позиция объявления в блоке</p><p><b class="urlBuilder_help_term active" data-add-term="1">{position_type}</b> — Тип блока, если показ произошел на странице с результатами поиска Яндекса</p><p><b class="urlBuilder_help_term active" data-add-term="1">{source}</b> — Место показа</p><p><b class="urlBuilder_help_term active" data-add-term="1">{source_type}</b> — Тип площадки, на которой произведен показ объявления</p><p><b class="urlBuilder_help_term active" data-add-term="1">{region_name}</b> — Регион, в котором было показано объявление</p><p><b class="urlBuilder_help_term active" data-add-term="1">{region_id}</b> — Идентификатор региона, в котором было показано объявление</p>						</div>
                        <div class="urlBuilder_help_tabBody urlBuilder_help_tabBody-google hide">
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{campaignid}</b> — Идентификатор кампании</p><p><b class="urlBuilder_help_term active" data-add-term="1">{adgroupid}</b> — Идентификатор группы объявлений</p><p><b class="urlBuilder_help_term active" data-add-term="1">{feeditemid}</b> — Идентификатор расширения, на которое нажал пользователь</p><p><b class="urlBuilder_help_term active" data-add-term="1">{targetid}</b> — Идентификатор ключевого слова (kwd), динамического поискового объявления (dsa), цели списка ремаркетинга (aud) или сегментации товара (pla), который вызывает показ объявлений</p><p><b class="urlBuilder_help_term active" data-add-term="1">{loc_interest_ms}</b> — Идентификатор местоположения, указанного в поисковом запросе пользователя</p><p><b class="urlBuilder_help_term active" data-add-term="1">{loc_physical_ms}</b> — Идентификатор географического местоположения, из которого был получен клик</p><p><b class="urlBuilder_help_term active" data-add-term="1">{matchtype}</b> — Тип соответствия ключевого слова, по которому показано объявление</p><p><b class="urlBuilder_help_term active" data-add-term="1">{network}</b> — Рекламная сеть, из которой получен клик</p><p><b class="urlBuilder_help_term active" data-add-term="1">{device}</b> — Тип устройства, с которого получен клик</p><p><b class="urlBuilder_help_term active" data-add-term="1">{devicemodel}</b> — Модель телефона или планшета, с которого получен клик</p><p><b class="urlBuilder_help_term active" data-add-term="1">{ifmobile:mobile}</b> — Клик получен с мобильного телефона</p><p><b class="urlBuilder_help_term active" data-add-term="1">{ifnotmobile:notmobile}</b> — Клик получен с компьютера или планшета</p><p><b class="urlBuilder_help_term active" data-add-term="1">{ifsearch:search}</b> — Клик получен из поисковой сети Google</p><p><b class="urlBuilder_help_term active" data-add-term="1">{ifcontent:content}</b> — Клик получен из контекстно-медийной сети Google</p><p><b class="urlBuilder_help_term active" data-add-term="1">{creative}</b> — Уникальный идентификатор объявления</p><p><b class="urlBuilder_help_term active" data-add-term="1">{keyword}</b> — Ключевое слово, по которому показано ваше объявление в поисковой сети, или наиболее близкое ключевое слово при показе в контекстно-медийной сети</p><p><b class="urlBuilder_help_term active" data-add-term="1">{placement}</b> — Сайт, где ваше объявление получило клик</p><p><b class="urlBuilder_help_term active" data-add-term="1">{target}</b> — Категория мест размещения</p><p><b class="urlBuilder_help_term active" data-add-term="1">{param1}</b> — Параметр объявления 1</p><p><b class="urlBuilder_help_term active" data-add-term="1">{param2e}</b> — Параметр объявления 2</p><p><b class="urlBuilder_help_term active" data-add-term="1">{random}</b> — Случайное число, сгенерированное сервером Google (беззнаковое 64-битное целое число)</p><p><b class="urlBuilder_help_term active" data-add-term="1">{aceid}</b> — Идентификатор контрольной или экспериментальной группы, используемый в экспериментах AdWords</p><p><b class="urlBuilder_help_term active" data-add-term="1">{adposition}</b> — Позиция вашего объявления на странице</p>						</div>
                        <div class="urlBuilder_help_tabBody urlBuilder_help_tabBody-vk hide">
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{campaign_id}</b> — ID рекламной кампании</p><p><b class="urlBuilder_help_term active" data-add-term="1">{ad_id}</b> — ID объявления</p>						</div>
                        <div class="urlBuilder_help_tabBody urlBuilder_help_tabBody-target-mail hide">
                            <p><b class="urlBuilder_help_term active" data-add-term="1">@{{advertiser_id}}</b> — ID рекламодателя</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{campaign_id}}</b> — ID рекламной кампании</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{campaign_name}}</b> — Название рекламной кампании</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{banner_id}}</b> — ID баннера</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{geo}}</b> — ID региона по геодереву myTarget, из которого был сделан переход</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{gender}}</b> — Пол пользователя, который сделал переход</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{age}}</b> — Возраст пользователя, который сделал переход</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{random}}</b> — Случайное число</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{impression_weekday}}</b> — Передает день недели, в который произошел показ баннера</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{impression_hour}}</b> — Передает час, в который произошел показ по Московскому времени в 24-часовом формате</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{user_timezone}}</b> — Передает временную зону пользователя, которому был сделан показ</p>						</div>
                    </div>
                </div>

                <div class="urlBuilder_el">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="urlBuilder_el_label">
                                Ключевое слово в кампании
                                <i>utm_term</i>
                                <b class="urlBuilder_el_helpToggle" data-toggle-label="Скрыть описание">Показать описание</b>
                            </div>
                        </div>
                    </div>

                    <div class="urlBuilder_el_input">

                        <div class="row">

                            <div class="col-md-6">
                                <input type="text" id="urlBuilderUtmTerm">
                            </div>
                            <div class="col-md-4">
                                <div class="urlBuilder_el_input_items">
                                    <span>{keyword}</span>
                                    <span>@{{geo}}.@{{gender}}.@{{age}}</span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="urlBuilder_help">
                        <p>
                            Метка <b class="urlBuilder_help_term">utm_term</b> содержит конкретное ключевое слово
                            рекламного объявления или дату почтовой рассылки.
                            Можно вписать произвольное значение, либо воспользоваться динамическими параметрами
                            соответствующей рекламной системы.
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">{keyword}</b> — Ключевая фраза, по которой было
                            показано объявление в Яндекс.Директ и Google AdWords
                        </p>
                        <p>
                            В myTarget можно использовать нескольких параметров, например для передачи
                            региона, пола и возраста посетителя можно использовать связку
                            <b class="urlBuilder_help_term active">@{{geo}}.@{{gender}}.@{{age}}</b>.
                            Все динамические параметры myTarget перечислены ниже:
                        </p>
                        <p><b class="urlBuilder_help_term active" data-add-term="1">@{{advertiser_id}}</b> — ID рекламодателя</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{campaign_id}}</b> — ID рекламной кампании</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{campaign_name}}</b> — Название рекламной кампании</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{banner_id}}</b> — ID баннера</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{geo}}</b> — ID региона по геодереву myTarget, из которого был сделан переход</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{gender}}</b> — Пол пользователя, который сделал переход</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{age}}</b> — Возраст пользователя, который сделал переход</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{random}}</b> — Случайное число</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{impression_weekday}}</b> — Передает день недели, в который произошел показ баннера</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{impression_hour}}</b> — Передает час, в который произошел показ по Московскому времени в 24-часовом формате</p><p><b class="urlBuilder_help_term active" data-add-term="1">@{{user_timezone}}</b> — Передает временную зону пользователя, которому был сделан показ</p>					</div>
                </div>

                <div class="row">
                    <div class="col-md-6">

                        <div class="urlBuilder_el">
                            <span class="urlBuilder_el_check" id="urlBuilderOpenstat">
                                <i class="fa fa-square-o"></i>
                                <i class="fa fa-square"></i>
                                Добавить метку Openstat
                            </span>
                            <b class="urlBuilder_el_helpToggle" data-toggle-label="Скрыть подсказку">Что это?</b>
                            <div class="urlBuilder_help">
                                <p>
                                    Метка <b class="urlBuilder_help_term">_openstat</b> представляет собой закодированную
                                    строку, содержащую значение меток
                                    <b class="urlBuilder_help_term">utm_source</b>,
                                    <b class="urlBuilder_help_term">utm_campaign</b>
                                    и
                                    <b class="urlBuilder_help_term">utm_content</b>.
                                </p>
                            </div>
                        </div>

                        <div class="urlBuilder_el urlBuilder_el-paramsDirect">
                            <span class="urlBuilder_el_check" id="urlBuilderParamsDirect">
                                <i class="fa fa-square-o"></i>
                                <i class="fa fa-square"></i>
                                Добавить динамические параметры Яндекс.Директ
                            </span>
                            <div class="urlBuilder_el_params hide">
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_ad_id" data-param-value="{ad_id}">
                                        <i class="fa fa-square-o"></i>
                                        <i class="fa fa-square"></i>
                                        <b>{ad_id}</b><p>Идентификатор объявления</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_addphrases" data-param-value="{addphrases}">
                                        <i class="fa fa-square-o"></i>
                                        <i class="fa fa-square"></i>
                                        <b>{addphrases}</b>
                                        <p>Инициирован ли этот показ автоматически добавленными фразами</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_addphrasestext" data-param-value="{addphrasestext}">
                                        <i class="fa fa-square-o"></i>
                                        <i class="fa fa-square"></i>
                                        <b>{addphrasestext}</b>
                                        <p>Текст автоматически добавленной фразы</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_campaign_type" data-param-value="{campaign_type}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{campaign_type}</b><p>Тип кампании</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="yd_campaign_id" data-param-value="{campaign_id}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{campaign_id}</b><p>Идентификатор рекламной кампании</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="yd_device_type" data-param-value="{device_type}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{device_type}</b><p>Тип устройства, на котором произведен показ</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="yd_gbid" data-param-value="{gbid}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{gbid}</b><p>Идентификатор группы</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="yd_keyword" data-param-value="{keyword}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{keyword}</b><p>Ключевая фраза, по которой было показано объявление</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="yd_phrase_id" data-param-value="{phrase_id}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{phrase_id}</b><p>Идентификатор ключевой фразы для текстово-графических объявлений или рекламы мобильных приложений</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="yd_retargeting_id" data-param-value="{retargeting_id}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{retargeting_id}</b><p>Идентификатор условия ретаргетинга</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="yd_adtarget_name" data-param-value="{adtarget_name}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{adtarget_name}</b><p>Условие нацеливания, по которому было показано динамическое объявление</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="yd_adtarget_id" data-param-value="{adtarget_id}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{adtarget_id}</b><p>Идентификатор условия нацеливания динамического объявления</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="yd_position" data-param-value="{position}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{position}</b><p>Точная позиция объявления в блоке</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="yd_position_type" data-param-value="{position_type}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{position_type}</b><p>Тип блока, если показ произошел на странице с результатами поиска Яндекса</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="yd_source" data-param-value="{source}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{source}</b><p>Место показа</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="yd_source_type" data-param-value="{source_type}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{source_type}</b><p>Тип площадки, на которой произведен показ объявления</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="yd_region_name" data-param-value="{region_name}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{region_name}</b><p>Регион, в котором было показано объявление</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="yd_region_id" data-param-value="{region_id}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{region_id}</b><p>Идентификатор региона, в котором было показано объявление</p></span></div>					</div>
                        </div>

                        <div class="urlBuilder_el urlBuilder_el-paramsAdwords">
                            <span class="urlBuilder_el_check" id="urlBuilderParamsAdwords">
                                <i class="fa fa-square-o"></i>
                                <i class="fa fa-square"></i>
                                Добавить динамические параметры Google AdWords
                            </span>
                            <div class="urlBuilder_el_params hide">
                                <div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_campaignid" data-param-value="{campaignid}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{campaignid}</b><p>Идентификатор кампании</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_adgroupid" data-param-value="{adgroupid}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{adgroupid}</b><p>Идентификатор группы объявлений</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_feeditemid" data-param-value="{feeditemid}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{feeditemid}</b><p>Идентификатор расширения, на которое нажал пользователь</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_targetid" data-param-value="{targetid}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{targetid}</b><p>Идентификатор ключевого слова (kwd), динамического поискового объявления (dsa), цели списка ремаркетинга (aud) или сегментации товара (pla), который вызывает показ объявлений</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_loc_interest_ms" data-param-value="{loc_interest_ms}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{loc_interest_ms}</b><p>Идентификатор местоположения, указанного в поисковом запросе пользователя</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_loc_physical_ms" data-param-value="{loc_physical_ms}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{loc_physical_ms}</b><p>Идентификатор географического местоположения, из которого был получен клик</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_matchtype" data-param-value="{matchtype}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{matchtype}</b><p>Тип соответствия ключевого слова, по которому показано объявление</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_network" data-param-value="{network}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{network}</b><p>Рекламная сеть, из которой получен клик</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_device" data-param-value="{device}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{device}</b><p>Тип устройства, с которого получен клик</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_devicemodel" data-param-value="{devicemodel}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{devicemodel}</b><p>Модель телефона или планшета, с которого получен клик</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_ifmobile:mobile" data-param-value="{ifmobile:mobile}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{ifmobile:mobile}</b><p>Клик получен с мобильного телефона</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_ifnotmobile:notmobile" data-param-value="{ifnotmobile:notmobile}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{ifnotmobile:notmobile}</b><p>Клик получен с компьютера или планшета</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_ifsearch:search" data-param-value="{ifsearch:search}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{ifsearch:search}</b><p>Клик получен из поисковой сети Google</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_ifcontent:content" data-param-value="{ifcontent:content}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{ifcontent:content}</b><p>Клик получен из контекстно-медийной сети Google</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_creative" data-param-value="{creative}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{creative}</b><p>Уникальный идентификатор объявления</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_keyword" data-param-value="{keyword}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{keyword}</b><p>Ключевое слово, по которому показано ваше объявление в поисковой сети, или наиболее близкое ключевое слово при показе в контекстно-медийной сети</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_placement" data-param-value="{placement}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{placement}</b><p>Сайт, где ваше объявление получило клик</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_target" data-param-value="{target}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{target}</b><p>Категория мест размещения</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_param1" data-param-value="{param1}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{param1}</b><p>Параметр объявления 1</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_param2e" data-param-value="{param2e}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{param2e}</b><p>Параметр объявления 2</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_random" data-param-value="{random}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{random}</b><p>Случайное число, сгенерированное сервером Google (беззнаковое 64-битное целое число)</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_aceid" data-param-value="{aceid}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{aceid}</b><p>Идентификатор контрольной или экспериментальной группы, используемый в экспериментах AdWords</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="ga_adposition" data-param-value="{adposition}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{adposition}</b><p>Позиция вашего объявления на странице</p></span></div>					</div>
                        </div>

                        <div class="urlBuilder_el urlBuilder_el-paramsVK">
                            <span class="urlBuilder_el_check" id="urlBuilderParamsVK">
                                <i class="fa fa-square-o"></i>
                                <i class="fa fa-square"></i>
                                Добавить динамические параметры таргетинга ВКонтакте
                            </span>
                            <div class="urlBuilder_el_params hide">
                                <div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="vk_campaign_id" data-param-value="{campaign_id}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{campaign_id}</b><p>ID рекламной кампании</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="vk_ad_id" data-param-value="{ad_id}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{ad_id}</b><p>ID объявления</p></span></div>					</div>
                        </div>

                        <div class="urlBuilder_el urlBuilder_el-paramsMailru">
                            <span class="urlBuilder_el_check" id="urlBuilderParamsMailru">
                                <i class="fa fa-square-o"></i>
                                <i class="fa fa-square"></i>
                                Добавить динамические параметры myTarget
                            </span>
                            <div class="urlBuilder_el_params hide">
                                <div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="mt_advertiser_id" data-param-value="@{{advertiser_id}}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{advertiser_id}}</b><p>ID рекламодателя</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="mt_campaign_id" data-param-value="@{{campaign_id}}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{campaign_id}}</b><p>ID рекламной кампании</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="mt_campaign_name" data-param-value="@{{campaign_name}}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{campaign_name}}</b><p>Название рекламной кампании</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="mt_banner_id" data-param-value="@{{banner_id}}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{banner_id}}</b><p>ID баннера</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="mt_geo" data-param-value="@{{geo}}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{geo}}</b><p>ID региона по геодереву myTarget, из которого был сделан переход</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="mt_gender" data-param-value="@{{gender}}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{gender}}</b><p>Пол пользователя, который сделал переход</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="mt_age" data-param-value="@{{age}}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{age}}</b><p>Возраст пользователя, который сделал переход</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="mt_random" data-param-value="@{{random}}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{random}}</b><p>Случайное число</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="mt_impression_weekday" data-param-value="@{{impression_weekday}}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{impression_weekday}}</b><p>Передает день недели, в который произошел показ баннера</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="mt_impression_hour" data-param-value="@{{impression_hour}}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{impression_hour}}</b><p>Передает час, в который произошел показ по Московскому времени в 24-часовом формате</p></span></div><div class="urlBuilder_el_param"><span class="urlBuilder_el_check" data-param-key="mt_user_timezone" data-param-value="@{{user_timezone}}"><i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{user_timezone}}</b><p>Передает временную зону пользователя, которому был сделан показ</p></span></div>					</div>
                        </div>

                        <div class="urlBuilder_el">
                            <div class="btn btn-secondary urlBuilder_go">Создать URL</div>
                            <div class="urlBuilder_error"></div>
                        </div>

                    </div>
                </div>

                <div class="hide">
                    <div class="urlBuilder_result">
                        <div class="urlBuilder_result_title">Готово! Можете использовать:</div>
                        <div class="urlBuilder_result_body">
                            <input type="text" readonly="true">
                            <textarea readonly="true" wrap="off"></textarea>
                        </div>
                        <div class="urlBuilder_result_buttons">
                            <span class="btn btn-secondary swpmodal-close">Закрыть</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @slot('js')
        <script type="text/javascript" src="{{ asset('plugins/utm-marks/js/url-builder.min.js') }}"></script>
    @endslot
@endcomponent
