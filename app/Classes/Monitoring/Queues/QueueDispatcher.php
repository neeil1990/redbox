<?php


namespace App\Classes\Monitoring\Queues;


abstract class QueueDispatcher
{
    protected $user;
    protected $queue;
    protected $dataDispatch = [];
    protected $countOff = 0;
    protected $status = false;
    protected $msg = '';
    protected $error = '';

    public function addQueryWithRegion($query, $region)
    {
        $this->dataDispatch[] = [
            'query' => $query,
            'region' => $region,
        ];
    }

    public function getData()
    {
        return $this->dataDispatch;
    }

    public function notify($msg = "")
    {
        return response()->json([
            'status' => $this->status,
            'count' => $this->countOff,
            'msg' => $msg ?: $this->msg,
            'error' => $this->error,
        ]);
    }

    abstract public function dispatch();
}
