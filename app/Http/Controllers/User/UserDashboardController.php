<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\CanadaPostController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UPSController;
use App\Models\Address;
use App\Models\Card;
use App\Models\FinalGrading;
use App\Models\HomepageStep;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Plan;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use App\Service\CanadaPostService;
use Barryvdh\DomPDF\Facade\Pdf;
use Brian2694\Toastr\Facades\Toastr;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use LSS\XML2Array;
use Mitrik\Shipping\ServiceProviders\Exceptions\InvalidCredentials;
use Mitrik\Shipping\ServiceProviders\Exceptions\InvalidShipmentParameters;
use Mitrik\Shipping\ServiceProviders\ServiceCanadaPost\ServiceCanadaPostCredentials;

class UserDashboardController extends Controller
{

    public function index()
    {

        $user = auth()->user();
        $title = __('messages.Welcome_Back') . ', ' . $user->name;
        // $data = Card::where('user_id', $user->id);
        // $data2 = Card::where('user_id', $user->id);
        $totalCards = 0;
        $most_viewd_card = 0;
        // $cards = $data->latest('created_at')->get();
        // $vcardIds = $data->pluck('id')->toArray();
        // $totalConnection = Inquiry::with('vcard')->whereIn('vcard_id', $vcardIds)->whereDate('created_at', Carbon::today())->count();
        $transactionTotal = Transaction::where('user_id', $user->id)->where('status', 1)->sum('amount');
        
        $today = Carbon::today();
        $validDate = Carbon::parse($user->current_pan_valid_date);
        $day = $today->diffInDays($validDate, false);
        $remain_day = max($day, 0);
        $addresses = Address::where('user_id', auth()->id())->get();
 

        return view('user.index', compact('title',  'most_viewd_card', 'totalCards',  'transactionTotal','remain_day', 'user', 'addresses'));
    }

    public function editProfile()
    {
        $title = __('messages.common.profile');
        $user = Auth::user();
        return view('user.profile', compact('user', 'title'));
    }

    public function profileUpdate(Request $request)
    {

        $user_id = Auth::user()->id;
        $user = User::where('id', $user_id)->first();

        $this->validate($request, [
            'first_name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id . ',id',
            'phone' => 'nullable',
        ]);

        try {

            $user->name         = $request->first_name;
            $user->last_name    = $request->last_name;
            // $user->email        = $request->email;
            $user->dial_code    = $request->dial_code;
            $user->phone        = $request->phone;
            $user->address      = $request->address;
            $image              = $request->file('image');
            if (!empty($image)) {
                $user->image = uploadGeneralImage($image, 'UserInfo', $user->image);
            }

            $user->save();
        } catch (\Exception $e) {
            throw $e;
            Toastr::error(__('messages.toastr.user_profile_update_error'), __('messages.common.error'), ["positionClass" => "toast-top-right"]);
            return redirect()->back();
        }

        Toastr::success(__('messages.toastr.user_profile_update_success'), __('messages.common.success'), ["positionClass" => "toast-top-right"]);
        return redirect()->back();
    }

    public function changePassword(Request $request)
    {

        $request->validate([
            'new_password' => ['required',Password::min(8)->mixedCase()->symbols()->numbers()],
            'confirm_password' => 'required|same:new_password',

        ],[
            'password.mixed' => '1 uppercase and 1 lowercase is required',
            'password.symbols' => '1 special character is required',
            'password.numbers' => '1 number is required',
        ]);

        try {

            $user = User::find(Auth::user()->id);
            $user->password = Hash::make($request->input('new_password'));
            $user->update();

        } catch (\Exception $e) {
            Toastr::error(__('messages.toastr.user_password_change_error'), __('messages.common.error'), ["positionClass" => "toast-top-right"]);
            return redirect()->back();
        }
        Toastr::success(__('messages.toastr.user_password_change_success'), __('messages.common.success'), ["positionClass" => "toast-top-right"]);
        return redirect()->back();
    }

    public function orders()
    {
        $title = __('messages.user_dashboard.orders');
        $orders = Order::withSum('details', 'qty')->where('user_id', auth()->user()->id)->latest()->get();
        return view('user.orders', compact('title', 'orders'));
    }

    public function cards($id)
    {
        $title = __('messages.user_dashboard.orders');
        $order = Order::where('id', $id)->first()->load('details.cards');
        // $finalGradings = FinalGrading::get(['name','finalgrade'])->pluck('name','finalgrade');
        $finalGradings = FinalGrading::get(['name', 'finalgrade'])->groupBy('finalgrade')->map(function ($items) {
            return $items->pluck('name')->toArray();
        });
        return view('user.cards', compact('title', 'order', 'finalGradings'));
    }
    

