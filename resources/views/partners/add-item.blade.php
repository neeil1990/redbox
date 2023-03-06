@component('component.card', ['title' => __('Add partner')])
    <div class="card-body">
        <div class="mb-3">
            <a href="{{ route('partners.add.group') }}" class="btn btn-outline-secondary">{{ __('Add group') }}</a>
            <a href="{{ route('partners.add.item') }}" class="btn btn-outline-secondary">{{ __('Add partner') }}</a>
            <a href="{{ route('partners.admin') }}"
               class="btn btn-outline-secondary">{{ __('Partners (admins)') }}</a>
            <a href="{{ route('partners') }}" class="btn btn-outline-secondary">{{ __('Partners (users)') }}</a>
        </div>
        <form action="{{ route('partners.save.item') }}" method="POST" class="w-50"
              enctype="multipart/form-data">
            @csrf

            <div class="form-group required">
                <label>{{ __('Group Name') }}</label>
                <select name="partners_groups_id" id="partners_groups_id" class="custom-select">
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group required">
                <div>
                    <label for="auditorium_ru">Ru</label>
                    <input type="checkbox" name="auditorium_ru" id="auditorium_ru">
                </div>

                <div id="ru" style="display: none">
                    <div class="form-group required">
                        <label>{{ __('Partner name') }} (ru)</label>
                        <input type="text" name="name_ru" class="form form-control ru-input">
                    </div>

                    <div class="form-group required">
                        <label>{{ __('Link') }} (ru)</label>
                        <input type="text" name="link_ru" class="form form-control ru-input">
                    </div>

                    <div class="form-group required">
                        <label>{{ __('Partner description') }} (ru)</label>
                        <textarea name="description_ru" cols="8" rows="8"
                                  class="form form-control ru-input"></textarea>
                    </div>
                </div>

                <div>
                    <label for="auditorium_en">Eng</label>
                    <input type="checkbox" name="auditorium_en" id="auditorium_en">
                </div>

                <div id="en" style="display: none">
                    <div class="form-group required">
                        <label>{{ __('Partner name') }} (en)</label>
                        <input type="text" name="name_en" class="form form-control en-input">
                    </div>

                    <div class="form-group required">
                        <label>{{ __('Link') }} (en)</label>
                        <input type="text" name="link_en" class="form form-control en-input">
                    </div>

                    <div class="form-group required">
                        <label>{{ __('Partner description') }} (en)</label>
                        <textarea name="description_en" cols="8" rows="8" class="form form-control en-input"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-group required">
                <label>{{ __('Position') }}</label>
                <input type="number" name="position" class="form form-control" required>
            </div>


            <div class="form-group">
                {!! Form::label('image', __('Image')) !!}
                <div class="input-group">
                    <div class="custom-file">
                        {!! Form::file('image', ['class' => 'custom-file-input', 'accept' => '.jpg, .jpeg, .png']) !!}
                        {!! Form::label('image', __('Choose img'), ['class' => 'custom-file-label', 'for' => 'customFile']) !!}
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

            <input type="submit" class="btn btn-secondary" value="{{ __('Add') }}">
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
