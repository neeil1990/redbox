@component('component.card', ['title' => __('Add group')])
    <div class="card-body">
        <div class="mb-3">
            <a href="{{ route('partners.add.group') }}" class="btn btn-outline-secondary">{{ __('Add group') }}</a>
            <a href="{{ route('partners.add.item') }}" class="btn btn-outline-secondary">{{ __('Add partner') }}</a>
            <a href="{{ route('partners.admin') }}"
               class="btn btn-outline-secondary">{{ __('Partners (admins)') }}</a>
            <a href="{{ route('partners') }}" class="btn btn-outline-secondary">{{ __('Partners (users)') }}</a>
        </div>
        <form action="{{ route('partners.save.group') }}" method="POST" class="w-50">
            @csrf
            <div class="form-group required">
                <label>{{ __('Group Name') }} (ru)</label>
                <input type="text" name="name_ru" class="form form-control" required>
            </div>

            <div class="form-group required">
                <label>{{ __('Group Name') }} (en)</label>
                <input type="text" name="name_en" class="form form-control" required>
            </div>

            <div class="form-group required">
                <label>{{ __('Position') }}</label>
                <input type="number" name="position" class="form form-control" required>
            </div>

            @if ($errors->any())
                <div class="mb-3 mt-3">
                    @foreach ($errors->all() as $error)
                        <div class="text-danger">{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <input type="submit" class="btn btn-secondary" value="{{ __('Add') }}">
        </form>
    </div>
@endcomponent
