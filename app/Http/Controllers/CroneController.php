<?php

namespace App\Http\Controllers;

use App\DomainMonitoring;
use Carbon\Carbon;
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
        try {
            if (!file_exists($timing . '.txt')) {
                file_put_contents($timing . '.txt', '', 8);
                $projects = DomainMonitoring::where('timing', '=', $timing)->get();
                foreach ($projects as $project) {
                    DomainMonitoring::httpCheck($project);
                }
                unlink($timing . '.txt');
            }
        } catch (Exception $exception) {
            Log::debug('scan error', [$exception->getMessage()]);
            unlink($timing . '.txt');
        }
    }
}
