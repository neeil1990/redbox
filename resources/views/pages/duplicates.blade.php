@component('component.card', ['title' => __('Duplicates')])

    <textarea id="strings" type="text" class="form-control" rows="10"></textarea>
    <br/>
    <input type="button" id="start" class="btn btn-success" name="delete" value="Удалить дубликаты">

    @slot('js')
        <script type="text/javascript">
            $(function () {
                $("#start").click(function(){
                var strings = $('#strings').val().split(/[\r\n]+/);
                var unique = [];
                top:for (var i in strings) {
                    for (var j in unique) {
                        if (strings[i] == unique[j]) {
                            continue top;
                        }
                    }unique.push(strings[i]);
                }
                $('#strings').val(unique.join("\r\n"));
                });
            });
        </script>
    @endslot
@endcomponent
