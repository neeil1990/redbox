<span data-position="{{ $model->position }}">
    {{ $model->position }}
    @if($model->diffPosition)
        <sup class="text-sm">@if($model->diffPosition > 0)+@endif{{ $model->diffPosition }}</sup>
    @endif
</span>
