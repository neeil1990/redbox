@component('component.card', ['title' => 'Адаптивный промпт'])
    @slot('css')
        <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        
        <style>
            .history-container {
                height: 600px;
                display: flex;
                flex-direction: column;
            }

            .history-table-wrapper {
                flex-grow: 1;
                overflow-y: auto;
                overflow-x: auto;
            }

            .history-sidebar .dataTables_wrapper {
                display: flex;
                flex-direction: column;
                height: 100%;
            }

            .card-body::after, .card-footer::after, .card-header::after {
                display: none;
            }
            .select2-container .select2-selection--single {
                height: 38px !important;
                border: 1px solid #ced4da !important;
            }
            .text-truncate-3 {
                display: -webkit-box;
                -webkit-box-orient: vertical;  
                overflow: hidden;
            }
            .history-sidebar .dataTables_filter {
                width: 100%;
                margin: 0;
                padding-bottom: 10px;
            }
            .history-sidebar .dataTables_filter input {
                width: 100% !important;
                margin-left: 0 !important;
                border-radius: 20px;
            }
        </style>
        <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.css') }}">
    @endslot

    <div class="card">
        <div class="card-header d-flex p-0">
            @include('ai-generation.blocks.nav')
        </div>
        <div class="card-body">
            
            <div class="row">
                <div class="col-lg-8 col-md-12">
                    
                    <div class="form-group mb-4">
                        <label>Промпт для генерации</label>
                        <textarea id="prompt-text" class="form-control" rows="12" style="background: #f8f9fa;"></textarea>
                    </div>

                    <div class="form-group mt-3 mb-4">
                        <label for="category-link">Посадочная страница</label>
                        <input type="text" class="form-control mb-3" id="category-link" placeholder="https://example.com/category/...">
                            
                        <div class="mt-4">
                            <button class="btn btn-success generate-button" data-mode="new">
                                Сгенерировать текст
                            </button>
                        </div>

                        <hr>

                        <label class="d-block mb-2">Способ анализа страницы</label>
                        
                        <div class="custom-control custom-radio mb-3">
                            <input type="radio" id="source-ai" name="parsing_method" class="custom-control-input" value="ai_database" checked>
                            <label class="custom-control-label" for="source-ai">
                                Достать информацию из базы данных AI
                                <small class="d-block text-muted mt-1">
                                    Использует встроенные знания нейросети. Быстро и без дополнительных затрат.
                                </small>
                            </label>
                        </div>

                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="source-parse" name="parsing_method" class="custom-control-input" value="parse_html">
                            <label class="custom-control-label" for="source-parse">
                                Пропарсить текущий HTML
                                <span class="badge badge-warning ml-2">Высокая стоимость</span>
                                <small class="d-block text-muted mt-1">
                                    Система получит актуальный видимый HTML и автоматически подставит его в промпт. <b class="text-danger">Внимание:</b> этот метод потребляет много токенов и значительно увеличивает цену запроса.
                                </small>
                            </label>
                        </div>
                    </div>

                    @include('ai-generation.blocks.relevance')
                    @include('ai-generation.blocks.words')

                    <div id="generation-result" class="d-none mt-4">
                        <div id="generation-loading" class="text-muted">
                            <div class="d-flex align-items-center mt-4">
                                <span id="loading-text">Генерация текста</span>
                                <div class="spinner-border text-primary ml-2 me-2" role="status"></div>
                            </div>
                        </div>

                        <div id="generation-success" class="d-none">
                            <div class="row">
                                <div class="col-md-8">
                                    <textarea id="result-text" class="form-control" rows="12"></textarea>

                                    <button class="btn btn-primary mt-2" id="copy-result">
                                        Скопировать
                                    </button>
                                </div>

                                <div class="col-md-4">
                                    <div class="card p-3">
                                        <h6>Примечание</h6>

                                        <textarea id="regenerate-note"
                                                class="form-control mb-2"
                                                rows="6"
                                                placeholder="Например: сделай текст более продающим, добавь преимущества, упростить стиль..."></textarea>

                                        <button class="btn btn-warning w-100 mb-2 generate-button" data-mode="new">
                                            Новая генерация с примечанием
                                        </button>

                                        <button class="btn btn-info w-100 generate-button" data-mode="regenerate">
                                            Перегенерация полученного результата
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="card card-outline card-secondary history-sidebar" style="top: 22px;">
                        <div class="card-header d-flex justify-content-between align-items-center p-2">
                            <h3 class="card-title m-0" style="font-size: 1.1rem;">История запросов</h3>
                            <input type="text" id="custom-history-search" class="form-control form-control-sm" style="width: 150px;" placeholder="Поиск...">
                        </div>
                        
                        <div class="card-body p-0 history-container"> <div class="history-table-wrapper">
                                <table id="sidebar-history-table" class="table table-hover w-100" style="border-top: none; table-layout: fixed;">
                                    <thead style="display: none;"><tr><th>Данные</th></tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/lang/summernote-ru-RU.js') }}"></script>
        <script src="{{ asset('plugins/select2/js/select2.js') }}"></script>
        <script src="{{ asset('plugins/select2/js/profile.js') }}"></script>
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
        
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>

        <script>
            let historyTable;

            $(document).ready(function() {
                $('#result-text').summernote({
                    height: 300,
                    lang: 'ru-RU',
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['view', ['codeview']]
                    ]
                });

                const defaultPrompt = `Роль: \nТы — профессиональный копирайтер.\n\nЗадача:\nСоставь уникальный текст для категории товаров, которая расположена по ссылке: {link}. \nДля составления текста используй реальное содержимое указанной страницы.\n\nТекст должен быть составлен таким образом, чтобы его можно было разместить на сайте в качестве SEO-текста для привлечения клиентов. Достаточно составить один вариант текста.\n\nУникальность и грамотность:\nТекст должен быть полностью уникальным (не скопирован с других сайтов).\nПредложения должны быть грамотными, правильными с точки зрения русского языка и легко читаться.`;

                $('#prompt-text').val(defaultPrompt);

                historyTable = $('#sidebar-history-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('ai.generation.history.json') }}",
                        type: "POST",
                        data: function(d) {
                            d._token = "{{ csrf_token() }}";
                        }
                    },
                    columns: [
                        { 
                            data: null, 
                            orderable: false,
                            render: function (data, type, row) {
                                const isParse = row.source === 'parse_html';
                                const badgeClass = isParse ? 'badge-primary' : 'badge-success';
                                const sourceText = isParse ? 'Парсинг HTML' : 'AI DB';
                                const keywordsAttr = encodeURIComponent(JSON.stringify(row.keywords || []));
                                const stopwordsAttr = encodeURIComponent(JSON.stringify(row.stopwords || []));
                                
                                let statusBadge = '';
                                if(row.status === 'pending') statusBadge = '<span class="badge badge-info ml-1">В очереди</span>';
                                else if(row.status === 'failed') statusBadge = '<span class="badge badge-danger ml-1">Ошибка</span>';

                                let cleanPrompt = row.prompt ? row.prompt.replace(/<\/?[^>]+(>|$)/g, "") : '';
                                cleanPrompt = cleanPrompt.substring(0, 120) + (cleanPrompt.length > 120 ? '...' : '');

                                return `
                                <div class="p-2">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>${row.date} ${statusBadge}</span>
                                        <span class="badge ${badgeClass}">${sourceText}</span>
                                    </div>
                                    <div class="text-truncate font-weight-bold mb-1 small">
                                        <i class="fas fa-link mr-1"></i> ${row.link.substring(0, 70) + (row.link.length > 70 ? '...' : '')}
                                    </div>

                                    <div class="text-muted small mb-2 text-truncate-3" style="font-size: 0.8rem; line-height: 1.2;">
                                        ${cleanPrompt}
                                    </div>
                                    <button class="btn btn-xs btn-block btn-outline-primary apply-history" 
                                        data-prompt="${encodeURIComponent(row.prompt || '')}"
                                        data-link="${row.link}"
                                        data-source="${row.source}"
                                        data-keywords="${keywordsAttr}"
                                        data-stopwords="${stopwordsAttr}">
                                        Применить этот промпт
                                    </button>
                                </div>`;
                            }
                        }
                    ],
                    paging: true,
                    lengthChange: false,
                    searching: true,
                    ordering: false,
                    info: false,
                    pageLength: 5,
                    language: {
                        emptyTable: "История пуста",
                        paginate: { next: "»", previous: "«" }
                    },
                    dom: 'rt<"d-flex justify-content-center mt-2"p>'
                });

                $('#custom-history-search').on('keyup search input', function () {
                    historyTable.search(this.value).draw();
                });

                $('#sidebar-history-table').on('click', '.apply-history', function() {
                    let btn = $(this);
                    let prompt = decodeURIComponent(btn.data('prompt'));
                    let link = btn.data('link');
                    let source = btn.data('source');
                    
                    let keywordsData = [];
                    let stopwordsData = [];
                    
                    try {
                        keywordsData = JSON.parse(decodeURIComponent(btn.data('keywords') || '[]'));
                        stopwordsData = JSON.parse(decodeURIComponent(btn.data('stopwords') || '[]'));
                    } catch (e) {
                        console.error("Ошибка парсинга слов:", e);
                    }

                    $('#prompt-text').val(prompt);
                    $('#category-link').val(link).trigger('input');
                    $(`input[name="parsing_method"][value="${source}"]`).prop('checked', true);

                    if (window.applyWordsFromHistory) {
                        window.applyWordsFromHistory(keywordsData, stopwordsData);
                    }

                    toastr.success('Конфигурация и списки слов восстановлены');
                });

                $('#category-link').on('input', function () {
                    let val = $(this).val().trim();

                    if (!val) {
                        $('#preview-link').text('[ссылка не указана]').removeClass('text-success text-danger').addClass('text-muted');
                        $(this).removeClass('is-valid is-invalid');
                        return;
                    }

                    if (isValidUrl(val)) {
                        $('#preview-link').text(val).removeClass('text-danger text-muted').addClass('text-success');
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    } else {
                        $('#preview-link').text('Некорректная ссылка').removeClass('text-success text-muted').addClass('text-danger');
                        $(this).removeClass('is-valid').addClass('is-invalid');
                    }
                });
            });

            let lastId = null;

            $('.generate-button').click(function () {
                let mode = $(this).data('mode');
                let link = $('#category-link').val().trim();
                let note = $('#regenerate-note').val().trim();
                let customPrompt = $('#prompt-text').val();
                let source = $('#source-parse').is(':checked') ? 'parse_html' : 'ai_database';

                let isRegenerate = (mode === 'regenerate');

                if (isRegenerate) {
                    $('#loading-text').text('Перегенерация текущего текста...');
                } else if (note) {
                    $('#loading-text').text('Новая генерация с примечанием...');
                } else {
                    $('#loading-text').text('Генерация текста...');
                }

                if (!isValidUrl(link)) {
                    $('#category-link').removeClass('is-valid').addClass('is-invalid');
                    toastr.error('Введите корректную ссылку');
                    return;
                }

                $.ajax({
                    url: "{{ route('ai.generation.prompt.generate') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        link: link,
                        prompt: customPrompt,
                        source: source,
                        keywords: typeof getWords === 'function' ? getWords() : [],
                        stopwords: typeof getStopWords === 'function' ? getStopWords() : [],
                        note: note,
                        mode: mode,
                        current_text: isRegenerate ? $('#result-text').val() : null,
                        id: lastId,
                    },
                    beforeSend: function () {
                        $('.generate-button').prop('disabled', true);
                        $('#generation-loading').removeClass('d-none');
                        $('#generation-success').addClass('d-none');
                    },
                    success: function (response) {
                        toastr.success('Запрос отправлен');
                        let recordId = response.record_id;

                        $('#generation-result').removeClass('d-none');
                        $('#generation-loading').removeClass('d-none');

                        historyTable.ajax.reload(null, false);

                        startPolling(recordId);
                    },
                    error: function (xhr) {
                        toastr.error('Ошибка при генерации');
                        console.error(xhr.responseText);
                        $('.generate-button').prop('disabled', false);
                    },
                });
            });

            $('#copy-result').click(function () {
                let html = $('#result-text').summernote('code');
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(html).then(function () {
                        toastr.success('Скопировано!');
                    });
                } else {
                    if(typeof fallbackCopyTextToClipboard === 'function') {
                        fallbackCopyTextToClipboard(html);
                    }
                }
            });

            function isValidUrl(string) {
                try {
                    let url = new URL(string);
                    return url.protocol === "http:" || url.protocol === "https:";
                } catch (_) {
                    return false;
                }
            }

            function startPolling(recordId) {
                let interval = setInterval(function () {
                    $.ajax({
                        url: "/ai-generation/get-result/" + recordId,
                        method: "GET",
                        success: function (response) {
                            if (response.record) {
                                if (response.record.status === 'completed' || response.record.status === 'failed') {
                                    $('.generate-button').prop('disabled', false);
                                    clearInterval(interval);
                                    
                                    historyTable.ajax.reload(null, false);

                                    if (response.record.status === 'failed') {
                                        toastr.error('Ошибка генерации. Возможно, таймаут или проблема с API.');
                                        $('#generation-loading').addClass('d-none');
                                        return;
                                    }

                                    $('#generation-loading').addClass('d-none');
                                    $('#generation-success').removeClass('d-none');
                                    $('#result-text').summernote('code', response.record.result);

                                    lastId = response.record.id;
                                    toastr.success('Генерация успешно завершена!');
                                }
                            }
                        },
                        error: function () {
                            console.log('Ошибка при проверке статуса');
                        }
                    });
                }, 3000);
            }
        </script>
    @endslot
@endcomponent