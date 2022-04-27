
<div class="col-md-12">

    <table class="table table-bordered">
        <thead>
            <tr>
                <th style="width: 10px">#</th>
                <th>{{ __('Project name') }}</th>
                <th>{{ __('Project site') }}</th>
                <th>{{ __('Search system') }}</th>
                <th>{{ __('Words') }}</th>
                <th>{{ __('Top') }}</th>
                <th>{{ __('Position') }}</th>
                <th>{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $project)
            <tr>
                <td><input type="checkbox" class="checkbox-projects" value="{{ $project->id }}"></td>
                <td>
                    <a href="{{ route('monitoring.show', $project->id) }}">{{ $project->name }}</a>
                </td>
                <td>{{ $project->url }}</td>
                <td>Clean database</td>
                <td>Clean database</td>
                <td>Clean database</td>
                <td>Clean database</td>
                <td>Clean database</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
