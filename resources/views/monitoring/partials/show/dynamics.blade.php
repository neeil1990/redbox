@if($dynamics === 0)
    <span class="dynamics"> - </span>
@else
    @if($dynamics > 0)
        <span class="dynamics text-green"> +{{ $dynamics }} </span>
    @else
        <span class="dynamics text-red"> {{ $dynamics }} </span>
    @endif
@endif