    public function orderTracking($id)
    {
        $title = __('messages.user_dashboard.orders');
        $order = Order::find($id);
        $setting = getSetting();
        $trackingNumber = $order->admin_tracking_id;
        $trackingDetails = [];
        $trackingStatus = 'Pending'; 
        $is_invalid = 0;

        if(!(auth('admin')->check() || auth('user')->id()==$order->user_id)){
            abort(404);
        }        
        if ($order->shipping_method == 'canada_post') 
        {
            $test = '';
            
            $setting = getSetting();
            $order = Order::where('id', $id)->first();

            $customer_number = $setting->canadapost_customer_number;
            $username = $setting->canadapost_username;
            $password = $setting->canadapost_password;
            $mode = $setting->canadapost_mode;

            if($mode == 'live')
            {
                $test = False;
            } else {
                $test = True;
            }
            
            
            try {
                
                $data = app(CanadaPostController::class)->track($order->id);
                $trackingDetails = $data['tracking-detail'];
                // dd($trackingDetails);
                $statusLists = collect($trackingDetails['significant-events']['occurrence'])->pluck('event-description');
                
                // Log::info($statusLists);
                if($statusLists->contains(function($value, $key){
                    return str($value)->contains(['successfully delivered','Delivered']);
                })){
                    $trackingStatus = 'Delivered';
                } else if($statusLists->contains(function($value, $key){
                    return str($value)->contains(['Item out for delivery','being forwarded']);
                })){
                    $trackingStatus = 'In Transit';
                }else if($statusLists->contains(function($value, $key){
                    return str($value)->contains(['received by Canada Post','Item accepted at the']);
                })){
                    $trackingStatus = 'Accepted';
                }
                $is_invalid = 0;

            } catch (ClientException $e) {
                if ($e->getCode() === 401) {
                    Log::error('Canada Post Tracking: Invalid credentials.', [
                        'exception' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
        
                if ($e->getCode() === 400) {
                    $responseBody = $e->getResponse()->getBody()->getContents();
                
                    $array = XML2Array::createArray($responseBody);
        
                    Log::error('Canada Post Tracking: Invalid request parameters.', [
                        'response_body' => $responseBody,
                        'parsed_response' => $array,
                    ]);
                }
                Log::error('Canada Post Tracking: Unhandled exception.', [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $is_invalid = 1;
            } catch (\Exception $e) {
                Log::error('Canada Post Tracking: General error occurred.', [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $is_invalid = 1;

            }

            $view = 'user.tracking.canada-post';
        } elseif($order->shipping_method === 'ups') {
            
            $data = app(UPSController::class)->track($order);
            try {
                $trackingDetails = $data['trackResponse']['shipment'][0]['package'][0] ?? [];
                // dd($trackingDetails);
               
                if (!empty($data['trackResponse']['shipment'][0]['package'])) {
                    $packages = $data['trackResponse']['shipment'][0]['package'];
                    foreach ($packages as $package) {
                        $activities = $package['activity'];

                        foreach ($activities as $activity) {
                            $statusType = strtolower($activity['status']['type'] ?? '');
                            $statusDescription = strtolower($activity['status']['description'] ?? '');
                            $statusCode = $activity['status']['statusCode'] ?? '';
                
                            // Check for "Delivered" status
                            if (strpos($statusDescription, 'delivered') !== false || $statusCode === '001') {
                                $trackingStatus = 'Delivered';
                                break 2; // Exit both loops
                            }
                
                            // Check for "In Transit" status
                            if ($statusType === 'i' || $statusCode === '005' || $statusType === 'x') {
                                $trackingStatus = 'In Transit';
                            }
                
                            // Check for "Accepted" status
                            if ($statusType === 'p' || $statusCode === '038' || $statusType === 'm') {
                                $trackingStatus = 'Accepted';
                            }
                        }
                
                    }

                    $trackingStatus = $trackingStatus ?? 'In Transit';
                }

                $trackingStatus;
                // dd($trackingStatus);
                $is_invalid = 0;

            } catch (\GuzzleHttp\Exception\ClientException $e) {
                if ($e->getCode() === 401) {
                    Log::error('UPS Tracking: Invalid credentials.', [
                        'exception' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

                if ($e->getCode() === 400) {
                    $responseBody = $e->getResponse()->getBody()->getContents();

                    Log::error('UPS Tracking: Invalid request parameters.', [
                        'response_body' => $responseBody,
                    ]);
                }
                Log::error('UPS Tracking: Unhandled exception.', [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                $is_invalid = 1;
            } catch (\Exception $e) {
                Log::error('UPS Tracking: General error occurred.', [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $is_invalid = 1;

            }

            $view = 'user.tracking.ups';
        } else {
            // Local pickup or unknown shipping method
            $trackingDetails = [];
            $trackingStatus = 'Delivered';
            $view = 'user.tracking.localpickup';
        }

        return view($view, compact('title', 'trackingDetails', 'order', 'trackingStatus', 'is_invalid'));
    }

    public function orderTrackingInfoStore(Request $request, Order $order)
    {
        abort_if(!$order,404 );

        $request->validate([
            'tracking_url' => 'required|string',
            'tracking_note' => 'nullable',
        ]);

        try {
            $order->customer_tracking_url = $request->tracking_url;
            $order->customer_tracking_note = $request->tracking_note;

            $order->save();
            toastr()->success('Customer shipping tracking information updated successfully');
        } catch (\Throwable $th) {
            //throw $th;
            toastr()->error('Failed to update customer shipping tracking information');
        }

        return back();

    }

    public function orderInvoice($id)
    {

        $title = __('messages.user_dashboard.order_invoice');
        $order = Order::find($id);
        $trnx = Transaction::where('user_id', Auth::user()->id)->where('order_id', $id)->first();
        abort_if(!$trnx, 404);
        return view('user.order_invoice', compact('title', 'trnx','order'));
    }

    public function orderInvoiceDownload($id)
    {
        $title = __('messages.user_dashboard.order_invoice');
        $order = Order::find($id);
        $trnx = Transaction::where('user_id', Auth::user()->id)->where('order_id', $id)->first();
        abort_if(!$trnx, 404);
        // return view('common.invoice_pdf', compact('title', 'trnx'));

        return Pdf::loadView('common.invoice_pdf',compact('title', 'trnx'))->download('Invoice#'.$trnx->order->order_number.'.pdf');
    }
    public function showInvoice($id)
    {
        $title = __('messages.user_dashboard.order_invoice');
        $trnx = Transaction::where('user_id', Auth::id())->where('order_id', $id)->first();

        // Abort if no transaction is found
        abort_if(!$trnx, 404);

        return view('common.invoice_pdf', compact('title', 'trnx'));
    }
    public function orderPaymentInvoice($id)
    {
        $title = __('messages.user_dashboard.order_invoice');
        $order = Order::find($id);
        $trnx = Transaction::where('user_id', Auth::user()->id)->where('order_id', $id)->first();
        abort_if(!$trnx, 404);
        return view('user.order_payment_invoice', compact('title', 'trnx','order'));
    }
    public function orderPaymentInvoiceDownload($id)
    {
        $title = __('messages.user_dashboard.order_invoice');
        $order = Order::find($id);
        $trnx = Transaction::where('user_id', Auth::user()->id)->where('order_id', $id)->first();
        abort_if(!$trnx, 404);
        // return view('user.invoice_pdf', compact('title', 'trnx'));

        return Pdf::loadView('common.payment_invoice_pdf',compact('title', 'trnx'))->download('Invoice#'.$trnx->order->order_number.'.pdf');
    }
    // public function support()
    // {
    //     $title = 'Support';
    //     $tickets = SupportTicket::where('user_id', Auth::user()->id)->latest('created_at')->get();
    //     return view('user.support', compact('title', 'tickets'));
    // }

    // public function createSupport()
    // {
    //     return view('user.support_create');
    // }
    // public function storeSupport(Request $request)
    // {
    //     $request->validate([
    //         'subject' => 'required',
    //         'message' => 'required',
    //     ]);
    //     $ticket = SupportTicket::max('ticket_number');

    //     $support = new SupportTicket();
    //     $support->user_id = Auth::user()->id;
    //     $support->subject = $request->subject;
    //     $support->ticket_number = max($ticket, 1000) + 1;
    //     $support->priority = $request->priority;
    //     $support->message = $request->message;
    //     $support->status = 0;
    //     $support->save();

    //     Toastr::success('Support Ticket Created Successfully', 'Success');
    //     return redirect()->route('user.support');
    // }



    public function upgradePlan()
    {
        $title = __('messages.user_dashboard.upgrade_plan');
        $userPlan = Auth::user()->current_plan_id;
        $plans = Plan::with('features')->where('is_default', '!=', 1)->where('status', 1)->orderBy('order_number', 'asc')->get();
        // dd($plans->toArray());
        return view('user.plan_upgrade', compact('title', 'plans', 'userPlan'));
    }
}
