<?php

namespace App\Models\Relevance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RelevanceProgress extends Model
{
    protected $table = 'relevance_progress';

    protected $guarded = [];

    public $timestamps = false;

    /**
     * @return void
     */
    public static function startProgress($hash)
    {
        $progress = new RelevanceProgress();
        $progress->user_id = Auth::id();
        $progress->hash = $hash;
        $progress->progress = 0;

        $progress->save();
    }

    /**
     * @param $percent
     * @param $request
     * @return void
     */
    public static function editProgress($percent, $request)
    {
        if (isset($request['hash'])) {
            RelevanceProgress::where('hash', '=', $request['hash'])->update(['progress' => $percent]);
        }
    }

    /**
     * @param $hash
     * @return mixed
     */
    public static function endProgress($hash)
    {
        return RelevanceProgress::where('hash', '=', $hash)->delete();
    }
}
