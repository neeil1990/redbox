<ul class="list-inline user-list">

    @foreach ($project->users as $user)
        <li class="list-inline-item position-relative @can('change_user_status_project_monitoring') change-user-status @endcan"
            user-id="{{ $user->id }}" project-id="{{ $project->id }}"
            data-toggle="tooltip" title="{{ $user->last_name }} {{ $user->name }} - {{ $user->roles()->value('title') }}">

            @if ($user->hasRole('admin_monitoring'))
                <img class="table-avatar img-circle img-bordered-sm admin-monitoring" src="{{ $user->image }}">
            @else
                <img class="table-avatar img-circle img-bordered-sm" src="{{ $user->image }}">
            @endif

            @if(auth()->user()->can('delete_user_from_project_monitoring') && $user->id !== auth()->id())
                <span class="badge badge-secondary navbar-badge detach-user" data-id="{{ $user->id }}" data-project="{{ $project->id }}" style="cursor: pointer; top: -5px; right: 0px; font-size: x-small;">
                    <i class="fas fa-times"></i>
                </span>
            @endif
        </li>
    @endforeach

</ul>
