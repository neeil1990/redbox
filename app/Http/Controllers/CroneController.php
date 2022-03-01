<?php

namespace App\Http\Controllers;

use App\DomainMonitoring;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;

class CroneController extends Controller
{
    /**
     * @param $timing
     * @return void
     */
    public function checkLinkCrone($timing)
    {
        Log::debug('start monitoring with timing', [$timing]);
        try {
            $projects = DomainMonitoring::where('timing', '=', $timing)->get();
            foreach ($projects as $project) {
                DomainMonitoring::httpCheck($project);
            }
        } catch (Exception $exception) {
            Log::debug('scan error', [$exception->getMessage()]);
        }
    }
}
