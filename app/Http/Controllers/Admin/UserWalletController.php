<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BalanceMail;
use App\Models\User;
use App\Models\UserWallet;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserWalletController extends Controller
{
    public function index()
    {
        if (!auth('admin')->user()->can('admin.wallet.view')) {
            abort(403, 'Unauthorized action.');
        }

        $customers = User::active()->withCount('wallets')->get();

        // $data['rows'] = UserWallet::
        // when(request('customer_id'), function ($query) {
        //     $query->where('customer_id', request('customer_id'));
        // })->when(request('type'), function ($query) {   
        //     $query->where('amount', request('type') == 'credit' ? '>' : '<', 0);
        // })
        // ->latest()
        // ->
        // with(['customer', 'createdBy','updatedBy']) 
        //     ->orderBy('created_at', 'desc') 
        //     ->paginate(10); 

        
        return view('admin.wallets.index', compact('customers'));
    }
    public function create()
    {
        
        if (!Auth::guard('admin')->user()->can('admin.wallet.create')) {
            abort(403, 'Unauthorized action.');
        }
        // $data['rows'] = UserWallet::
        // when(request('customer_id'), function ($query) {
        //     $query->where('customer_id', request('customer_id'));
        // })->when(request('type'), function ($query) {   
        //     $query->where('amount', request('type') == 'credit' ? '>' : '<', 0);
        // })
        // ->latest()
        // ->
        // with(['customer', 'createdBy','updatedBy']) 
        //     ->orderBy('created_at', 'desc') 
        //     ->paginate(10); 
        return view('admin.wallets.create', compact('customers'));
    }

    public function store(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('admin.wallet.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:255',
        ]);

        $adminId = Auth::guard('admin')->user()->id;

        try {
            $walletTransaction = new UserWallet();
            $walletTransaction->customer_id = $request->customer_id;
            $walletTransaction->amount = ($request->amount);
            $walletTransaction->description = $request->description;
            $walletTransaction->created_by = $adminId;
            $walletTransaction->save();
            syncWallet($walletTransaction->customer_id);

            $user = User::findOrFail($request->customer_id);
            $setting = getSetting();
            $data = [
                'greeting' => 'Dear '.$user->name.' '.$user->last_name,
                'thanks' => 'Thank you for being a valued customer!',
                'body' => 'We have credited your account with $'.$request->amount,
                'reason' => $request->description,
                'info' => 'This amount can be used towards your next payment. ',
                'site_name' => $setting->site_name ?? config('app.name'),
                'site_url' => url('/'),
                'footer' => 1,
            ];
            try {
                Mail::to($user->email)->send(new BalanceMail($data));
            } catch (\Exception $e) {
                Log::alert('Mail not sent. Error: ' . $e->getMessage());
            }
            Toastr::success('Wallet transaction created successfully.');
        } catch (\Throwable $th) {
            //throw $th;
            Toastr::error('Failed to create wallet transaction.');
        }

        return to_route('admin.wallet.index');
    }
    public function edit($id)
    {
        if (!Auth::guard('admin')->user()->can('admin.wallet.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $transaction = UserWallet::findOrFail($id);

        $customers = User::active()->get();

        return view('admin.wallets.edit', compact('transaction', 'customers'));
    }

    public function details($id)
    {
        if (!Auth::guard('admin')->user()->can('admin.wallet.view')) {
            abort(403, 'Unauthorized action.');
        }
        $data['customer'] = User::findOrFail($id);
        $data['rows'] = UserWallet::
        where('customer_id', $id)
        ->latest()
        ->
        with(['customer', 'createdBy','updatedBy']) 
            ->orderBy('created_at', 'desc') 
            ->paginate(10); 


        return view('admin.wallets.details', $data);
    }

    public function update(Request $request, $id)
    {
        if (!Auth::guard('admin')->user()->can('admin.wallet.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            // 'customer_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:255',
        ]);

    

        try {

            // $transaction = UserWallet::findOrFail($id);
            // $adminId = Auth::guard('admin')->user()->id;
            // $transaction->customer_id = $request->customer_id;
            // $transaction->amount = ($request->amount);
            // $transaction->description = $request->description;
            // $transaction->updated_by = $adminId;
            // $transaction->save();

            $adminId = Auth::guard('admin')->user()->id;

            $user = User::findOrFail($id);

            // Check if the amount is negative
            if ($request->amount < 0) {
                $deductibleAmount = -$user->wallet_balance;
                
                if ($deductibleAmount > $request->amount) {
                    Toastr::warning('Cannot deduct more than the wallet balance.');
                    return redirect()->back();
                }
            }
            
            $walletTransaction = new UserWallet();
            $walletTransaction->customer_id = $user->id;
            $walletTransaction->amount = $request->amount;
            $walletTransaction->description = $request->description;
            $walletTransaction->created_by = $adminId;
            $walletTransaction->save();

            syncWallet($walletTransaction->customer_id);

            $setting = getSetting();
            $data = [
                'greeting' => 'Dear '.$user->name.' '.$user->last_name,
                'thanks' => 'Thank you for being a valued customer!',
                'body' => 'We have credited your account with $'.$request->amount,
                'reason' => $request->description,
                'info' => 'This amount can be used towards your next payment. ',
                'site_name' => $setting->site_name ?? config('app.name'),
                'site_url' => url('/'),
                'footer' => 1,
            ];
            try {
                Mail::to($user->email)->send(new BalanceMail($data));
            } catch (\Exception $e) {
                Log::alert('Mail not sent. Error: ' . $e->getMessage());
            }
            
            Toastr::success('User balance updated successfully.');
        } catch (\Throwable $th) {
            throw $th;
            Toastr::error('Failed to update user balance.');
        }

        return to_route('admin.wallet.index');
    }

    public function balance($id)
    {
        
        if (!Auth::guard('admin')->user()->can('admin.wallet.edit')) {
            abort(403, 'Unauthorized action.');
        }
        $customer = User::findOrFail($id);
        return view('admin.wallets.balance', compact('customer'));
    }
}
