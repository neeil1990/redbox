@component('component.card', ['title' => __('Remove Duplicates')])
    <remove-duplicates :names="{{ $options }}"
                       start="{{ __('remove characters at the beginning of a word') }}: +-!"
                       end="{{ __('remove characters at the end of a word') }}: .!?"
                       submit="{{ __('Remove duplicates') }}"
    ></remove-duplicates>

    @slot('js')
        <script>
            $('.RemoveDublicate').css({
                'background': 'oldlace'
            })

            $('#app > div > div > div.card-body > form').prepend(
                '<div class="d-flex justify-content-between">' +
                '   <div>' + "{{ __('Your text') }}" + '</div>' +
                '   <div>' + "{{ __('Count phrases') }}:" + ' <span id="countPhrases">0</span></div>' +
                '</div>'
            )

            $('#app > div > div > div.card-body > form > div.form-group > textarea').attr('id', 'eventListener')
            $('#eventListener').on('keyup', function () {
                let counter = calculate($(this))
                $('#countPhrases').html(counter)
            });

            function calculate(elem) {
                let numberLineBreaksInFirstList = 0;
                let firstList = elem.val().split('\n')
                for (let i = 0; i < firstList.length; i++) {
                    if (firstList[i] !== '') {
                        numberLineBreaksInFirstList++
                    }
                }

                return numberLineBreaksInFirstList;
            }

            $('#start').on('click', function () {
                let elem = $('#app > div > div > div.card-body > form > div.form-group > textarea')
                let oldVal = calculate(elem)
                let counter = 1
                let interval = setInterval(() => {
                    if (counter === 10) {
                        clearInterval(interval)
                    }

                    let newVal = calculate(elem)
                    if (newVal !== oldVal) {
                        elem.html(newVal)
                        clearInterval(interval)
                    }

                    counter++
                }, 1000)
            })
        </script>
    @endslot
@endcomponent
