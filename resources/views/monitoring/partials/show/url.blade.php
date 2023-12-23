<a href="#" @if($urls->count()) data-toggle="popover" @endif title="URL" data-content="{{ view('monitoring.partials.show.popover.urls', ['urls' => $urls]) }}">
    <span class="badge badge-light">
        <i class="fas fa-link"></i>
        <span class="{{ $textClass }} text-sm text-bold">{{ $urls->count() }}</span>
    </span>
</a>
