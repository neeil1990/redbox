<?php

namespace App\Jobs\Monitoring;

use App\MonitoringHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class MonitoringHelperQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $hash;
    protected string $date;
    protected int $lr;
    protected array $keywords;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($date, $lr, $keywords,$hash)
    {
        $this->date = $date;
        $this->lr = $lr;
        $this->keywords = $keywords;
        $this->hash = $hash;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $records = DB::table(DB::raw('search_indices use index(search_indices_query_index, search_indices_lr_index, search_indices_position_index)'))
            ->whereDate('search_indices.created_at', $this->date)
            ->where('search_indices.lr', $this->lr)
            ->whereIn('search_indices.query', $this->keywords)
            ->orderBy('search_indices.id', 'desc')
            ->limit(count($this->keywords) * 100)
            ->select(DB::raw('search_indices.url, search_indices.position, search_indices.query, search_indices.created_at'))
            ->get();


        $results = [];
        if (count($records) > 0) {
            foreach ($records as $record) {
                $results[$this->date][$record->query][$this->lr][] = $record;
            }
        }

        MonitoringHelper::create([
            'result' => json_encode($results),
            'hash' => $this->hash,
        ]);
    }
}
