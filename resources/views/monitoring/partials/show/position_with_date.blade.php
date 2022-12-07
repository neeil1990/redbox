
<span data-position="{{ $model->position }}" style="display: block;">
    {{ $model->position }}
    @if($model->diffPosition)
        <sup class="text-sm">@if($model->diffPosition > 0)+@endif{{ $model->diffPosition }}</sup>
    @endif
</span>

<div class="badge badge-info">{{ $model->date }}</div>

