<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemoveDuplicatesDBFromSearchIndicesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search-indices:remove-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for managed search_indices table in data base';

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
        set_time_limit(0);

        $model = new \App\SearchIndex();

        $dates = $model->select(\DB::raw('DATE(created_at) as date'))->groupBy('date')->get();

        $bar = $this->output->createProgressBar($dates->count());

        foreach($dates->pluck('date') as $date){

            $data = $model->select(\DB::raw('*, count(id) as cnt'))
                ->whereDate('created_at', $date)
                ->groupBy('query', 'lr')
                ->having('cnt', '>', 100)
                ->get();

            foreach($data as $d){
                $records = $model->whereDate('created_at', $d['created_at']->format('Y-m-d'))
                    ->where('query', $d['query'])
                    ->where('lr', $d['lr'])
                    ->orderBy('position', 'asc')
                    ->get();

                $now = -1;
                foreach($records as $rec){
                    if($now == -1 || $rec['position'] > $now ){
                        $now = $rec['position'];
                        continue;
                    }

                    $rec->delete();
                }
            }

            $bar->advance();
        }

        $bar->finish();
    }
}
