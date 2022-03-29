<?php


namespace App\Classes\Pay\Robokassa;


use App\Classes\Pay\Pay;
use \Illuminate\Support\Collection;

class RobokassaPay extends Pay
{
    private $url;
    private $params;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getParams(): Collection
    {
        return $this->params;
    }

    public function setParams(string $key, $value): void
    {
        $this->params->put($key, $value);
    }

    public function __construct()
    {
        $this->params = collect();

        $config = config('payment.robokassa');

        $this->url = $config['url'];
        $this->login = $config['login'];
        $this->password = $config['password'];
        $this->password2 = $config['password2'];

        $this->setParams('MrchLogin',$this->login);
    }

    public function pays(){
        $this->setParams('SignatureValue', $this->signature());

        return implode('?', [$this->url, $this->httpBuild()]);
    }

    protected function httpBuild()
    {
        return http_build_query($this->getParams()->toArray());
    }

    protected function signature()
    {
        $params = $this->getParams();

        $OutSum = $params->get('OutSum');
        $InvId = $params->get('InvId');

        return md5("$this->login:$OutSum:$InvId:$this->password");
    }
}
