<?php

namespace App\Http\Controllers;

use App\Exports\Monitoring\TestExport;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class MonitoringExportsController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            return $next($request);
        });
    }

    public function index()
    {
        $id = 48;
        /** @var User $user */
        $user = $this->user;
        $project = $user->monitoringProjects()->find($id);

        return Excel::download(new TestExport, 'test.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }
}
