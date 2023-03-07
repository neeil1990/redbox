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
                    <option value="{{ $item->partner->id }}">
                        {{ $item->partner->name_ru }}/ {{ $item->partner->name_en }}
                    </option>
                    @foreach($groups as $group)
                        @if($item->partner->id === $group->id)
                            @continue
                        @endif
                        <option value="{{ $group->id }}">{{ $group->name_ru }} / {{ $group->name_en }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group required">
                <label>{{ __('Position') }}</label>
                <input type="number" name="position" class="form form-control" value="{{ $item->position }}"
                       required>
            </div>

            <div class="form-group required">
                <div>
                    <label for="auditorium_ru">Ru</label>
                    <input type="checkbox" name="auditorium_ru" id="auditorium_ru"
                           @if($item['auditorium_ru']) checked @endif>
                </div>

                <div id="ru" @if(!$item['auditorium_ru']) style="display: none" @endif>
                    <div class="form-group required">
                        <label>{{ __('Partner name') }} (ru)</label>
                        <input type="text" name="name_ru" class="form form-control ru-input"
                               value="{{ $item['name_ru'] }}">
                    </div>

                    <div class="form-group required">
                        <label>{{ __('Link') }} (ru)</label>
                        <input type="text" name="link_ru" class="form form-control ru-input"
                               value="{{ $item['link_ru'] }}">
                    </div>

                    <div class="form-group required">
                        <label>{{ __('Partner description') }} (ru)</label>
                        <textarea name="description_ru" cols="8" rows="8"
                                  class="form form-control ru-input">{{ $item['description_ru'] }}</textarea>
                    </div>
                </div>

                <div>
                    <label for="auditorium_en">Eng</label>
                    <input type="checkbox" name="auditorium_en" id="auditorium_en"
                           @if($item['auditorium_en']) checked @endif>
                </div>

                <div id="en" @if(!$item['auditorium_en']) style="display: none" @endif>
                    <div class="form-group required">
                        <label>{{ __('Partner name') }} (en)</label>
                        <input type="text" name="name_en" class="form form-control en-input"
                               value="{{ $item['name_en'] }}">
                    </div>

                    <div class="form-group required">
                        <label>{{ __('Link') }} (en)</label>
                        <input type="text" name="link_en" class="form form-control en-input"
                               value="{{ $item['link_en'] }}">
                    </div>

                    <div class="form-group required">
                        <label>{{ __('Partner description') }} (en)</label>
                        <textarea name="description_en" cols="8" rows="8"
                                  class="form form-control en-input">{{ $item['description_en'] }}</textarea>
                    </div>
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
    @slot('js')
        <script>
            $('#auditorium_ru').on('click', function () {
                if ($(this).is(':checked')) {
                    $('#ru').show(300)
                    $('.ru-input').prop('required', true);
                } else {
                    $('#ru').hide(300)
                    $('.ru-input').prop('required', false);
                }
            })

            $('#auditorium_en').on('click', function () {
                if ($(this).is(':checked')) {
                    $('#en').show(300)
                    $('.en-input').prop('required', true);
                } else {
                    $('#en').hide(300)
                    $('.en-input').prop('required', false);
                }
            })
        </script>
    @endslot
@endcomponent
