@component('component.card', ['title' => __('List comparison')])

    <form action="{{  route('counting.list.comparison') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-sm-12 textpart">
                <h3>Сравните списки ключевых фраз и получите общий уникальный список.</h3>
            </div>
        </div>
        <div class="row mt-3 mb-3">
            <div class="col-sm-6 d-flex flex-column">
                <label class="font-weight-light">Первый список</label>
                <textarea class="form-control" name="firstList" rows="7"
                          required>{{\Illuminate\Support\Facades\Input::old('firstList')}}</textarea>
            </div>
            <div class="col-sm-6 d-flex flex-column">
                <label class="font-weight-light">Второй список</label>
                <textarea class="form-control" name="secondList" rows="7"
                          required>{{\Illuminate\Support\Facades\Input::old('secondList')}}</textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-8 d-flex flex-column">
                <label class="mt-3 mb-3">Тип сравнения:</label>
                <label title="список ключевых фраз, которые нашлись как в первом, так и во втором списке (пересечение)"
                       class="radio font-weight-light">
                    <input type="radio" name="option" value="unique" id="first-radio-option" checked
                           onclick="saveOptionState('first')"> Уникальные фразы,
                    которые есть в каждом из
                    двух
                    списков
                    <a href=""
                       title="список ключевых фраз, которые нашлись как в первом, так и во втором списке (пересечение)"
                       class="help-link">
                        <i aria-hidden="true" class="fa fa-question-circle"></i>
                    </a>
                </label>
                <label title="список ключевых фраз, которые нашлись в любом из списков (объединение)"
                       class="radio font-weight-light">
                    <input type="radio" name="option" value="union" id="second-radio-option"
                           onclick="saveOptionState('second')"> Уникальные фразы, которые
                    есть в любом из двух
                    списков
                    <a href="" title="список ключевых фраз, которые нашлись в любом из списков (объединение)"
                       class="help-link">
                        <i aria-hidden="true" class="fa fa-question-circle"></i>
                    </a>
                </label>
                <label title="список ключевых фраз, которые есть в первом списке, но нет во втором"
                       class="radio font-weight-light">
                    <input type="radio" name="option" value="uniqueInFirstList" id="third-radio-option"
                           onclick="saveOptionState('third')">
                    Уникальные фразы, которые есть только в первом списке
                    <a href="" title="список ключевых фраз, которые есть в первом списке, но нет во втором"
                       class="help-link">
                        <i aria-hidden="true" class="fa fa-question-circle"></i>
                    </a>
                </label>
                <label title="список ключевых фраз, которые есть во втором списке, но нет в первом"
                       class="radio font-weight-light">
                    <input type="radio" name="option" value="uniqueInSecondList" id="fourth-radio-option"
                           onclick="saveOptionState('fourth')">
                    Уникальные фразы, которые есть только во втором списке
                    <a href="" title="список ключевых фраз, которые есть во втором списке, но нет в первом"
                       class="help-link">
                        <i aria-hidden="true" class="fa fa-question-circle"></i>
                    </a>
                </label>
            </div>
            <div class="col-sm-8 mt-3 mb-3 mt-3">
                <input class="btn btn-secondary" type="submit" value="Обработать">
            </div>
        </div>
    </form>
    @if (\Illuminate\Support\Facades\Session::has('result'))
        <div class="result">
            <textarea name="result" id="comparison-result" class="form-control"
                      rows="10">{{\Illuminate\Support\Facades\Session::get('result')}}</textarea>
            <div class="col-sm-12 d-flex">
                <button onclick="saveOfBuffer()" class="btn btn-default mt-2 mr-2">
                    <i aria-hidden="true" class="fa fa-clipboard"></i>
                </button>
                <form action="{{route('download-comparison-file')}}" method="GET">
                    @csrf
                    <input type="hidden" value="{{\Illuminate\Support\Facades\Session::get('result')}}" name="result">
                    <a title="Загрузить в виде файла" class="pull-left ml-2 mr-2">
                        <button class="btn btn-default mt-2">
                            <i aria-hidden="true" class="fa fa-download"></i>
                        </button>
                    </a>
                </form>
            </div>
        </div>
    @endif
    @slot('js')
        <script>
            window.onload = function () {
                if (localStorage.getItem('radioOptionState') !== null) {
                    let index = localStorage.getItem('radioOptionState');
                    document.getElementById(index + '-radio-option').checked = true;
                }
            }

            function saveOptionState(index) {
                localStorage.setItem('radioOptionState', index)
            }

            function saveOfBuffer() {
                document.getElementById('comparison-result').select();
                document.execCommand('copy');
            }
        </script>
    @endslot
@endcomponent
