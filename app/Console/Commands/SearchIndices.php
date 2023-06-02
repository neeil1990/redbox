<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SearchIndices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search-indices:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
