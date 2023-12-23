<div id="{{ $target }}-part" data-action="{{ $target }}" class="content" role="tabpanel" aria-labelledby="{{ $target }}-part-trigger">
    @include("monitoring.partials.stepper.inputs.$target")

    @include('monitoring.partials.stepper._buttons')
</div>
