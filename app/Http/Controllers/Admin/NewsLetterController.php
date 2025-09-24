<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;


class NewsLetterController extends Controller
{
    protected $subscriber;
    public $user;

    public function __construct(Newsletter $subscriber)
    {
        $this->subscriber     = $subscriber;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index()
    {
        // if (is_null($this->user) || !$this->user->can('admin.subscriber.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = 'Subscribers list';
        $data['rows'] = Newsletter::latest()->get();

        return view('admin.newsletter.index', $data);
    }
}
