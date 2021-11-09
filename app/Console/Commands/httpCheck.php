<?php

namespace App\Console\Commands;

use App\DomainMonitoring;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\VarDumper\VarDumper;

class httpCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'httpCheck {timing} {iterator}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Domain health check using curl';

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
        $iterator = (integer)$this->argument('iterator');
        $timing = (integer)$this->argument('timing');
        Log::debug("process $iterator start", []);

        $projects = DomainMonitoring::where('timing', '=', $timing)->get();
        $projects = $projects->chunk(count($projects) / 5);

        foreach ($projects[$iterator] as $project) {
            try {
                $oldState = $project->broken;
                $curl = DomainMonitoring::curlInit($project->link);
                if (isset($curl) && $curl[1]['http_code'] === 200) {
                    if (isset($project->phrase)) {
                        DomainMonitoring::searchPhrase($curl, $project->phrase, $project);
                    } else {
                        $project->status = 'Everything all right';
                        $project->broken = false;
                    }
                    $project->code = 200;
                } else {
                    $project->status = 'unexpected response code';
                    $project->code = $curl[1]['http_code'];
                    $project->broken = true;
                }
            } catch (\Exception $e) {
                $project->status = 'the domain did not respond within 6 seconds';
                $project->code = 0;
                $project->broken = true;
            }
            DomainMonitoring::calculateTotalTimeLastBreakdown($project, $oldState);
            DomainMonitoring::calculateUpTime($project);
            DomainMonitoring::sendNotifications($project, $oldState);
            $project->last_check = Carbon::now();
            $project->save();
        }
        Log::debug("process $iterator end", []);
    }
}
