<span class="query-string">
    {{ $key->query }}
</span>

@if($key->page)
    <a href="{{ $key->page }}" data-toggle="popover" title="Целевой URL" data-content="{{ view('monitoring.partials.show.popover.url', ['url' => $key->page]) }}">
        <span class="badge badge-light"><i class="fas fa-link"></i></span>
    </a>
@endif


