@component('component.card', ['title' => __('Edit Group')])
    <div class="card-body">
        <div class="mb-3">
            <a href="{{ route('partners.add.group') }}" class="btn btn-outline-secondary">{{ __('Add group') }}</a>
            <a href="{{ route('partners.add.item') }}" class="btn btn-outline-secondary">{{ __('Add partner') }}</a>
            <a href="{{ route('partners.admin') }}" class="btn btn-outline-secondary">{{ __('Partners (admins)') }}</a>
            <a href="{{ route('partners') }}" class="btn btn-outline-secondary">{{ __('Partners (users)') }}</a>
        </div>
        <form action="{{ route('partners.edit.save') }}" method="POST" class="w-50">
            @csrf
            <div class="form-group required">
                <label>{{ __('Group Name') }}</label>
                <input type="text" name="name" class="form form-control" value="{{ $group->name }}" required>
            </div>

            <div class="form-group required">
                <label>{{ __('Position') }}</label>
                <input type="number" name="position" class="form form-control" value="{{ $group->position }}"
                       required>
            </div>

            @if ($errors->any())
                <div class="mb-3 mt-3">
                    @foreach ($errors->all() as $error)
                        <div class="text-danger">{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <input type="hidden" name="id" value="{{ $group->id }}">
            <input type="submit" class="btn btn-secondary" value="{{ __('Save') }}">
        </form>

    </div>
@endcomponent
