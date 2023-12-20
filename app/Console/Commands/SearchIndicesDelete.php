<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class SearchIndicesDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search-indices:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command delete records from search_indices table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::today();
        $model = new \App\SearchIndex();
        $days = (new \App\MonitoringSettings())->getValue('search_indices_days_delete') ?: 180;

        $cnt = $model->where('created_at', '<', $now->subDays($days))->delete();

        $this->info("Deleted $cnt records!");
    }
}
