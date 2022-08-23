@foreach($urls as $u)
    <span class="text-nowrap">{{ $u->created_at->format('d M Y H:i:s') }} <a href="{{ $u->url }}">{{ $u->url ?? 'Удалён' }}</a></span><br />
@endforeach

