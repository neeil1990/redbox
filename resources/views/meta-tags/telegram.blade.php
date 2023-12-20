{{ __('Module') }}: <a href="{{ route('meta-tags.index') }}">{{ __('Monitoring meta-tags') }}</a>
{{ __('Project') }}: {{ $model->name }}
{{ __('Changes have occurred on the pages.') }}
{{ __('View changes') }}:
{{ __('Go to') }} - <a href="{{ $link_compare }}">{{ $link_compare }}</a>
