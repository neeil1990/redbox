@component('component.card', ['title' => __('UTM Marks')])

    @slot('css')
        <link rel='stylesheet' id='swpc-main-css' href='{{ asset('plugins/utm-marks/css/style.css') }}' type='text/css'
              media='all'/>

        <style>
            .UTM {
                background: oldlace;
            }
        </style>
    @endslot

    <div class="grid urlBuilder mt-base">
        <div class="grid_c grid_c-1of2">
            <div class="urlBuilder_form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="urlBuilder_step">{{ __('Step 1. Landing page') }}</div>
                        <div class="urlBuilder_el urlBuilder_el-url">
                            <div class="urlBuilder_el_label">
                                {{ __('Enter the page address:') }}
                                <span class="urlBuilder_toogleUrls">{{ __('Multiple URLs') }}</span>
                            </div>
                            <div class="urlBuilder_el_input">
                                <input id="urlBuilderUrl" type="text"></div>
                            <div class="urlBuilder_el_tip">Пример: https://prime-ltd.su/nashi-servisyi/</div>
                        </div>

                        <div class="urlBuilder_el urlBuilder_el-urls hide">
                            <div class="urlBuilder_el_label">
                                {{ __('Enter page addresses') }}:
                                <span class="urlBuilder_toogleUrls">{{ __('One URL') }}</span>
                            </div>
                            <div class="urlBuilder_el_input">
                                <textarea id="urlBuilderUrls"></textarea>
                            </div>
                            <div class="urlBuilder_el_tip">
                                {{ __('Example') }}: https://prime-ltd.su/nashi-servisyi/
                                {{ __('One for each line.') }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="urlBuilder_tpl">
                            <div class="urlBuilder_tpl_title">{{ __('Sample:') }}</div>
                            <div class="urlBuilder_tpl_items">
                                <span class="active btn btn-secondary btn-flat click_tracking" data-id="custom" data-click="Arbitrary">{{ __('Arbitrary') }}</span>
                                <span data-id="direct"
                                      class="btn btn-secondary btn-flat click_tracking" data-click="Yandex_Direct">{{ __('Yandex.Direct') }}</span>
                                <span data-id="adwords"
                                      class="btn btn-secondary btn-flat click_tracking" data-click="Google AdWords">{{ __('Google AdWords') }}</span>
                                <span data-id="vk" class="btn btn-secondary btn-flat click_tracking" data-click="VK targeting">{{ __('VK targeting') }}</span>
                                <span data-id="mailru" class="btn btn-secondary btn-flat click_tracking" data-click="myTarget">{{ __('myTarget') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="urlBuilder_step">{{ __('Step 2. Basic parameters') }}</div>
                    </div>
                </div>

                <div class="urlBuilder_el">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="urlBuilder_el_label">
                                {{ __('Campaign source') }}
                                <i>utm_source</i>
                                <b class="urlBuilder_el_helpToggle"
                                   data-toggle-label="Скрыть описание">{{ __('Show Description') }}</b>
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
                                    <span class="btn btn-secondary btn-flat">yandex</span>
                                    <span class="btn btn-secondary btn-flat">google</span>
                                    <span class="btn btn-secondary btn-flat">vk</span>
                                    <span class="btn btn-secondary btn-flat">target-mail</span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="urlBuilder_help">
                        <p>
                            {{__('Label')}} <b
                                class="urlBuilder_help_term">utm_source</b> {{ __('indicates the source of the advertising campaign.') }}
                            <br>
                            {{ __('As a rule, the following options are used:') }}
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">yandex</b> — {{ __('Yandex.Direct') }}
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">google</b> — {{ __('Google AdWords') }}
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">vk</b> — {{ __('VK targeting') }}
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">target-mail</b> — {{ __('myTarget') }}
                        </p>
                    </div>

                </div>

                <div class="urlBuilder_el">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="urlBuilder_el_label">
                                {{ __('Campaign channel') }}
                                <i>utm_medium</i>
                                <b class="urlBuilder_el_helpToggle"
                                   data-toggle-label="Скрыть описание">{{ __('Show Description') }}</b>
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
                                    <span class="btn btn-secondary btn-flat">cpc</span>
                                    <span class="btn btn-secondary btn-flat">cpv</span>
                                    <span class="btn btn-secondary btn-flat">cpm</span>
                                    <span class="btn btn-secondary btn-flat">email</span>
                                    <span class="btn btn-secondary btn-flat">banner</span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="urlBuilder_help">
                        <p>
                            {{ __('Label') }} <b
                                class="urlBuilder_help_term">utm_medium</b> {{ __('denotes the advertising campaign channel used (marketing medium, traffic channel, or customer interaction).') }}
                            <br>
                            {{ __('As a rule, the following options are used:') }}
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">cpc</b> — {{ __('Pay per click advertising') }}
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">cpv</b> — {{ __('Pay per visit advertising') }}
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">cpm</b> — {{ __('Pay-per-view advertising') }}
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">email</b> — {{ __('Mailing list') }}
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">banner</b> — {{ __('Banner on the site') }}
                        </p>
                    </div>
                </div>

                <div class="urlBuilder_el">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="urlBuilder_el_label">
                                {{ __('Campaign name') }}
                                <i>utm_campaign</i>
                                <b class="urlBuilder_el_helpToggle"
                                   data-toggle-label="{{ __('Hide description') }}">{{ __('Show Description') }}</b>
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
                                    <span class="btn btn-secondary btn-flat">{campaign_id}</span>
                                    <span class="btn btn-secondary btn-flat">{campaignid}</span>
                                    <span class="btn btn-secondary btn-flat">@{{campaign_id}}</span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="urlBuilder_help">
                        <p>
                            {{ __('Label') }} <b
                                class="urlBuilder_help_term">utm_campaign</b> {{ __('denotes a specific advertising campaign (for example, an advertisement for a specific product or a special promotion). You can enter an arbitrary value, or use the dynamic parameters of the corresponding advertising system:') }}
                            <br>
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">{campaign_id}</b>
                            — {{ __('ID of the advertising campaign in Yandex.Direct and targeting VKontakte') }}
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">{campaignid}</b>
                            — {{ __('ID of the advertising campaign in Google Adwords') }}
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">@{{campaign_id}}</b>
                            — {{ __('ID of the advertising campaign in myTarget') }}
                        </p>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="urlBuilder_step">{{ __('Step 3. Additional parameters') }}</div>
                    </div>
                </div>

                <div class="urlBuilder_el">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="urlBuilder_el_label">
                                {{ __('Campaign content') }}
                                <i>utm_content</i>
                                <b class="urlBuilder_el_helpToggle"
                                   data-toggle-label="Скрыть описание">{{ __('Show Description') }}</b>
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
                                    <span class="btn btn-secondary btn-flat">{ad_id}</span>
                                    <span class="btn btn-secondary btn-flat">{creative}</span>
                                    <span class="btn btn-secondary btn-flat">@{{banner_id}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="urlBuilder_help">
                        <p>
                            {{ __('Label') }} <b
                                class="urlBuilder_help_term">utm_content</b> {{ __('contains additional information. Can be used for A / B testing or for targeted ads. You can enter an arbitrary value, or use the dynamic parameters of the corresponding advertising system:') }}
                        </p>
                        <div class="urlBuilder_help_tabs">
                            <div class="urlBuilder_help_tab active" data-tab="yandex">{{ __('Yandex.Direct') }}</div>
                            <div class="urlBuilder_help_tab" data-tab="google">{{ __('Google AdWords') }}</div>
                            <div class="urlBuilder_help_tab" data-tab="vk">{{ __('VK targeting') }}</div>
                            <div class="urlBuilder_help_tab" data-tab="target-mail">{{ __('myTarget') }}</div>
                        </div>
                        <div class="urlBuilder_help_tabBody urlBuilder_help_tabBody-yandex">
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{ad_id}</b> — {{ __('Ad ID') }}
                            </p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{addphrases}</b>
                                — {{ __('Whether this impression was triggered by auto-added phrases') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{addphrasestext}</b>
                                — {{ __('Auto-added phrase text') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{campaign_type}</b>
                                — {{ __('Campaign type') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{campaign_id}</b>
                                — {{ __('Advertising campaign ID') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{device_type}</b>
                                — {{ __('The type of device on which the impression was made') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{gbid}</b>
                                — {{ __('Group id') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{keyword}</b>
                                — {{ __('Key phrase for which the ad was shown') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{phrase_id}</b>
                                — {{ __('Key phrase ID for text-image ads or mobile app ads') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{retargeting_id}</b>
                                — {{ __('Retargeting condition ID') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{adtarget_name}</b>
                                — {{ __('The targeting condition on which the dynamic ad was shown') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{adtarget_id}</b>
                                — {{ __('ID of the dynamic ad targeting condition') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{position}</b>
                                — {{ __('The exact position of the ad in the block') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{position_type}</b>
                                — {{ __('Block type if the impression occurred on a page with Yandex search results') }}
                            </p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{source}</b>
                                — {{ __('Place of display') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{source_type}</b>
                                — {{ __('The type of site on which the ad was displayed') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{region_name}</b>
                                — {{ __('The region in which the ad was shown') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{region_id}</b>
                                — {{ __('ID of the region in which the ad was shown') }}</p>
                        </div>
                        <div class="urlBuilder_help_tabBody urlBuilder_help_tabBody-google hide">
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{campaignid}</b>
                                — {{ __('Campaign ID') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{adgroupid}</b>
                                — {{ __('Ad group ID') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{feeditemid}</b>
                                — {{ __('The ID of the extension that the user clicked on') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{targetid}</b>
                                — {{ __('The id of the keyword (kwd), dynamic search ad (dsa), remarketing list target (aud), or product segmentation (pla) that triggers the ad serving') }}
                            </p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{loc_interest_ms}</b>
                                — {{ __('The identifier of the location specified in the users search query') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{loc_physical_ms}</b>
                                — {{ __('ID of the geographic location from which the click was received') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{matchtype}</b>
                                — {{ __('Matching type of the keyword for which the ad is shown') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{network}</b>
                                — {{ __('The ad network from which the click was received') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{device}</b>
                                — {{ __('The type of device from which the click was received') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{devicemodel}</b>
                                — {{ __('The model of the phone or tablet from which the click was received') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{ifmobile:mobile}</b>
                                — {{ __('Click received from a mobile phone') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{ifnotmobile:notmobile}</b>
                                — {{ __('Click received from a computer or tablet') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{ifsearch:search}</b>
                                — {{ __('Click from Google Search Network') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{ifcontent:content}</b>
                                — {{ __('Click from Google Display Network') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{creative}</b>
                                — {{ __('Unique ad identifier') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{keyword}</b>
                                — {{ __('The keyword your ad is shown for on the Search Network, or the closest keyword when shown on the Display Network') }}
                            </p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{placement}</b>
                                — {{ __('The site where your ad got clicked') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{target}</b>
                                — {{ __('Placement category') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{param1}</b>
                                — {{__('Ad parameter')}} 1</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{param2e}</b>
                                — {{__('Ad parameter')}} 2</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{random}</b>
                                — {{ __('Random number generated by google server (unsigned 64 bit integer)') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{aceid}</b>
                                — {{ __('Control or experimental group ID used in AdWords experiments') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{adposition}</b>
                                — {{ __('Position of your ad on the page') }}</p>
                        </div>
                        <div class="urlBuilder_help_tabBody urlBuilder_help_tabBody-vk hide">
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{campaign_id}</b>
                                — {{ __('Advertising campaign ID') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">{ad_id}</b> — {{ __('Ad ID') }}
                            </p>
                        </div>
                        <div class="urlBuilder_help_tabBody urlBuilder_help_tabBody-target-mail hide">
                            <p><b class="urlBuilder_help_term active" data-add-term="1">@{{advertiser_id}}</b>
                                — {{ __('Advertiser ID') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">@{{campaign_id}}</b>
                                — {{ __('Advertising campaign ID') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">@{{campaign_name}}</b>
                                — {{ __('Name of the ad campaign') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">@{{banner_id}}</b>
                                — {{ __('Banner ID') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">@{{geo}}</b>
                                — {{ __('ID of the region by geotree myTarget, from which the transition was made') }}
                            </p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">@{{gender}}</b>
                                — {{ __('Gender of the user who made the transition') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">@{{age}}</b>
                                — {{ __('Age of the user who made the transition') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">@{{random}}</b>
                                — {{ __('Random number') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">@{{impression_weekday}}</b>
                                — {{ __('Sends the day of the week on which the banner was shown') }}</p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">@{{impression_hour}}</b>
                                — {{ __('Sends the hour at which the show took place Moscow time in a 24-hour format') }}
                            </p>
                            <p><b class="urlBuilder_help_term active" data-add-term="1">@{{user_timezone}}</b>
                                — {{ __('Sends the time zone of the user to whom the impression was made') }}</p>
                        </div>
                    </div>
                </div>

                <div class="urlBuilder_el">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="urlBuilder_el_label">
                                {{ __('Campaign Keyword') }}
                                <i>utm_term</i>
                                <b class="urlBuilder_el_helpToggle"
                                   data-toggle-label="{{ __('Hide') }}">{{ __('Show Description') }}</b>
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
                                    <span class="btn btn-secondary btn-flat">{keyword}</span>
                                    <span class="btn btn-secondary btn-flat">@{{geo}}.@{{gender}}.@{{age}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="urlBuilder_help">
                        <p>
                            {{ __('Label') }} <b
                                class="urlBuilder_help_term">utm_term</b> {{ __('contains the specific keyword of the advertisement or the date of the mailing. You can enter an arbitrary value, or use the dynamic parameters of the corresponding advertising system.') }}
                        </p>
                        <p>
                            <b class="urlBuilder_help_term active">{keyword}</b>
                            — {{ __('Key phrase by which the ad was shown in Yandex.Direct and Google AdWords') }}
                        </p>
                        <p>
                            {{ __('In myTarget, you can use several parameters, for example, to transfer the region, gender and age of the visitor, you can use the bundle') }}
                            <b class="urlBuilder_help_term active">@{{geo}}.@{{gender}}.@{{age}}</b>.
                            {{ __('All dynamic parameters of myTarget are listed below:') }}
                        </p>
                        <p><b class="urlBuilder_help_term active" data-add-term="1">@{{advertiser_id}}</b>
                            — {{ __('Advertiser ID') }}</p>
                        <p><b class="urlBuilder_help_term active" data-add-term="1">@{{campaign_id}}</b>
                            — {{ __('Advertising campaign ID') }}</p>
                        <p><b class="urlBuilder_help_term active" data-add-term="1">@{{campaign_name}}</b>
                            — {{ __('Name of the ad campaign') }}</p>
                        <p><b class="urlBuilder_help_term active" data-add-term="1">@{{banner_id}}</b>
                            — {{ __('Banner ID') }}</p>
                        <p><b class="urlBuilder_help_term active" data-add-term="1">@{{geo}}</b>
                            — {{ __('ID of the region by geotree myTarget, from which the transition was made') }}</p>
                        <p><b class="urlBuilder_help_term active" data-add-term="1">@{{gender}}</b>
                            — {{ __('Gender of the user who made the transition') }}</p>
                        <p><b class="urlBuilder_help_term active" data-add-term="1">@{{age}}</b>
                            — {{ __('Age of the user who made the transition') }}</p>
                        <p><b class="urlBuilder_help_term active" data-add-term="1">@{{random}}</b>
                            — {{ __('Random number') }}</p>
                        <p><b class="urlBuilder_help_term active" data-add-term="1">@{{impression_weekday}}</b>
                            — {{ __('Sends the day of the week on which the banner was shown') }}</p>
                        <p><b class="urlBuilder_help_term active" data-add-term="1">@{{impression_hour}}</b>
                            — {{ __('Sends the hour at which the show took place Moscow time in a 24-hour format') }}
                        </p>
                        <p><b class="urlBuilder_help_term active" data-add-term="1">@{{user_timezone}}</b>
                            — {{ __('Sends the time zone of the user to whom the impression was made') }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">

                        <div class="urlBuilder_el">
                            <span class="urlBuilder_el_check click_tracking" data-click="add openstat" id="urlBuilderOpenstat">
                                <i class="fa fa-square-o"></i>
                                <i class="fa fa-square"></i>
                                {{ __('Add a label') }} Openstat
                            </span>
                            <b class="urlBuilder_el_helpToggle"
                               data-toggle-label="Скрыть подсказку">{{ __('Whats this?') }}</b>
                            <div class="urlBuilder_help">
                                <p>
                                    {{ __('Label') }} <b
                                        class="urlBuilder_help_term">_openstat</b> {{ __('is an encoded string containing the value of the labels') }}
                                    <b class="urlBuilder_help_term">utm_source</b>,
                                    <b class="urlBuilder_help_term">utm_campaign</b>
                                    {{ __('and') }}
                                    <b class="urlBuilder_help_term">utm_content</b>.
                                </p>
                            </div>
                        </div>

                        <div class="urlBuilder_el urlBuilder_el-paramsDirect">
                            <span class="urlBuilder_el_check click_tracking" data-click="Add dynamic parameters Yandex.Direct" id="urlBuilderParamsDirect">
                                <i class="fa fa-square-o"></i>
                                <i class="fa fa-square"></i>
                                {{ __('Add dynamic parameters Yandex.Direct') }}
                            </span>
                            <div class="urlBuilder_el_params hide">
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_ad_id"
                                          data-param-value="{ad_id}">
                                        <i class="fa fa-square-o"></i>
                                        <i class="fa fa-square"></i>
                                        <b>{ad_id}</b><p>{{ __('Ad ID') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_addphrases"
                                          data-param-value="{addphrases}">
                                        <i class="fa fa-square-o"></i>
                                        <i class="fa fa-square"></i>
                                        <b>{addphrases}</b>
                                        <p>{{ __('Whether this impression was triggered by auto-added phrases') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_addphrasestext"
                                          data-param-value="{addphrasestext}">
                                        <i class="fa fa-square-o"></i>
                                        <i class="fa fa-square"></i>
                                        <b>{addphrasestext}</b>
                                        <p>{{ __('Auto-added phrase text') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_campaign_type"
                                          data-param-value="{campaign_type}"><i class="fa fa-square-o"></i><i
                                            class="fa fa-square"></i>
                                        <b>{campaign_type}</b><p>{{ __('Campaign type') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_campaign_id"
                                          data-param-value="{campaign_id}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{campaign_id}</b>
                                        <p>{{ __('Advertising campaign ID') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_device_type"
                                          data-param-value="{device_type}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{device_type}</b>
                                        <p>{{ __('The type of device on which the impression was made') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_gbid"
                                          data-param-value="{gbid}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{gbid}</b>
                                        <p>{{ __('Group id') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_keyword"
                                          data-param-value="{keyword}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{keyword}</b>
                                        <p>{{ __('Key phrase for which the ad was shown') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_phrase_id"
                                          data-param-value="{phrase_id}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{phrase_id}</b>
                                        <p>{{ __('Key phrase ID for text-image ads or mobile app ads') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_retargeting_id"
                                          data-param-value="{retargeting_id}">
                                        <i class="fa fa-square-o"></i><i
                                            class="fa fa-square"></i> <b>{retargeting_id}</b>
                                        <p>{{ __('Retargeting condition ID') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_adtarget_name"
                                          data-param-value="{adtarget_name}">
                                        <i class="fa fa-square-o"></i><i
                                            class="fa fa-square"></i> <b>{adtarget_name}</b>
                                        <p>{{ __('The targeting condition on which the dynamic ad was shown') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_adtarget_id"
                                          data-param-value="{adtarget_id}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{adtarget_id}</b>
                                        <p>{{ __('ID of the dynamic ad targeting condition') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_position"
                                          data-param-value="{position}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{position}</b>
                                        <p>{{ __('The exact position of the ad in the block') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_position_type"
                                          data-param-value="{position_type}">
                                        <i class="fa fa-square-o"></i><i
                                            class="fa fa-square"></i> <b>{position_type}</b>
                                        <p>{{ __('Block type if the impression occurred on a page with Yandex search results') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_source"
                                          data-param-value="{source}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{source}</b>
                                        <p>{{ __('Place of display') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_source_type"
                                          data-param-value="{source_type}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{source_type}</b>
                                        <p>{{ __('The type of site on which the ad was displayed') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_region_name"
                                          data-param-value="{region_name}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{region_name}</b>
                                        <p>{{ __('The region in which the ad was shown') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="yd_region_id"
                                          data-param-value="{region_id}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{region_id}</b>
                                        <p>{{ __('ID of the region in which the ad was shown') }}</p>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="urlBuilder_el urlBuilder_el-paramsAdwords">
                            <span class="urlBuilder_el_check click_tracking" data-click="Add dynamic Google AdWords parameters" id="urlBuilderParamsAdwords">
                                <i class="fa fa-square-o"></i>
                                <i class="fa fa-square"></i>
                                {{ __('Add dynamic Google AdWords parameters') }}
                            </span>
                            <div class="urlBuilder_el_params hide">
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_campaignid"
                                          data-param-value="{campaignid}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{campaignid}</b>
                                        <p>{{ __('Campaign ID') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_adgroupid"
                                          data-param-value="{adgroupid}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{adgroupid}</b>
                                        <p>{{ __('Ad group ID') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_feeditemid"
                                          data-param-value="{feeditemid}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{feeditemid}</b>
                                        <p>{{ __('The ID of the extension that the user clicked on') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_targetid"
                                          data-param-value="{targetid}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{targetid}</b>
                                        <p>{{ __('The id of the keyword (kwd), dynamic search ad (dsa), remarketing list target (aud), or product segmentation (pla) that triggers the ad serving') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_loc_interest_ms"
                                          data-param-value="{loc_interest_ms}">
                                        <i class="fa fa-square-o"></i><i
                                            class="fa fa-square"></i> <b>{loc_interest_ms}</b>
                                        <p>{{ __('The identifier of the location specified in the users search query') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_loc_physical_ms"
                                          data-param-value="{loc_physical_ms}">
                                        <i class="fa fa-square-o"></i><i
                                            class="fa fa-square"></i> <b>{loc_physical_ms}</b>
                                        <p>{{ __('ID of the geographic location from which the click was received') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_matchtype"
                                          data-param-value="{matchtype}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{matchtype}</b>
                                        <p>{{ __('Matching type of the keyword for which the ad is shown') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_network"
                                          data-param-value="{network}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{network}</b>
                                        <p>{{ __('The ad network from which the click was received') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_device"
                                          data-param-value="{device}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{device}</b>
                                        <p>{{ __('The type of device from which the click was received') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_devicemodel"
                                          data-param-value="{devicemodel}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{devicemodel}</b>
                                        <p>{{ __('The model of the phone or tablet from which the click was received') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_ifmobile:mobile"
                                          data-param-value="{ifmobile:mobile}">
                                        <i class="fa fa-square-o"></i><i
                                            class="fa fa-square"></i> <b>{ifmobile:mobile}</b>
                                        <p>{{ __('Click received from a mobile phone') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_ifnotmobile:notmobile"
                                          data-param-value="{ifnotmobile:notmobile}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{ifnotmobile:notmobile}</b>
                                        <p>{{ __('Click received from a computer or tablet') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_ifsearch:search"
                                          data-param-value="{ifsearch:search}">
                                        <i class="fa fa-square-o"></i><i
                                            class="fa fa-square"></i> <b>{ifsearch:search}</b>
                                        <p>{{ __('Click from Google Search Network') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_ifcontent:content"
                                          data-param-value="{ifcontent:content}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{ifcontent:content}</b>
                                        <p>{{ __('Click from Google Display Network') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_creative"
                                          data-param-value="{creative}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{creative}</b>
                                        <p>{{ __('Unique ad identifier') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_keyword"
                                          data-param-value="{keyword}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{keyword}</b>
                                        <p>{{ __('The keyword your ad is shown for on the Search Network, or the closest keyword when shown on the Display Network') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_placement"
                                          data-param-value="{placement}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{placement}</b>
                                        <p>{{ __('The site where your ad got clicked') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_target"
                                          data-param-value="{target}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{target}</b>
                                        <p>{{ __('Placement category') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_param1"
                                          data-param-value="{param1}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{param1}</b>
                                        <p>{{ __('Ad parameter') }} 1</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_param2e"
                                          data-param-value="{param2e}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{param2e}</b>
                                        <p>{{ __('Ad parameter') }} 2</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_random"
                                          data-param-value="{random}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{random}</b>
                                        <p>{{ __('Random number generated by google server (unsigned 64 bit integer)') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_aceid"
                                          data-param-value="{aceid}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{aceid}</b>
                                        <p>{{ __('Control or experimental group ID used in AdWords experiments') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="ga_adposition"
                                          data-param-value="{adposition}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{adposition}</b>
                                        <p>{{ __('Position of your ad on the page') }}</p>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="urlBuilder_el urlBuilder_el-paramsVK click_tracking" data-click="Add dynamic targeting options to VKontakte">
                            <span class="urlBuilder_el_check" id="urlBuilderParamsVK">
                                <i class="fa fa-square-o"></i>
                                <i class="fa fa-square"></i>
                                {{ __('Add dynamic targeting options to VKontakte') }}
                            </span>
                            <div class="urlBuilder_el_params hide">
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="vk_campaign_id"
                                          data-param-value="{campaign_id}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{campaign_id}</b>
                                        <p>{{ __('Advertising campaign ID') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="vk_ad_id"
                                          data-param-value="{ad_id}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>{ad_id}</b>
                                        <p>{{ __('Ad ID') }}</p>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="urlBuilder_el urlBuilder_el-paramsMailru click_tracking" data-click="Add dynamic parameters to myTarget">
                            <span class="urlBuilder_el_check" id="urlBuilderParamsMailru">
                                <i class="fa fa-square-o"></i>
                                <i class="fa fa-square"></i>
                                {{ __('Add dynamic parameters to myTarget') }}
                            </span>
                            <div class="urlBuilder_el_params hide">
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="mt_advertiser_id"
                                          data-param-value="@{{advertiser_id}}">
                                        <i class="fa fa-square-o"></i><i
                                            class="fa fa-square"></i> <b>@{{advertiser_id}}</b>
                                        <p>{{ __('Advertiser ID') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="mt_campaign_id"
                                          data-param-value="@{{campaign_id}}">
                                        <i class="fa fa-square-o"></i><i
                                            class="fa fa-square"></i> <b>@{{campaign_id}}</b>
                                        <p>{{ __('Advertising campaign ID') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="mt_campaign_name"
                                          data-param-value="@{{campaign_name}}">
                                        <i class="fa fa-square-o"></i><i
                                            class="fa fa-square"></i> <b>@{{campaign_name}}</b>
                                        <p>{{ __('Name of the ad campaign') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="mt_banner_id"
                                          data-param-value="@{{banner_id}}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{banner_id}}</b>
                                        <p>{{ __('Banner ID') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="mt_geo"
                                          data-param-value="@{{geo}}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{geo}}</b>
                                        <p>{{ __('ID of the region by geotree myTarget, from which the transition was made') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="mt_gender"
                                          data-param-value="@{{gender}}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{gender}}</b>
                                        <p>{{ __('Gender of the user who made the transition') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="mt_age"
                                          data-param-value="@{{age}}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{age}}</b>
                                        <p>{{ __('Age of the user who made the transition') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="mt_random"
                                          data-param-value="@{{random}}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{random}}</b>
                                        <p>{{ __('Random number') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="mt_impression_weekday"
                                          data-param-value="@{{impression_weekday}}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{impression_weekday}}</b>
                                        <p>{{ __('Sends the day of the week on which the banner was shown') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param"><span class="urlBuilder_el_check"
                                                                       data-param-key="mt_impression_hour"
                                                                       data-param-value="@{{impression_hour}}">
                                        <i class="fa fa-square-o"></i><i class="fa fa-square"></i> <b>@{{impression_hour}}</b>
                                        <p>{{ __('Sends the hour at which the show took place Moscow time in a 24-hour format') }}</p>
                                    </span>
                                </div>
                                <div class="urlBuilder_el_param">
                                    <span class="urlBuilder_el_check" data-param-key="mt_user_timezone"
                                          data-param-value="@{{user_timezone}}">
                                        <i class="fa fa-square-o"></i><i
                                            class="fa fa-square"></i> <b>@{{user_timezone}}</b>
                                        <p>{{ __('Sends the time zone of the user to whom the impression was made') }}</p>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="urlBuilder_el">
                            <div class="btn btn-secondary urlBuilder_go">{{ __('Create URL') }}</div>
                            <div class="urlBuilder_error"></div>
                        </div>

                    </div>
                </div>

                <div class="hide">
                    <div class="urlBuilder_result">
                        <div class="urlBuilder_result_title">{{ __('Ready! You can use:') }}</div>
                        <div class="urlBuilder_result_body">
                            <input type="text" readonly="true">
                            <textarea readonly="true" wrap="off"></textarea>
                        </div>
                        <div class="urlBuilder_result_buttons">
                            <span class="btn btn-secondary swpmodal-close">{{ __('Close') }}</span>
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
