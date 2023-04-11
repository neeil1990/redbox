@component('component.card', ['title' => __('Counting text length')])
    @slot('css')
        <style>
            .TextLength {
                background: oldlace;
            }
        </style>
    @endslot
    <div>
        <h3>{{__("Enter text")}}</h3>
        <form>
            <textarea name="text" class="form-control col-12" id="text" rows="10" required></textarea>
            <br>
            <input class="btn btn-secondary mr-2" type="button" value="{{__('Calculate')}}">
            <input class="btn btn-flat btn-default" id="reset" type="reset" value="{{__('Clear')}}"
                   onclick="clearCountingResult();">
        </form>
        <div id="progress-bar">
            <div class="progress-bar mt-3 mb-3" role="progressbar"></div>
        </div>
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
                            let xhr = $.ajaxSettings.xhr();
                            document.querySelector('.progress-bar').style.opacity = 1
                            $("#progress-bar").show(300)
                            xhr.upload.addEventListener('progress', function (evt) {
                                if (evt.lengthComputable) {
                                    let percent = Math.floor((evt.loaded / evt.total) * 100);
                                    setProgressBarStyles(percent)
                                    if (percent === 100) {
                                        setTimeout(() => {
                                            document.querySelector('.progress-bar').style.opacity = 0
                                            document.querySelector('.progress-bar').style.width = 0 + '%'
                                            $("#progress-bar").hide(300)
                                        }, 2000)
                                    }
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
                    });
                });
            });

            function setProgressBarStyles(percent) {
                document.querySelector('.progress-bar').style.width = percent + '%'
                document.querySelector('.progress-bar').innerText = percent + '%'
            }
        </script>
    @endslot
@endcomponent
