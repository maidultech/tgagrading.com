<?php


namespace App\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Transaction;
use PDF;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected $transaction;
    public $user;

    public function __construct(Transaction $transaction)
    {
        $this->transaction     = $transaction;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public  function index(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.transaction.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }


        $data['title'] = 'Manage Transaction';
        $data['coupon_flag'] = false;
        $data['used_coupon_count'] = 0;

        $query = Transaction::with('plan', 'user')->where('status', 1);

        if ($request->has('coupon_id')) {
            $data['coupon_flag'] = true;
            $data['used_coupon_count'] = Coupon::where('id', $request->coupon_id)->first()->total_uses;
            $orderIds = Order::where('coupon_id', $request->coupon_id)->pluck('id');
            $query->whereIn('order_id', $orderIds);
        }

        $data['transactions'] = $query->orderBy('id', 'desc')->get();
        
        return view('admin.transaction.index', $data);
    }

    public function invoiceDownload($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.transaction.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $row = Transaction::find($id);
        // $pdf = PDF::loadView('user.invoice', $row);\
        // view('user.invoice_pdf');
        $invoice_dl = true;
        return Pdf::loadView('common.invoice_pdf',(['trnx' => $row]))->download($row->transaction_number.'.pdf');
    }
}
