<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RelevanceProgress extends Model
{
    protected $table = 'relevance_progress';

    protected $guarded = [];

    public $timestamps = false;

    public static function startProgress(): string
    {
        $progress = new RelevanceProgress([
            'user_id' => Auth::id(),
            'hash' => md5(Auth::id() . time()),
            'progress' => 0
        ]);
        $progress->save();

        return $progress->hash;
    }

    public static function editProgress($percent, $request)
    {
        if (isset($request['hash'])) {
            RelevanceProgress::where('hash', $request['hash'])->update(['progress' => $percent]);
        }
    }

    public static function endProgress($hash)
    {
        return RelevanceProgress::where('hash', $hash)->delete();
    }
}
