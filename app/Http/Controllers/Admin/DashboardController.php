<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Card;
use App\Models\FinalGrading;
use App\Models\ManualLabel;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DashboardController extends Controller
{

    public function dashboard(Request $request, $finalGrade=null)
    {
        $data['title'] = __('messages.common.dashboard');

        $query = User::query();
        $orderQuery = Order::query();
        $transactionQuery = Transaction::query();
    
        if ($request->has('date_range')) {
            $dateRange = explode(' to ', $request->date_range);
        
            if (count($dateRange) == 2) {
                $startDate = Carbon::createFromFormat('d-m-Y', trim($dateRange[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('d-m-Y', trim($dateRange[1]))->endOfDay();
        
                $query->whereBetween('created_at', [$startDate, $endDate]);
                $orderQuery->whereBetween('created_at', [$startDate, $endDate]);
                $transactionQuery->whereBetween('created_at', [$startDate, $endDate]);
            } elseif (count($dateRange) == 1) {
                $startDate = Carbon::createFromFormat('d-m-Y', trim($dateRange[0]))->startOfDay();
                $query->where('created_at', '>=', $startDate);
                $orderQuery->where('created_at', '>=', $startDate);
                $transactionQuery->where('created_at', '>=', $startDate);
            }
        }
    
        $data['users_count'] = $query->count();
        $data['plan'] = Plan::count();
        $data['unpaid_orders'] = (clone $orderQuery)->whereNot('item_type', 0)
            ->whereNot(function ($query) {
                $query->whereIn('status', [35, 40])
                    ->where('payment_status', 1);
            })->count();
        $data['paid_orders'] = (clone $orderQuery)->where('payment_status', 1)
            ->where('status', 35)
            ->count();;
        $data['total_orders'] = $data['unpaid_orders'] + $data['paid_orders'];
        $data['totalTransaction'] = $transactionQuery->where('status', 1)->sum('amount');

        $data['order'] = $orderQuery->latest()->take(10)->with('details.cards')->get();
        // $data['finalGradings'] = FinalGrading::get(['name','finalgrade'])->pluck('name','finalgrade');
        $data['finalGradings'] = FinalGrading::get(['name', 'finalgrade'])->groupBy('finalgrade')->map(function ($items) {
            return $items->pluck('name')->toArray();
        });
        $data['total_graded_cards'] = Order::where('payment_status', 1)
        ->where(function ($query) {
            $query->where('status', 35)
                  ->orWhere('status', 40);
        })
        ->with('cards')
        ->get()
        ->sum(function ($order) {
            return $order->cards->count();
        });
        $data['total_graded_cards'] = $data['total_graded_cards'] + ManualLabel::count();
        return view('admin.dashboard',compact('data'));
    }

    public function gradingCalculation(Request $request)
    {
           // Retrieve input values from the form
           $centering = (float) $request['centering'];
           $corners = (float) $request['corners'];
           $edges = (float) $request['edges'];
           $surface = (float) $request['surface'];

           // Calculate the final grade
           $finalGrade = calculateFinalGrade([$centering, $corners, $edges, $surface]);

           return redirect()->route('admin.dashboard',['finalGrade'=>$finalGrade,'centering'=>$centering,'corners'=>$corners,'edges'=>$edges,'surface'=>$surface, 'scrollToBottom' => 'true']);   
    }

    public function cacheClear(){
        toastr()->success('Cache Cleared');
        Artisan::call('optimize:clear');
        return back();
    }

    public function adminProfile()
    {
        $roles = Role::latest()->get();
         return view('admin.profile.index', compact('roles'));
    }
    public function profileEdit()
    {
         return view('admin.profile.edit');
    }

    public function profileUpdate(Request $request)
    {


        $user_id = Auth::user()->id;
        $user = Admin::where('id', $user_id)->first();
        $this->validate($request, [
            'name'  => 'required',
            'email'   => 'required|unique:admins,email,' . $user->id . ',id',
        ]);

        try {

            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->hasFile('image')) {

                // Delete the existing image file if it exists
                if (File::exists(public_path($user->image))) {
                    File::delete(public_path($user->image));
                }

                // Upload and save the new image
                $image = $request->file('image');
                $base_name  = preg_replace('/\..+$/', '', $image->getClientOriginalName());
                $base_name  = explode(' ', $base_name);
                $base_name  = implode('-', $base_name);
                $base_name  = Str::lower($base_name);
                $image_name = $base_name . "-" . uniqid() . "." . $image->getClientOriginalExtension();
                $extension  = $image->getClientOriginalExtension();
                $file_path  = 'uploads/admin';
                $image->move(public_path($file_path), $image_name);
                $user->image  = $file_path . '/' . $image_name;

            }

            $user->save();
        } catch (\Exception $e) {
            Toastr::error(trans('An unexpected error occured while updating profile information'), trans('Error'), ["positionClass" => "toast-top-right"]);
            return redirect()->back();
        }

        Toastr::success(trans('Profile information updated successfully'), trans('Success'), ["positionClass" => "toast-top-right"]);
        return redirect()->route('admin.profile');
    }
    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [

            'password'          => 'required|min:6',
            'confirm_password'  => 'required|same:password',
        ]);

        try {
            $user  = Admin::find(Auth::user()->id);
            $user->password = Hash::make($request->input('password'));
            $user->update();

        } catch (\Exception $e) {
            Toastr::error(trans('An unexpected error occured while updating password'), trans('Error'), ["positionClass" => "toast-top-right"]);
            return redirect()->back();
        }
        Toastr::success(trans('Password updated successfully'), trans('Success'), ["positionClass" => "toast-top-right"]);
        return redirect()->back();
    }

    public function qrGenerate(Request $request)
    {
        $request->validate([
            'qr_link' => 'required|url',
            'site_logo' => 'required|boolean',
        ]);

        $qrLink = $request->qr_link;
        $siteLogo = $request->site_logo;
        $setting = getSetting();

        $qr = QrCode::format('png')
            ->size(250)
            ->style('round')
            ->color(255, 255, 255)
            ->backgroundColor(0, 0, 0);

        if ($siteLogo == 1) {
            $qr->merge(public_path($setting->site_logo), 2, true);
        }

        $qrImage = $qr->generate($qrLink);
        return response($qrImage)->header('Content-Type', 'image/png');
    }
}
