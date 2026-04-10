@component('component.card', ['title' => 'Анонсы'])
    @slot('css')
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
        <style>
            #prompt-preview {
                max-height: 450px;
                overflow-y: auto;
                overflow-x: hidden;
                padding: 10px;
                border: 1px solid #dee2e6;
                border-radius: 5px;
                background: #f8f9fa;
            }

            .card-body::after, .card-footer::after, .card-header::after {
                display: none;
            }

            .select2-container .select2-selection--single {
                height: 38px !important;
                border: 1px solid #ced4da !important;
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
            <div id="prompt-preview"></div>

            <div class="form-group mt-3 mb-3">
                <label>Исходный текст о товаре</label>
                <textarea id="source-text" class="form-control" rows="5" placeholder="Введите описание товара..."></textarea>
            </div>

            @include('ai-generation.blocks.words')

            <div class="mt-4">
                <button class="btn btn-success generate-button">
                    Сгенерировать текст
                </button>
            </div>

            <div id="generation-result" class="d-none mt-4">
                <div id="generation-loading" class="text-muted">
                    <div class="d-flex align-items-center mt-4">
                        <span id="loading-text">Генерация текста</span>
                        <div class="spinner-border text-primary ml-2 me-2" role="status"></div>
                    </div>
                </div>

                <div id="generation-success" class="d-none">
                    <div class="row">
                        <div class="col-md-12">
                            <textarea id="result-text" class="form-control" rows="12"></textarea>

                            <button class="btn btn-primary mt-2" id="copy-result">
                                Скопировать
                            </button>
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
        <script>
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
            });

            let lastId = null;

            $('.generate-button').click(function () {
                if($('#source-text').val() == '') {
                    $('#source-text').removeClass('is-valid').addClass('is-invalid');
                    toastr.error('Введите текст о товаре');
                    return;
                }

                $('#loading-text').text('Генерация текста...');

                $.ajax({
                    url: "{{ route('ai.generation.announcement.generate') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        keywords: getWords(),
                        stopwords: getStopWords(),
                        current_text: $('#source-text').val(),
                    },
                    beforeSend: function () {
                        $('.generate-button').prop('disabled', true);
                        $('#generation-loading').removeClass('d-none');
                    },
                    success: function (response) {
                        toastr.success('Запрос отправлен');
                        let recordId = response.record_id;

                        $('#generation-result').removeClass('d-none');
                        $('#generation-loading').removeClass('d-none');

                        startPolling(recordId);
                    },
                    error: function (xhr) {
                        toastr.error('Ошибка при генерации');
                        console.error(xhr.responseText);
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
                    fallbackCopyTextToClipboard(html);
                }
            });

            $(document).ready(function () {
                $('#keywords-table, #stopwords-table').on('input', 'input', updatePrompt);
                $(document).ready(updatePrompt);
            });
            
            function isValidUrl(string) {
                try {
                    let url = new URL(string);

                    return url.protocol === "http:" || url.protocol === "https:";
                } catch (_) {
                    return false;
                }
            }

            $(document).ready(function () {
                $('#source-text').on('input', function() {
                    let val = $(this).val().trim();

                    if (val.length > 0) {
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    } else {
                        $(this).removeClass('is-valid').addClass('is-invalid');
                    }
                    
                    updatePrompt();
                });

                $('#keywords-table, #stopwords-table').on('input', 'input', updatePrompt);

                updatePrompt();
            });

            function updatePrompt() {
                let sourceText = $('#source-text').val().trim() || '[Текст о товаре не введен]';

                let addWords = '';
                $('#keywords-table tbody tr').each(function () {
                    let word = $(this).find('input[name="keywords[]"]').val();
                    let count = $(this).find('input[name="counts[]"]').val();
                    if (word) {
                        addWords += `<br><b>Нужно добавить слово ${word} или любое его склонение, число, падеж ${count} раз(а), чтобы слово естественно вписывалось в текст.</b>`;
                    }
                });
                if (addWords) addWords += '<br>';

                let cancelWords = '';
                $('#stopwords-table tbody tr').each(function () {
                    let word = $(this).find('input[name="stopwords[]"]').val();
                    if (word) {
                        cancelWords += `<b>Запрещено использовать слово и любое его склонение, число или падеж: ${word}</b><br>`;
                    }
                });
                if (cancelWords) cancelWords += '<br>';

                let prompt = `
                    <b>Роль:</b> Ты — профессиональный копирайтер.<br><br>

                    <b>Исходные данные:</b><br>
                    Ниже представлен текст о товаре, на основе которого нужно составить преимущества:<br>
                    <i>${sourceText}</i><br><br>

                    <b>Задача:</b><br>
                    Составь список коротких тезисов, описывающих ключевые преимущества. Не выдумывай характеристики, которых нет в тексте.<br><br>

                    Ты обязан выполнить следующие требования:<br>
                    ${addWords}
                    Если ты не можешь вписать в текст слово, пропусти его.<br><br>

                    ${cancelWords}

                    <b>Формат:</b><br>
                    - Маркированный список.<br>
                    - Объем до 400 символов с пробелами.<br>
                    - Уникальный стиль (не копировать фразы дословно).<br>
                    - Текст должен быть полностью уникальным (не скопирован с других сайтов).<br>
                    - Предложения должны быть грамотными, правильными с точки зрения русского языка и легко читаться.<br>
                `;

                $('#prompt-preview').html(prompt);
            }

            function startPolling(recordId) {
                let interval = setInterval(function () {
                    $.ajax({
                        url: "/ai-generation/get-result/" + recordId,
                        method: "GET",
                        success: function (response) {
                            if (response.record) {
                                $('.generate-button').prop('disabled', false);

                                clearInterval(interval);

                                if (response.record.status === 'failed') {
                                    toastr.error('Ошибка генерации');
                                    return;
                                }

                                $('#generation-loading').addClass('d-none');
                                $('#generation-success').removeClass('d-none');
                                $('#result-text').summernote('code', response.record.result);

                                lastId = response.record.id;
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

