<ul class="nav nav-pills p-2" id="main-nav">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('ai.generation.prompt') }}">
            Адаптивный промпт
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('ai.macros.index') }}">Макросы</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('ai.stopwords.index') }}">
            Запрещённые слова
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('ai.generation.story') }}">
            История
        </a>
    </li>
    @if(App\User::isUserAdmin())
        <li class="nav-item">
            <a class="nav-link" href="{{ route('ai.generation.all.story') }}">
                Общая история
            </a>
        </li>
    @endif
</ul>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentUrl = window.location.href;

    document.querySelectorAll('#main-nav .nav-link').forEach(function(link) {
        let linkUrl = link.href;
        link.classList.remove('active');

        if (currentUrl === linkUrl) {
            link.classList.add('active');
        }
    });
});
</script>