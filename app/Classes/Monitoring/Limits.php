<?php


namespace App\Classes\Monitoring;


use App\MonitoringLimit;

abstract class Limits
{
    protected $dateFormat = "Y-m";
    protected $user;
    protected $limit;
    protected $date;

    public function check(int $cnt)
    {
        $monthLimit = $this->limit;
        if(!$monthLimit)
            return true;

        $limitOff = $this->getCounter() + $cnt;
        if($monthLimit >= $limitOff){
            $this->increment($cnt);
            return true;
        }

        return false;
    }

    public function increment($cnt = 1)
    {
        $limit = MonitoringLimit::firstOrCreate(
            ['user_id' => $this->user['id'], 'date' => $this->date],
            ['counter' => 0]
        );

        $limit->increment('counter', $cnt);
    }

    public function getCounter()
    {
        $record = MonitoringLimit::where(['user_id' => $this->user['id'], 'date' => $this->date])->first();
        if(!$record)
            return 0;

        return $record['counter'];
    }
}
