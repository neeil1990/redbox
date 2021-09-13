@component('component.card', ['title' => __('Counting text length')])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div>
        <p class="w-50 mb-3">
            {{__('This tool will instantly calculate how many characters and spaces are in your text, as well as the number of characters without spaces and the number of words in the text.')}}
        </p>
        <p class="w-50 mt-3 mb-3">
            {{__('If you are typing not in a text editor, but in a notepad or browser, then this tool will become your faithful assistant.')}}
        </p>
        <h2>{{__("Enter text")}}</h2>
        <form>
            <textarea name="text"
                      class="form-control col-lg-6 col-sm-12"
                      id="text"
                      rows="10"
                      required></textarea>
            <br>
            <input class="btn btn-secondary mr-2" type="button" value="{{__('Calculate')}}">
            <input class="btn btn-flat btn-default" id="reset" type="reset" value="{{__('Clear')}}"
                   onclick="clearCountingResult();">
        </form>
        <div id="text-length-result" class="mt-3">
            <div id="all-text">
                <b>{{__('Total characters')}}: </b>
                <span class="counting-result text-length"></span>
            </div>
            <div id="spaces">
                <b>{{__('Total spaces')}}: </b>
                <span class="counting-result total-spaces"></span>
            </div>
            <div id="no-spaces">
                <b>{{__('Total characters without spaces')}}: </b>
                <span class="counting-result lengthWithOutSpaces">
                    </span>
            </div>
            <div id="words">
                <b>{{__('Total words')}}: </b>
                <span class="counting-result countWord"></span>
            </div>
        </div>
    </div>
    @slot('js')
        <script src="{{ asset('plugins/text-length/js/text-length.js') }}"></script>
        <script>
            $(document).ready(function () {
                $(".btn.btn-secondary.mr-2").click(function () {
                    let token = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('counting.text.length') }}",
                        data: {
                            text: document.getElementById('text').value,
                            _token: token
                        },
                        xhr: function () {
                            var xhr = $.ajaxSettings.xhr(); // получаем объект XMLHttpRequest
                            xhr.upload.addEventListener('progress', function (evt) { // добавляем обработчик события progress (onprogress)
                                if (evt.lengthComputable) { // если известно количество байт
                                    console.log(evt.loaded)
                                    console.log(evt.total)
                                }
                            }, false);
                            return xhr;
                        },
                        success: function (response) {
                            document.getElementById('text-length-result').style.display = 'block'
                            document.querySelector('.text-length').innerText = response.data.length
                            document.querySelector('.total-spaces').innerText = response.data.countSpaces
                            document.querySelector('.lengthWithOutSpaces').innerText = response.data.lengthWithOutSpaces
                            document.querySelector('.countWord').innerText = response.data.countWords
                        },

                        // complete: function (success) {
                        //     console.log('c')
                        //     console.log(success.success);
                        // },
                    });
                });
            });

        </script>
    @endslot
@endcomponent
