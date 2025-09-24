<?php


namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Card;
use App\Models\Order;
use App\Models\OrderCard;
use App\Models\OrderDetail;
use App\Models\Plan;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\UserSubscription;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class CustomerController extends Controller
{
    protected $customer;
    public $user;

    public function __construct(User $customer)
    {
        $this->customer     = $customer;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }


    /**
     * Display a listing of the categories.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.customer.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }
        $openModal = $request->query('openModal');
        $data['title'] = __('Customer');
        $data['rows'] = User::all(); 
    //    $data['rows'] = User::orderBy('created_at', 'desc')->get();

        // dd(($data['rows']) );
        return view('admin.customer.index', compact('data', 'openModal'));
    }

    public function getPlan($id)
    {
        $data['user']= User::find($id);
        $data['plans'] = Plan::get();
        $html = view('admin.customer.plan_form', compact('data'))->render();
        return response()->json($html);
    }
    public function changePlan(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id', $request->user_id)->first();
            $plan = Plan::find($request->plan_id);
            $new_plan_card = $plan->no_of_vcards;
            $userPlan = $user->current_plan_id;
            $userPlanCard = Plan::find($userPlan);

            // $transaction = new Transaction();
            // $transaction->transaction_number = uniqid('trx_');
            // $transaction->user_id = auth()->id();
            // $transaction->plan_id = $plan->id;
            // $transaction->amount = $plan->price;
            // $transaction->currency = 'Euro';
            // $transaction->status = '1';
            // $transaction->pay_date = now();
            // $transaction->transaction_id = $this->generateCustomUniqueId();
            // $transaction->payment_method = 'PayPal';
            // $transaction->save();

            $current_plan_card = $userPlanCard->no_of_vcards;
            if ($current_plan_card > $new_plan_card) {

                // $card_difference = $current_plan_card_no - $new_plan_card;
                $activeCards = Card::where('user_id', $user->id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                // Determine how many cards need to be deactivated
                $activeCardCount = $activeCards->count();
                $cardsToDeactivateCount = $activeCardCount - $new_plan_card;

                if ($cardsToDeactivateCount > 0) {
                    // We need to deactivate the oldest excess cards
                    $cardsToDeactivate = $activeCards->take($cardsToDeactivateCount);

                    foreach ($cardsToDeactivate as $card) {
                        $card->status = 2; // Set status to inactive
                        $card->save();
                    }
                }
            }

            $user->current_plan_id = $plan->id;
            $user->current_plan_name = $plan->name;
            $user->current_pan_valid_date = Carbon::now()->addDay($plan->day);
            $user->update();

        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error(__('messages.toastr.plan_change_error'), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.customer.index');
        }
        DB::commit();
        Toastr::success(__('messages.toastr.plan_change_message'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.customer.index');
    }


    public function create()
    {
        // if (is_null($this->user) || !$this->user->can('admin.customer.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = __('messages.common.user');
        $data['roles'] = Role::orderBy('name', 'asc')->get();
        return view('admin.customer.create', compact('data'));
    }


    public function store(Request $request)
    {
        
        // if (is_null($this->user) || !$this->user->can('admin.customer.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $this->validate($request, [
            'name'          => 'required|max:100',
            'email'         => 'required|unique:users,email',
            'phone'         => 'required|unique:users,phone',
            // 'address'       => 'nullable',
            'image'         => 'nullable',
            'status'        => 'required',
            'password'      => ['required','string','min:8','regex:/[A-Z]/','regex:/[!@#$%^&*(),.?":{}|<>]/',],
        ]);
        // dd($request->all());

        DB::beginTransaction();
        try {
            $max_code = User::max('user_code');
            $user_code = $max_code ? $max_code + 1 : 1001;
            // dd($user_code);
            // $paln = Plan::where('is_default','1')->first();
            // if($paln == ''){
            //     return false;
            // }
            // $day = $paln->day;
            // $currentDate = Carbon::now()->addDays($day);

            $customer = new User();
            $customer->name          = $request->name;
            $customer->last_name     = $request->last_name;
            $customer->email         = $request->email;
            $customer->password      = Hash::make($request->password);
            $customer->phone         = $request->phone;
            $customer->dial_code     = $request->dial_code;
            // $customer->address       = $request->address;
            // $customer->dob           = $request->dob;
            $customer->status        = $request->status;
            $customer->user_code     = $user_code;
            $customer->username      = rand();
            
            // $customer->current_plan_id = $paln->id;
            // $customer->current_plan_name = $paln->name;
            // $customer->current_pan_valid_date = $currentDate->format('Y-m-d');

            if ($request->hasFile('image')) {

                // Upload and save the new image
                $image = $request->file('image');
                $base_name  = preg_replace('/\..+$/', '', $image->getClientOriginalName());
                $base_name  = explode(' ', $base_name);
                $base_name  = implode('-', $base_name);
                $base_name  = Str::lower($base_name);
                $image_name = $base_name . "-" . uniqid() . "." . $image->getClientOriginalExtension();
                $file_path  = 'uploads/UserInfo';
                $image->move(public_path($file_path), $image_name);
                $customer->image  = $file_path . '/' . $image_name;

            }
            $customer->save();

            Address::create([
                'user_id' => $customer->id,
                'first_name' => $request->name,
                'last_name' => $request->last_name,
                'street' => $request->street,
                'apt_unit' => $request->apt_unit,
                'city' => $request->city,
                'zip_code' => $request->zip_code,
                'country' => $request->country,
                'state' => $request->state,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            Toastr::error(__('messages.toastr.create_error'), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.customer.index');
        }
        DB::commit();
        Toastr::success(__('messages.toastr.user_created'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.customer.index');
    }

    public function edit($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.customer.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }
        
        $data['title'] = __('messages.crud.user_edit');
        $data['user'] = User::find($id);
        $data['address'] = Address::where('user_id', $data['user']->id)->orderBy('id', 'desc')->first();
        $data['role'] = Role::orderBy('name', 'asc')->get();
        $data['plans'] = Plan::where('type', 'subscription')->orderBy('order_number','asc')->get();
        return view('admin.customer.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        // if (is_null($this->user) || !$this->user->can('admin.customer.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $customer = User::find($id);
        $this->validate($request, [
            'name'          => 'required|max:100',
            'email'         => 'required|unique:users,email,'. $customer->id,
            'phone'         => 'nullable|unique:users,phone,'. $customer->id,
            // 'address'       => 'nullable',
            'image'         => 'nullable',
            'status'        => 'required',
        ]);
        DB::beginTransaction();
        try {

            $customer->name          = $request->name;
            $customer->last_name     = $request->last_name;
            $customer->email         = $request->email;
            $customer->phone         = $request->phone;
            $customer->dial_code     = $request->dial_code;
            $customer->local_pickup	 = $request->local_pickup ? 1:0;
            // $customer->dob           = $request->dob;
            // $customer->address       = $request->address;
            $customer->status        = $request->status;

            if ($request->hasFile('image')) {

                // Delete the existing image file if it exists
                if (File::exists(public_path($customer->image))) {
                    File::delete(public_path($customer->image));
                }

                // Upload and save the new image
                $image = $request->file('image');
                $base_name  = preg_replace('/\..+$/', '', $image->getClientOriginalName());
                $base_name  = explode(' ', $base_name);
                $base_name  = implode('-', $base_name);
                $base_name  = Str::lower($base_name);
                $image_name = $base_name . "-" . uniqid() . "." . $image->getClientOriginalExtension();
                $file_path  = 'uploads/UserInfo';
                $image->move(public_path($file_path), $image_name);
                $customer->image  = $file_path . '/' . $image_name;

            }

            if ($request->has('plan_id') && !empty($request->plan_id)) {
                $planData = Plan::find($request->plan_id);
                $customer->is_subscriber = 1;
                $customer->current_plan_id = $planData->id;
                $customer->current_plan_name = $planData->name;
                $customer->subscription_start = now();
                $customer->subscription_end = now()->addYears($planData->subscription_year);
                $customer->subscription_card_peryear = $planData->subscription_peryear_card;

                $currentYearStart = now();
                for ($i = 0; $i < $planData->subscription_year; $i++) {
                    $subscription = new UserSubscription();
                    $subscription->user_id = $customer->id;
                    $subscription->subscription_card_peryear = $planData->subscription_peryear_card;
                    $subscription->order_card_peryear = 0;
                    $subscription->year_start = $currentYearStart;
                    $subscription->year_end = $currentYearStart->copy()->addYear();
                    $subscription->save();
                    $currentYearStart = $subscription->year_end;
                }
            }
 

            $customer->save();

            Address::updateOrCreate(
                ['id' => $request->address_id],
                [
                    'user_id' => $customer->id,
                    'first_name' => $request->name,
                    'last_name' => $request->last_name,
                    'street' => $request->street,
                    'apt_unit' => $request->apt_unit,
                    'city' => $request->city,
                    'zip_code' => $request->zip_code,
                    'country' => $request->country,
                    'state' => $request->state,
                ]
            );

        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            Toastr::error(__('messages.toastr.update_error'), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.customer.index');
        }
        DB::commit();
        Toastr::success(__('messages.toastr.customer_upate'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.customer.index');
    }

    public function updatePassword(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.customer.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $this->validate($request, [
            'password' => ['required','string','min:8','regex:/[A-Z]/','regex:/[!@#$%^&*(),.?":{}|<>]/',],
        ]);
        $user = User::find($request->user_id);
        DB::beginTransaction();
        try {
            $user->password    = Hash::make($request->password);
            $user->save();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error(__('messages.toastr.password_change_error'), 'Error', ["positionClass" => "toast-top-right"]);
            return redirect()->back();
        }
        DB::commit();
        Toastr::success(__('messages.toastr.password_change_mesage'), 'Success', ["positionClass" => "toast-top-right"]);
        return redirect()->back();
    }


    public function delete($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.customer.delete')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        DB::beginTransaction();
        try {
            $customer = User::find($id);

            if ($customer) {
                Address::where('user_id', $customer->id)->delete();
                $orders = Order::where('user_id', $customer->id)->get();
                foreach ($orders as $order) {
                    OrderDetail::where('order_id', $order->id)->delete();

                    // Get order cards to delete images
                    $orderCards = OrderCard::where('order_id', $order->id)->get();
                    foreach ($orderCards as $card) {
                        if ($card->front_page && File::exists(public_path($card->front_page))) {
                            File::delete(public_path($card->front_page));
                        }
                        if ($card->back_page && File::exists(public_path($card->back_page))) {
                            File::delete(public_path($card->back_page));
                        }
                    }

                    // Delete order cards
                    OrderCard::where('order_id', $order->id)->delete();
                    $order->delete();
                }
                Transaction::where('user_id', $customer->id)->delete();
                SupportTicket::where('user_id', $customer->id)->delete();
                if (File::exists(public_path($customer->image))) {
                    File::delete(public_path($customer->image));
                }
                $customer->delete();
            }
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            Toastr::error(__('messages.toastr.user_delete_error'), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.customer.index');
        }
        DB::commit();
        Toastr::success(__('messages.toastr.user_delete_success'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.customer.index');
    }

    public function view($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.customer.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] =  __('messages.crud.customer_view');
        $data['user'] = User::find($id);
        $data['address'] = Address::where('user_id', $data['user']->id)->orderBy('id', 'desc')->first();
        // $data['cards'] = Card::where('user_id', $id)->withCount('analytics')->orderBy('id', 'desc')->get();
        return view('admin.customer.view', compact('data'));
    }

    function generateCustomUniqueId($length = 16) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $uniqueId = '';
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $uniqueId .= $characters[$index];
        }
        return $uniqueId;
    }

    public function disable(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
    
            if ($user->status == 1) {
                $user->status = 0;
                Toastr::success(trans('Successfully disabled the customer account'), 'Success', ["positionClass" => "toast-top-center"]);
            } else {
                $user->status = 1;
                Toastr::success(trans('Successfully activated the customer account'), 'Success', ["positionClass" => "toast-top-center"]);
            }
    
            $user->save();
    
        } catch (\Exception $e) {
            Toastr::error(trans('An error occurred while updating the account status'), 'Error', ["positionClass" => "toast-top-center"]);
        }
    
        return redirect()->route('admin.customer.index');
    }
    

    public function authAs(Request $request, $id)
    {
        $user = User::where('id', $id)->where('status', 1)->first();

        if (isset($user)) {
            Auth::guard('user')->loginUsingId($user->id);
            return redirect()->route('user.dashboard');
        } else {
            Toastr::error(trans('This customer account is currently inactive'), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.customer.index');
        }
    }

    public function cancelSubscription(Request $request)
    {
        try {
            $user = User::findOrFail($request->user_id);

            $user->is_subscriber = 0;
            $user->current_plan_id = null;
            $user->current_plan_name = null;
            $user->subscription_start = null;
            $user->subscription_end = null;
            $user->subscription_card_peryear = 0;
            $user->save();

            UserSubscription::where('user_id', $user->id)->delete();

            toastr()->success('Customer subscription cancelled successfully.');
        } catch (\Exception $e) {
            toastr()->error('An error occurred while cancelling the subscription.');
        }
        return redirect()->back();
    }
}
