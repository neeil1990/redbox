<?php

namespace App\Http\Middleware;

use App\HttpHeader;
use Closure;

class CheckHttpHeadersDataBase
{
    /**
     * @var HttpHeader
     */
    protected $httpHeaders;

    /**
     * CheckHttpHeadersDataBase constructor.
     * @param HttpHeader $httpHeaders
     */
    public function __construct(HttpHeader $httpHeaders)
    {
        $this->httpHeaders = $httpHeaders;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    /**
     * Delete database row HttpHeaders where date create more n days.
     */
    public function terminate()
    {
        $this->httpHeaders->deleteData();
    }
}
