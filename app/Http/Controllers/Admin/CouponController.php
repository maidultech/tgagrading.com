<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    protected $coupon;
    public $user;

    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }
    public function index(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('admin.coupon.view')) {
            abort(403, 'Sorry !! You are Unauthorized.');
        }

        $data['title'] = 'Coupon list';
        $data['rows'] = Coupon::orderBy('created_at', 'desc')->get();
        return view('admin.coupon.index', compact('data'));
    }

    
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('admin.coupon.create')) {
            abort(403, 'Sorry !! You are Unauthorized.');
        }

        $data['title'] = 'Create Coupon';
        return view('admin.coupon.create', compact('data'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('admin.coupon.create')) {
            abort(403, 'Sorry !! You are Unauthorized.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:coupons,name',
            'discount_type' => 'required|in:Percent,Fixed',
            'discount_value' => 'required|numeric|min:0',
            'discount_code' => 'required|string|unique:coupons,discount_code',
            'expiration_date' => 'required|date',
            'max_redemptions_per_user' => 'required|integer|min:1',
            'max_uses' => 'required|integer|min:1',
            'status' => 'required|in:0,1',
        ]);

        try {
            $coupon = new Coupon();
            $coupon->name = $request->name;
            $coupon->discount_type = $request->discount_type;
            $coupon->discount_value = $request->discount_value;
            $coupon->discount_code = $request->discount_code;
            $coupon->expiration_date = $request->expiration_date;
            $coupon->max_redemptions_per_user = $request->max_redemptions_per_user;
            $coupon->max_uses = $request->max_uses;
            $coupon->status = $request->status;
            $coupon->save();

            Toastr::success('Coupon created successfully!', 'Success', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.coupon.index');
        } catch (\Exception $e) {
            Log::info($e);
            Toastr::error('Failed to create coupon. Please try again.', 'Error', ["positionClass" => "toast-top-center"]);
            return back()->withInput();
        }
    }

    public function edit(Request $request,$id)
    {
        if (is_null($this->user) || !$this->user->can('admin.coupon.create')) {
            abort(403, 'Sorry !! You are Unauthorized.');
        }

        $data['title'] = 'Edit Coupon';
        $data['row'] = Coupon::findOrFail($id);
        return view('admin.coupon.edit', compact('data'));
    }
    
public function update(Request $request, $id)
{
    if (is_null($this->user) || !$this->user->can('admin.coupon.edit')) {
        abort(403, 'Sorry !! You are Unauthorized.');
    }

    $request->validate([
        'name' => 'required|string|max:255|unique:coupons,name,' . $id,
        'discount_type' => 'required|in:Percent,Fixed',
        'discount_value' => 'required|numeric|min:0',
        'discount_code' => 'required|string|unique:coupons,discount_code,' . $id,
        'expiration_date' => 'required|date',
        'max_redemptions_per_user' => 'required|integer|min:1',
        'max_uses' => 'required|integer|min:1',
        'status' => 'required|in:0,1',
    ]);

    try {
        $coupon = Coupon::findOrFail($id);
        $coupon->name = $request->name;
        $coupon->discount_type = $request->discount_type;
        $coupon->discount_value = $request->discount_value;
        $coupon->discount_code = $request->discount_code;
        $coupon->expiration_date = $request->expiration_date;
        $coupon->max_redemptions_per_user = $request->max_redemptions_per_user;
        $coupon->max_uses = $request->max_uses;
        $coupon->status = $request->status;
        $coupon->save();

        Toastr::success('Coupon updated successfully!', 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.coupon.index');
    } catch (\Exception $e) {
        Toastr::error('Failed to update coupon. Please try again.', 'Error', ["positionClass" => "toast-top-center"]);
        return back()->withInput();
    }
}

    public function delete($id)
    {
        if (is_null($this->user) || !$this->user->can('admin.coupon.delete')) {
            abort(403, 'Sorry !! You are Unauthorized.');
        }

        try {
            $coupon = Coupon::findOrFail($id);
        
            // Check if coupon is used in any order
            if (Order::where('coupon_id', $coupon->id)->exists()) {
                Toastr::error('This coupon is used in an order and cannot be deleted.', 'Error', ["positionClass" => "toast-top-center"]);
                return back();
            }
        
            // Safe to delete
            $coupon->delete();
        
            Toastr::success('Coupon deleted successfully!', 'Success', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.coupon.index');
        
        } catch (\Exception $e) {
            Toastr::error('Failed to delete coupon. Please try again.', 'Error', ["positionClass" => "toast-top-center"]);
            return back();
        }
    }

}