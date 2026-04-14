<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>Выбор проекта (Анализ релевантности)</div>
        <button class="btn btn-sm btn-outline-secondary" type="button" data-toggle="collapse" data-target="#project-card-body" aria-expanded="false" aria-controls="project-card-body">
            <i class="fa fa-eye"></i>
        </button>
    </div>
    <div id="project-card-body" class="collapse">
        <div class="card-body">
            <div class="form-group">
                <label>Проект</label>
                <select id="project-select" class="form-control">
                    <option value="">Загрузка проектов...</option>
                </select>
            </div>

            <div class="form-group mt-3 mb-3">
                <label>Фраза / страница</label>
                <select id="relevance-select" class="form-control" disabled>
                    <option value="">Сначала выберите проект</option>
                </select>
            </div>

            <div class="form-text text-muted mb-3">
                Выбор проекта не обязателен — вы можете указать ссылку вручную.
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $.get('/relevance-projects', function(data) {
            let options = '<option value="">Выберите проект</option>';
            
            data.forEach(function(project) {
                options += `<option value="${project.id}">${project.name}</option>`;
            });

            $('#project-select').html(options);
            $('#project-select').trigger('change');
            
        }).fail(function() {
            $('#project-select').html('<option value="">Ошибка загрузки проектов</option>');
        });

        $('#relevance-select').select2({
            placeholder: "Выберите фразу",
            allowClear: true,
            width: '100%'
        });

        $('#project-select').select2({
            placeholder: "Выберите проект",
            allowClear: true,
            width: '100%'
        });

        $('#project-select').on('change', function () {
            let projectId = $(this).val();

            if (projectId) {
                $('#category-link')
                    .prop('readonly', true)
                    .addClass('bg-light');
            } else {
                $('#category-link')
                    .prop('readonly', false)
                    .removeClass('bg-light');
            }

            $('#relevance-select').prop('disabled', true).html('<option>Выберите проект</option>');

            if (!projectId) return;

            $.get(`/relevance-history/${projectId}`, function (data) {
                data.sort((a, b) => {
                    return a.phrase.localeCompare(b.phrase);
                });

                let options = '<option value="">Выберите</option>';
                data.forEach(item => {
                    options += `<option 
                        value="${item.main_link}" 
                        data-id="${item.id}">
                        ${item.phrase} | ${item.main_link} | ${item.created_at}
                    </option>`;
                });

                $('#relevance-select').html(options).prop('disabled', false);
            });
        });

        $('#relevance-select').on('change', function () {
            let projectId = $('#project-select').val();
            let link = $(this).val();

            if (!projectId) return;

            $('#category-link').val(link).trigger('input');

            $.get(`/relevance-history/getPhrases/${projectId}`, function (response) {
                console.log('Ответ phrases:', response);

                let phrases = response.phrases;

                $('#keywords-table tbody').html('');

                for (let word in phrases) {
                    let item = phrases[word];

                    let avg = parseFloat(item.avgInTotalCompetitors) || 0;
                    let total = parseFloat(item.totalRepeatMainPage) || 0;

                    if (avg > total) {
                        let diff = Math.ceil(avg - total);

                        let row = `
                            <tr>
                                <td><input type="text" class="form-control" name="keywords[]" value="${word}"></td>
                                <td><input type="number" class="form-control" name="counts[]" value="${diff}"></td>
                                <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                            </tr>
                        `;

                        $('#keywords-table tbody').append(row);
                    }
                }
            });
        });
    });
</script>