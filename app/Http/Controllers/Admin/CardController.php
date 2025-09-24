<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinalGrading;
use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class CardController extends Controller
{
    protected $order;
    public $user;

    public function __construct(Order $order)
    {
        $this->order     = $order;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    // public  function index(Request $request)
    // {
    //     // if (is_null($this->user) || !$this->user->can('admin.card.edit')) {
    //     //     abort(403, 'Sorry !! You are Unauthorized.');
    //     // }

    //     return view('admin.card.index', $data);
    // }

    function index(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.order.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = 'Manage Cards';
        if ($request->has('customer')) {
            $data['order'] = Order::where('user_id', $request->customer)->get()->load('details.cards');
        } elseif($request->has('order')) {
            $data['order'] = Order::where('id', $request->order)->get()->load('details.cards');
        } else {
            $data['order'] = Order::all()->load('details.cards');
        }
        
        // $data['finalGradings'] = FinalGrading::get(['name','finalgrade'])->pluck('name','finalgrade');
        $data['finalGradings'] = FinalGrading::get(['name', 'finalgrade'])->groupBy('finalgrade')->map(function ($items) {
            return $items->pluck('name')->toArray();
        });
        return view('admin.card.index', $data);
    }
}
