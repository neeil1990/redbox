<?php


namespace App\Classes\Curl;

use Ixudra\Curl\Facades\Curl;
class CurlFacade
{
    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var
     */
    private $url;

    /**
     * @var array
     */
    private $data = [];

    /**
     * CurlFacade constructor.
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Init Curl
     */
    private function init()
    {
        $this->curl = Curl::to($this->url);
    }

    /**
     * @return array
     */
    public function run()
    {
        return $this->response()->get();
    }

    /**
     * @return $this|CurlFacade
     */
    private function response()
    {
        $this->init();

        $response = $this->curl->withResponseHeaders()->returnResponseArray()->get();

        if(!$response['status'])
            return $this;

        if($response['status'] == 301 || $response['status'] == 302){
            $this->data[] = $response;
            $this->url = $response['headers']['Location'];
            return $this->response();
        }
        $this->data[] = $response;

        return $this;
    }

    /**
     * @return array
     */
    private function get()
    {
        return $this->data;
    }


}
