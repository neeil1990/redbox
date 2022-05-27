<?php

namespace App;

use App\Jobs\RelevanceAnalysisQueue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Queue extends Model
{

    /**
     * @param $row
     * @param $request
     * @return void
     */
    public static function addInQueue($row, $request)
    {
        try {
            $userId = Auth::id();
            $item = explode(';', $row);
            $link = parse_url($item[0]);

            if (count($item) == 2 && isset($link['host'])) {
                $historyId = Queue::prepareHistory($request, $item[0], $userId, $item[1]);

                RelevanceAnalysisQueue::dispatch(
                    trim($item[0]),
                    trim($item[1]),
                    $request->separator,
                    $request->region,
                    $request->count,
                    $request->ignoredDomains,
                    $userId,
                    $request->all(),
                    $historyId
                );

            }

        } catch (\Exception $e) {
            Log::debug('debug', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }

    }

    /**
     * @param $request
     * @param $link
     * @param $userId
     * @param $phrase
     * @return int
     */
    public static function prepareHistory($request, $link, $userId, $phrase): int
    {
        $time = Carbon::now()->toDateTimeString();

        $mainHistory = DB::transaction(function () use ($request, $time, $link, $userId, $phrase) {
            $host = parse_url($link);
            return ProjectRelevanceHistory::createOrUpdate($host['host'], $time, $userId);
        });


        $default = [
            'mainPoints' => 0,
            'coverage' => 0,
            'coverageTf' => 0,
            'width' => 0,
            'density' => 0,
            'position' => 0,
        ];

        return RelevanceHistory::createOrUpdate(
            $phrase,
            $link,
            $request['region'],
            $default,
            $time,
            $mainHistory,
            0
        );
    }
}
