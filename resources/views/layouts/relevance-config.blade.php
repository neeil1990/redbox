@if($admin)
    <form action="{{ route('changeConfig') }}" method="POST" class="col-lg-5 col-md-12 p-0">
        @csrf
        <div>
            <div class="form-group required">
                <label>Выбрать значение по умолчанию Top 10/20</label>
                {!! Form::select('count', array_unique([
                        $config->count_sites => $config->count_sites,
                        '10' => 10,
                        '20' => 20,
                        ]), null, ['class' => 'custom-select rounded-0']) !!}
            </div>

            <div class="form-group required">
                <label>Выбрать регион по умолчанию</label>
                {!! Form::select('region', array_unique([
                        $config->region => $config->region,
                        '1' => __('Moscow'),
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
                        '213' => __('Moscow'),
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
                        ]), null, ['class' => 'custom-select rounded-0']) !!}
            </div>

            <div class="form-group required">
                <label>Список игнорируемых доменов по умолчанию</label>
                {!! Form::textarea("ignored_domains", $config->ignored_domains ,["class" => "form-control"] ) !!}
            </div>

            <div class="form-group required d-flex align-items-center">
                <span>Количество обрезаемых символов по умолчанию</span>
                <input type="number" class="form form-control col-2 ml-1 mr-1" name="separator"
                       id="separator" value="{{ $config->separator }}">
            </div>

            <div class="mt-3 mb-3">
                <div class="mt-3 mb-3">
                    <p>{{ __('hide ignored domains') }}</p>
                    {!! Form::select('hide_ignored_domains', array_unique([
                            $config->hide_ignored_domains => $config->hide_ignored_domains,
                            'yes' => __('yes'),
                            'no' => __('no'),
                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                </div>

                <div class="mt-3 mb-3">
                    <p>Отслеживать текст в теге noindex по умолчанию</p>
                    {!! Form::select('noindex', array_unique([
                            $config->noindex => $config->noindex,
                            'yes' => __('yes'),
                            'no' => __('no'),
                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                </div>

                <div class="mt-3 mb-3">
                    <p>Отслеживать слова в атрибутах alt, tittle и data-text по умолчанию</p>
                    {!! Form::select('meta_tags', array_unique([
                            $config->meta_tags => $config->meta_tags,
                            'yes' => __('yes'),
                            'no' => __('no'),
                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                </div>

                <div class="mt-3 mb-3">
                    <p>Отслеживать союзы, предлоги, местоимение по умолчанию</p>
                    {!! Form::select('parts_of_speech', array_unique([
                            $config->parts_of_speech => $config->parts_of_speech,
                            'yes' => __('yes'),
                            'no' => __('no'),
                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                </div>

                <div class="mt-3 mb-3">
                    <div>
                        Исключать слова по умолчанию
                    </div>

                    {!! Form::select('remove_my_list_words', array_unique([
                            $config->remove_my_list_words => $config->remove_my_list_words,
                            'yes' => __('yes'),
                            'no' => __('no'),
                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                </div>

                <div class="form-group required list-words mt-1">
                    <label for="my_list_words">Список исключаемых слов</label>
                    {!! Form::textarea('my_list_words', $config->my_list_words ,['class' => 'form-control', 'cols' => 8, 'rows' => 5]) !!}
                </div>
            </div>

            <div class="mt-3 mb-3">
                <div class="mt-3 mb-3">
                    <p>Количество записей в таблице tlp по умолчанию</p>
                    {!! Form::select('ltp_count', array_unique([
                            $config->ltp_count => $config->ltp_count,
                            '10' => 10,
                            '25' => 25,
                            '50' => 50,
                            '100' => 100,
                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                </div>

                <div class="mt-3 mb-3">
                    <p>Количество записей в таблице tlps по умолчанию</p>
                    {!! Form::select('ltps_count', array_unique([
                            $config->ltps_count => $config->ltps_count,
                            '10' => 10,
                            '25' => 25,
                            '50' => 50,
                            '100' => 100,
                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                </div>

                <div class="mt-3 mb-3">
                    <p>Количество записей в таблице проанализированных сайтов по умолчанию</p>
                    {!! Form::select('scanned_sites_count', array_unique([
                            $config->scanned_sites_count => $config->scanned_sites_count,
                            '10' => 10,
                            '25' => 25,
                            '50' => 50,
                            '100' => 100,
                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                </div>

                <div class="mt-3 mb-3">
                    <p>Количество записей в таблице рекомендаций по умолчанию</p>
                    {!! Form::select('recommendations_count', array_unique([
                            $config->recommendations_count => $config->recommendations_count,
                            '10' => 10,
                            '25' => 25,
                            '50' => 50,
                            '100' => 100,
                    ]), null, ['class' => 'custom-select rounded-0 w-25']) !!}
                </div>
            </div>

            <div class="d-flex mt-3 mb-3">
                <div>
                    <label for="boostPercent">добавить % к охвату</label>
                    <input name="boostPercent" type="number" class="form form-control"
                           value="{{ $config->boostPercent }}">
                </div>
            </div>
            <input type="submit" value="Изменить стартовую конфигурацию" class="btn btn-secondary">
        </div>
    </form>
@endif
