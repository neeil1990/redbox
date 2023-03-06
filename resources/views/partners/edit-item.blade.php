@component('component.card', ['title' => __('Edit information about partner ')])
    <div class="card-body">
        <div class="mb-3">
            <a href="{{ route('partners.add.group') }}" class="btn btn-outline-secondary">{{ __('Add group') }}</a>
            <a href="{{ route('partners.add.item') }}" class="btn btn-outline-secondary">{{ __('Add partner') }}</a>
            <a href="{{ route('partners.admin') }}" class="btn btn-outline-secondary">{{ __('Partners (admins)') }}</a>
            <a href="{{ route('partners') }}" class="btn btn-outline-secondary">{{ __('Partners (users)') }}</a>
        </div>
        <form action="{{ route('partners.save.edit.item') }}" method="POST" class="w-50"
              enctype="multipart/form-data">
            @csrf

            <div class="form-group required">
                <label>{{ __('Group Name') }}</label>
                <select name="partners_groups_id" id="partners_groups_id" class="custom-select">
                    <option value="{{ $item->partner->id }}">{{ $item->partner->name }}</option>
                    @foreach($groups as $group)
                        @if($item->partner->id === $group->id)
                            @continue
                        @endif
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group required">
                <label>{{ __('Position') }}</label>
                <input type="number" name="position" class="form form-control" value="{{ $item->position }}"
                       required>
            </div>

            <div class="form-group required">
                <label>{{ __('Partner name') }}</label>
                <input type="text" name="name" class="form form-control" value="{{ $item->name }}" required>
            </div>

            <div class="form-group required">
                <label>{{ __('Link') }}</label>
                <input type="text" name="link" class="form form-control" value="{{ $item->link }}" required>
            </div>

            <div class="form-group required">
                <label>{{ __('Partner description') }}</label>
                <textarea name="description" id="description" cols="8" rows="8"
                          class="form form-control">{{ $item->description }}</textarea>
            </div>

            <div class="form-group required">
                <label>{{ __('Auditorium') }}</label>
                <div>
                    <label for="auditorium_ru">Ru</label>
                    <input type="checkbox" name="auditorium_ru" @if($item->auditorium_ru) checked @endif>
                </div>
                <div>
                    <label for="auditorium_en">Eng</label>
                    <input type="checkbox" name="auditorium_en" @if($item->auditorium_en) checked @endif>
                </div>
            </div>

            <div class="form-group">
                <div>
                    <label>{{ __('Current Image') }}</label>
                    <img class="card-img-top" src="../../storage/{{ $item['image'] }}" alt="image"
                         style="width: 100px; height: 100px;">
                </div>

                {!! Form::label('image', __('Image')) !!}
                <div class="input-group">
                    <div class="custom-file">
                        {!! Form::file('image', ['class' => 'custom-file-input', 'id' => 'customFile', 'accept' => '.jpg, .jpeg, .png']) !!}
                        {!! Form::label('image', __('Choose new img'), ['class' => 'custom-file-label', 'for' => 'customFile']) !!}
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-3 mt-3">
                    @foreach ($errors->all() as $error)
                        <div class="text-danger">{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <input type="hidden" name="id" value="{{ $item->id }}">

            <input type="submit" class="btn btn-secondary" value="{{ __('Save') }}">
        </form>
    </div>
@endcomponent
