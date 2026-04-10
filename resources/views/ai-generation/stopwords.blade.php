@component('component.card', ['title' => 'Категории'])
    <div class="card">
        <div class="card-header d-flex p-0">
            @include('ai-generation.blocks.nav')
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between mb-4">
                <form action="{{ route('ai.stopwords.store') }}" method="POST" class="form-inline">
                    @csrf
                    <input type="text" name="word" class="form-control mr-2" placeholder="Новое слово" required>
                    <button type="submit" class="btn btn-primary">Добавить</button>
                </form>

                <div class="w-25">
                    <input type="text" id="search-stopwords" class="form-control" placeholder="Поиск по списку...">
                </div>
            </div>

            <table class="table table-sm table-hover" id="stopwords-main-table">
                <thead>
                    <tr>
                        <th>Слово</th>
                        <th width="150" class="text-right">Действие</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($words as $word)
                    <tr class="word-row">
                        <td class="word-text">{{ $word->word }}</td>
                        <td class="text-right">
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-sm btn-outline-info mr-2 edit-word-btn" 
                                        data-id="{{ $word->id }}" 
                                        data-word="{{ $word->word }}">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <form action="{{ route('ai.stopwords.destroy', $word->id) }}" method="POST" onsubmit="return confirm('Удалить?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="editWordModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="edit-word-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Редактировать слово</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="word" id="edit-word-input" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @slot('js')
    <script>
        $(document).ready(function() {
            $('#search-stopwords').on('keyup', function() {
                let value = $(this).val().toLowerCase();
                $("#stopwords-main-table tbody tr").filter(function() {
                    $(this).toggle($(this).find('.word-text').text().toLowerCase().indexOf(value) > -1)
                });
            });

            $('.edit-word-btn').on('click', function() {
                let id = $(this).data('id');
                let word = $(this).data('word');
                
                $('#edit-word-form').attr('action', `/ai-stopwords/${id}`);
                $('#edit-word-input').val(word);
                
                $('#editWordModal').modal('show');
            });
        });
    </script>
    @endslot
@endcomponent