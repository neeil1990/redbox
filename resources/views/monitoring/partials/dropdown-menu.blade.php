<div class="btn-group">
    <button type="button" data-toggle="dropdown" data-offset="-170" class="btn btn-info dropdown-toggle" aria-expanded="false"><i class="fas fa-bars"></i></button>

    <div class="dropdown-menu" style="">
        <a class="dropdown-item" href="{{ route('monitoring.show', $project->id) }}">
            <i class="far fa-folder-open mr-2"></i>Открыть проект
        </a>

        <div class="dropdown-divider"></div>

        @can('add_user_to_project_monitoring')
            <a class="dropdown-item add-user" data-id="{{ $project->id }}"><i class="far fa-user mr-2"></i>Добавить пользователя</a>
        @endcan

        @can('export_report_monitoring')
            <a class="dropdown-item click_tracking" data-click="Export project" data-toggle="modal" data-target=".modal" data-type="export-edit" data-id="{{ $project->id }}">
                <i class="fas fa-file-download mr-2"></i>Экспорт отчета
            </a>
        @endcan

        @can('create_query_monitoring')
            <a class="dropdown-item" data-toggle="modal" data-target=".modal" data-type="create_keywords" data-id="{{ $project->id }}">
                <i class="far fa-plus-square mr-2"></i>Добавить запрос
            </a>
        @endcan

        @can('edit_project_monitoring')
            <a class="dropdown-item" href="{{ route('monitoring.create') }}#id={{ $project->id }}"><i class="fas fa-edit mr-2"></i>Изменить проект</a>
        @endcan

        <a class="dropdown-item" href="{{ route('groups.index', $project->id) }}" title=""><i class="far fa-folder mr-2"></i>Группы проекта</a>

        @can('leave_project_monitoring')
            <div class="dropdown-divider"></div>

            <a class="dropdown-item detach-user" href="javascript:void(0)" data-id="{{ auth()->id() }}" data-project="{{ $project->id }}"><i class="fas fa-door-open mr-2"></i>Покинуть проект</a>
        @endcan

    </div>
</div>
