<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SearchIndicesCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search-indices:count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show count records from search_indices table';

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
        $model = new \App\SearchIndex();
        $cnt = $model->select(\DB::raw('COUNT(1) AS cnt'))->first()->cnt;

        $this->info($cnt);
    }
}
