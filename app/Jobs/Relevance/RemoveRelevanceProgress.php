<?php

namespace App\Jobs\Relevance;

use App\RelevanceProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveRelevanceProgress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $hash;

    public function __construct($hash)
    {
        $this->hash = $hash;
    }

    public function handle()
    {
        RelevanceProgress::where('hash', '=', $this->hash)->delete();
    }
}
