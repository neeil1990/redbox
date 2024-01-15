<?php

use Illuminate\Database\Seeder;

class MonitoringUserStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\MonitoringUserStatus::firstOrCreate(['code' => 'EMPTY', 'name' => 'Without status']);
        \App\MonitoringUserStatus::firstOrCreate(['code' => 'OWNER', 'name' => 'Owner']);
        \App\MonitoringUserStatus::firstOrCreate(['code' => 'TL', 'name' => 'Team lead']);
        \App\MonitoringUserStatus::firstOrCreate(['code' => 'PM', 'name' => 'Project manager']);
        \App\MonitoringUserStatus::firstOrCreate(['code' => 'SEO', 'name' => 'SEO']);
    }
}
