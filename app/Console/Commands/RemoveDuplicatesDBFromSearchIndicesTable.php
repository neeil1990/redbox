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
    protected $signature = 'search-indices:remove-duplicates {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for remove duplicates in search_indices table input: search-indices:remove-duplicates 2022-05-01';

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
        $date = $this->argument('date');

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
            foreach($records as $record){
                if($now == -1 || $record['position'] > $now ){
                    $now = $record['position'];
                    continue;
                }

                $record->delete();
            }
        }
    }
}
