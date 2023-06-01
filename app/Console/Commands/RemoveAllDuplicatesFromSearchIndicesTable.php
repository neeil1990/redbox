<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemoveAllDuplicatesFromSearchIndicesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search-indices:remove-all-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for remove all duplicates in search_indices table';

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
        $dates = $model->select(\DB::raw('DATE(created_at) as date'))->groupBy('date')->get();

        $bar = $this->output->createProgressBar($dates->count());
        foreach ($dates->pluck('date') as $date){

            \Artisan::queue('search-indices:remove-duplicates', [
                'date' => $date,
            ]);

            $bar->advance();
        }

        $bar->finish();
    }
}
