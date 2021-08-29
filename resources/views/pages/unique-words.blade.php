@component('component.card', ['title' => __('Highlighting unique words in the text')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/unique-words/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/unique-words/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/unique-words/css/style.css') }}"/>
    @endslot
    <form method="POST" action="{{  route('unique.words') }}">
        @csrf
        <h2 class="mt-3 mb-3">{{__('Get a list of unique words from the list of keywords')}}</h2>
        <div class="d-flex flex-column unique-words">
            <div class="d-flex flex-row justify-content-between">
                <label>{{__('List of keywords')}}</label>
                <div class="count-phrases">{{__('count phrases')}}:
                    <span id="countPhrases">0</span>
                </div>
            </div>
            <textarea class="form-control"
                      name="phrases"
                      rows="7"
                      id="phrases"
                      required>{{\Illuminate\Support\Facades\Input::old('phrases')}}</textarea>
        </div>
        <input class="btn btn-secondary mt-2 mr-2" type="submit" value="{{__('Processing')}}">
    </form>
    @if (\Illuminate\Support\Facades\Session::has('listWords'))
        <fieldset class="unique-words-filter mt-4 mb-3">
            <legend>{{__('Additionally')}}</legend>
            <div class="d-flex mt-2 mb-2">
                <div class="w-auto">
                    <p class="mr-3">{{__('Delete lines where the number of occurrences:')}}</p>
                </div>
                <div class="d-flex flex-column w-auto mr-1 ml-4">
                    <p>{{__('greater than or equal to:')}}</p>
                    <input type="number" min="1" id="greaterOrEqual" class="form-control">
                </div>
                <div class="d-flex flex-column w-auto ml-1 mr-4">
                    <p>{{__('less than or equal to:')}}</p>
                    <input type="number" min="1" id="lessOrEqual" class="form-control">
                </div>
                <div class="w-auto d-flex flex-column-reverse ml-3">
                    <input type="button" class="btn btn-secondary btn-flat" value="{{__('Remove')}}"
                           onclick="deleteItems()">
                </div>
            </div>
            <div class="d-flex row mt-3 mb-3">
                <form class="col-sm-12 d-flex flex-column" method="POST" action="{{ route('download.unique.words') }}">
                    @csrf
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox"
                               id="unique-word"
                               name="uniqueWord"
                               class="custom-control-input">
                        <label for="unique-word" class="custom-control-label">
                            {{__('Word')}}
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox"
                               id="unique-word-forms"
                               name="uniqueWordForms"
                               class="custom-control-input">
                        <label for="unique-word-forms" class="custom-control-label">
                            {{__('Word forms')}}
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox"
                               id="number-occurrences"
                               name="numberOccurrences"
                               class="custom-control-input">
                        <label for="number-occurrences" class="custom-control-label">
                            {{__('Number of occurrences')}}
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox"
                               id="key-phrases"
                               name="keyPhrases"
                               class="custom-control-input">
                        <label for="key-phrases" class="custom-control-label">
                            {{__('Key phrases')}}
                        </label>
                    </div>
                    <div class="flex">
                            <span class="__helper-link ui_tooltip_w btn btn-default mt-2" onclick="saveInBuffer()">
                            <i aria-hidden="true" class="fa fa-clipboard"></i>
                                <span class="ui_tooltip __right __l">
                                    <span class="ui_tooltip_content">
                                        {{__('Copy to Clipboard')}}
                                    </span>
                                </span>
                            </span>
                        <input type="hidden" id="extraId" name="extraId">
                        <input type="hidden"
                               name="phrases"
                               value="{{\Illuminate\Support\Facades\Input::old('phrases')}}">

                        <button class="btn btn-default mt-2 __helper-link ui_tooltip_w">
                            <i aria-hidden="true" class="fa fa-download"></i>
                            <span class="ui_tooltip __right __l">
                                    <span class="ui_tooltip_content">
                                        {{__('Upload as a file')}}
                                    </span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </fieldset>
        <div class="card mt-3 mb-3">
            <div class="card-header border-bottom">
                <h2 class="card-title">{{__('Result')}}</h2>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-valign-middle">
                    <thead>
                    <tr>
                        <th></th>
                        <th>{{__('Word')}}</th>
                        <th>{{__('Word forms')}}</th>
                        <th>{{__('Number of occurrences')}}</th>
                        <th>{{__('Key phrases')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(\Illuminate\Support\Facades\Session::get('listWords') as $key => $list)
                        <tr id="unique-words-id-{{$key}}" class="unique-result">
                            <td>
                                <i class="fa fa-trash" onclick="deleteItem({{$key}})"></i>
                            </td>
                            <td class="unique-word">{{$list['word']}}</td>
                            <td class="unique-word-form">{{$list['wordForms']}}</td>
                            <td class="number-occurrences">
                                {{$list['numberOccurrences']}}
                            </td>
                            <td class="d-flex flex-column unique-key-phrases">
                                <form action="{{ route('download.unique.phrases') }}" method="POST">
                                    @csrf
                                    <div class="flex-column">
                                    <span class="__helper-link ui_tooltip_w mr-1 btn btn-default mb-1"
                                          onclick="savePhrasesInBuffer({{$key}})">
                                        <i aria-hidden="true" class="fa fa-clipboard"></i>
                                        <span class="ui_tooltip __left __l">
                                            <span class="ui_tooltip_content">
                                                {{__('Copy to Clipboard')}}
                                            </span>
                                        </span>
                                    </span>
                                        <span class="__helper-link ui_tooltip_w">
                                        <button class="btn btn-default  mb-1">
                                            <i aria-hidden="true" class="fa fa-download"></i>
                                        </button>
                                        <span class="ui_tooltip __right __l">
                                            <span class="ui_tooltip_content">
                                                {{__('Upload as a file')}}
                                            </span>
                                        </span>
                                    </span>
                                    </div>
                                    <textarea
                                        name="keyPhrases"
                                        id="key-phrases-{{$key}}"
                                        rows="3"
                                        class="form-control key-phrases-result"
                                    >@foreach($list['keyPhrases'] as $phrases){{$phrases . "\n"}}@endforeach</textarea>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    @slot('js')
        <script src="{{ asset('plugins/unique-words/js/unique-words.js') }}"></script>
    @endslot
@endcomponent
