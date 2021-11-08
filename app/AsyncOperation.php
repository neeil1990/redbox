<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AsyncOperation extends \Thread
{

    private $arg;

    public function __construct($arg)
    {
        $this->arg = $arg;
    }

    public function run()
    {
        if ($this->arg) {
            $sleep = mt_rand(1, 10);
            Log::debug("$this->arg start time", [Carbon::now()]);
            printf('%s: %s  -start -sleeps %d' . "\n", date("g:i:sa"), $this->arg, $sleep);
            sleep($sleep);
            printf('%s: %s  -finish' . "\n", date("g:i:sa"), $this->arg);
            Log::debug("$this->arg end time", [Carbon::now()]);

        }
    }
}
