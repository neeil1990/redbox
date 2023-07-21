<?php

namespace App;

use App\Jobs\Relevance\RelevanceHistoryQueue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Queue
{

    public static function addInQueue($row, $request)
    {
        try {
            $userId = Auth::id();
            $item = explode(';', $row);
            $link = parse_url(trim($item[1]));

            if (count($item) == 2 && isset($link['host'])) {
                $historyId = Queue::prepareHistory($request->all(), trim($item[1]), $userId, trim($item[0]));

                RelevanceHistoryQueue::dispatch(
                    $userId,
                    $request->all(),
                    $historyId,
                    trim($item[1]),
                    trim($item[0])
                )->onQueue(UsersJobs::getPriority($userId));
            }
        } catch (\Throwable $e) {

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

        $mainHistory = ProjectRelevanceHistory::createOrUpdate(parse_url($link)['host'], $time, $userId);

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
            $request,
            $default,
            $time,
            $mainHistory,
            false,
            0
        );
    }
}
