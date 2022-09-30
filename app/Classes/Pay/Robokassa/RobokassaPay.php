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

        $this->setParams('MerchantLogin',$this->login);
    }

    public function action(){
        $this->setParams('SignatureValue', $this->signature());

        return implode('?', [$this->url, $this->httpBuild()]);
    }

    public function checkOut(array $params)
    {
        $out_summ = $params['OutSum'];
        $inv_id = $params['InvId'];

        $password = $this->getPassword2();

        $crc = $params['SignatureValue'];
        $my_crc = strtoupper(md5("$out_summ:$inv_id:$password"));

        if ($my_crc != $crc)
            return false;

        return true;
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
