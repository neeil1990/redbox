<?php

use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsMonitoringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::firstOrCreate(['name' => 'admin_monitoring', 'title' => 'Admin']);

        Role::firstOrCreate(['name' => 'viewer_monitoring', 'title' => 'Просмотр']);
        Role::firstOrCreate(['name' => 'team_lead_monitoring', 'title' => 'Team lead']);
        Role::firstOrCreate(['name' => 'project_manager_monitoring', 'title' => 'Project manager']);
        Role::firstOrCreate(['name' => 'seo_monitoring', 'title' => 'SEO-специалист']);

        $p1 = Permission::firstOrCreate(['name' => 'create_groups_monitoring', 'title' => 'Создать новую группу']);
        $p2 = Permission::firstOrCreate(['name' => 'edit_groups_monitoring', 'title' => 'Редактировать группу проекта']);
        $p3 = Permission::firstOrCreate(['name' => 'delete_groups_monitoring', 'title' => 'Удалить группу']);

        $p4 = Permission::firstOrCreate(['name' => 'update_occurrence_monitoring', 'title' => 'Обновить частотность проекта']);

        $p5 = Permission::firstOrCreate(['name' => 'update_price_monitoring', 'title' => 'Редактировать цену запроса']);
        $p6 = Permission::firstOrCreate(['name' => 'update_budget_monitoring', 'title' => 'Редактировать бюджет проекта']);

        $p7 = Permission::firstOrCreate(['name' => 'create_query_monitoring', 'title' => 'Добавить запрос']);
        $p8 = Permission::firstOrCreate(['name' => 'edit_query_monitoring', 'title' => 'Редактировать запрос']);
        $p9 = Permission::firstOrCreate(['name' => 'delete_query_monitoring', 'title' => 'Удалить запрос']);

        $p10 = Permission::firstOrCreate(['name' => 'form_keyword_monitoring', 'title' => 'Поле запрос']);
        $p11 = Permission::firstOrCreate(['name' => 'form_relative_url_monitoring', 'title' => 'Поле релевантный URL']);
        $p12 = Permission::firstOrCreate(['name' => 'form_target_monitoring', 'title' => 'Поле цель в топе']);
        $p13 = Permission::firstOrCreate(['name' => 'form_group_monitoring', 'title' => 'Поле группы']);

        $p14 = Permission::firstOrCreate(['name' => 'update_position_monitoring', 'title' => 'Добавить в очередь выбранные']);
        $p15 = Permission::firstOrCreate(['name' => 'update_position_all_monitoring', 'title' => 'Добавить в очередь все']);

        $p16 = Permission::firstOrCreate(['name' => 'add_user_to_project_monitoring', 'title' => 'Добавить пользователя']);
        $p17 = Permission::firstOrCreate(['name' => 'export_report_monitoring', 'title' => 'Экспорт отчета']);
        $p18 = Permission::firstOrCreate(['name' => 'edit_project_monitoring', 'title' => 'Изменить проект']);
        $p19 = Permission::firstOrCreate(['name' => 'delete_user_from_project_monitoring', 'title' => 'Удалить пользователя']);
        $p20 = Permission::firstOrCreate(['name' => 'change_user_status_project_monitoring', 'title' => 'Изменить статус пользователя']);

        Permission::firstOrCreate(['name' => 'leave_project_monitoring', 'title' => 'Покинуть проект']);

        $admin->syncPermissions([$p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9, $p10, $p11, $p12, $p13, $p14, $p15, $p16, $p17, $p18, $p19, $p20]);
    }
}
