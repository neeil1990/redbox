<?php


namespace App\Classes\Monitoring\Queues;

use App\Classes\Monitoring\PositionLimit;
use App\Jobs\AutoUpdatePositionQueue;
use App\Jobs\OccurrenceQueue;
use App\User;
class OccurrenceDispatch extends QueueDispatcher
{
    public function __construct(int $user, string $queue)
    {
        $this->user = User::find($user);
        $this->queue = $queue;
    }

    public function dispatch()
    {
        $queries = $this->getData();
        $this->countOff = count($queries);

        //Проверка лимитов
        $limit = new PositionLimit($this->user['id']);
        if($this->status = $limit->check($this->countOff)){
            $this->msg = __('Job added to queue');
            //Отправка очереди
            foreach ($queries as $ar)
                dispatch((new OccurrenceQueue($ar['query'], $ar['region']))->onQueue($this->queue));
        }else
            $this->error = __('Limit exhausted');
    }
}
