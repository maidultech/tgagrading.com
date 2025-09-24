<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\LogHistory;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;


class LogsHistoryController extends Controller
{
    protected $log;
    public $user;

    public function __construct(LogHistory $log)
    {
        $this->log     = $log;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    // public function index()
    // {
    //     $data['title'] = 'Log History';
    //     $data['rows'] = LogHistory::latest()->get();

    //     return view('admin.logs', $data);
    // }
}
