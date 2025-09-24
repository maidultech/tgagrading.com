<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderStoreRequest;
use App\Http\Requests\Admin\OrderUpdateRequest;
use App\Mail\OrderMail;
use App\Models\Brand;
use App\Models\Card;
use App\Models\FinalGrading;
use App\Models\Order;
use App\Models\OrderAdditionalCost;
use App\Models\OrderCard;
use App\Models\OrderDetail;
use App\Models\Plan;
use App\Models\Role;
use App\Models\ServiceLevel;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class OrderController extends Controller
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

    function subscriptions(Request $request) {
        $data['title'] = 'Manage Subscriptions';
        // $query = Order::withSum('details', 'qty')->orderBy('id', 'desc');
        $query = Order::withSum('details', 'qty')
                        ->orderBy('id', 'desc')
                        ->where('payment_status', 1)
                        ->where('item_type', 0);

        if ($request->has('customer')) {
            $query->where('user_id', $request->customer);
        }
        $data['rows'] = $query->get();

        return view('admin.order.subscription', $data);
    }
    /**
     * Display a listing of the categories.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $status = null)
    {

        // if (is_null($this->user) || !$this->user->can('admin.order.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = 'Manage Orders';
        // $query = Order::withSum('details', 'qty')->orderBy('id', 'desc');
        $query = Order::withSum('details', 'qty')
                        ->orderBy('id', 'desc')
                        ->whereNot('item_type', 0);

        if (!$request->has('customer')) {
            $query->where(function ($query) {
                $query->whereNot(function ($query) {
                    $query->where(function ($query) {
                        $query->where('status', 35)
                            ->orWhere('status', 40);
                    })
                    ->where('payment_status', 1);
                });
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('customer')) {
            $query->where('user_id', $request->customer);
        }
        $data['rows'] = $query->get();

        return view('admin.order.index', $data);
    }

    public function outGoing(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.order.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = 'Manage Outgoing Orders';
        $query = Order::withSum('details', 'qty')->where('payment_status', 1)
                        ->where(function ($query) {
                            $query->where('status', 35)
                                ->orWhere('status', 40);
                        })
                        ->orderBy('pay_date', 'desc')
                        ->orderByRaw('(SELECT amount FROM transactions WHERE transactions.order_id = orders.id LIMIT 1) DESC');

        $data['rows'] = $query->get();
        // dd($data['rows']->first());
        return view('admin.order.outgoing', $data);
    }
    public function outGoingView(Request $request,$id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.order.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $title = __('View Order');
        $order = Order::find($id);
        $trnx = Transaction::where('order_id', $id)->first();

        return view('admin.order.outgoingview', compact('title', 'trnx','order'));
    }
    public function view(Request $request,$id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.order.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }
        // return $id;

        $title = __('View Order');
        $order = Order::find($id);
        $trnx = Transaction::where('order_id', $id)->first();

        return view('admin.order.view', compact('title', 'trnx','order'));
    }

    public function shippingMethod(Request $request,$id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.order.view')) {
            //     abort(403, 'Sorry !! You are Unauthorized.');
            // }

            $title = 'Label Information';
            $order = Order::find($id);
            $trnx = Transaction::whereBelongsTo($order)->first();

        return view('admin.order.shipping_method', compact('title', 'order','trnx'));
    }

    public function download(Request $request, $id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.order.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $certificate_verification = OrderCard::with('details', 'finalGrade')
            ->where('is_no_grade', 0)
            ->where('is_graded', 1)
            ->where('order_id', $id)
            ->get();

        foreach ($certificate_verification as $cert) {
            $note = $cert->notes ?? $cert->details->notes;
            if (strlen($note) > 25) {
                Toastr::error('Note length should be less than 25 characters', 'Error');
                return back();
            }
        }

        if (!$certificate_verification || $certificate_verification->isEmpty()) {
            Toastr::success('Certificate with grade not found', 'Error');
            return back();
        }

        $finalGradings = FinalGrading::get(['name', 'finalgrade'])->groupBy('finalgrade')->map(function ($items) {
            return $items->pluck('name')->toArray();
        });

        $html = view('admin.order.print-label.all_label', [
            'certs' => $certificate_verification,
            'finalGradings' => $finalGradings,
            'isManual' => false
        ])->render();

        // return $html;
        // Generate PDF
        $pdf = SnappyPdf::setOptions([
            'margin-top' => 0,
            'margin-left' => 0,
            'margin-right' => 0,
            'margin-bottom' => 0,
            // 'page-width' => "261px",
            // 'page-height' => "80px",
            // 'page-width' => "264px",
            // 'page-height' => "158px",
            'page-width' => "262px",
            'page-height' => "154px",
            'enable-local-file-access' => true,
            'encoding' => 'UTF-8',
        ])->loadHTML($html);

        Toastr::success('Label Generated Successfully', 'Success');

        return $pdf->download('label'.$id.'.pdf');
    }



    public function create()
    {
        // if (is_null($this->user) || !$this->user->can('admin.order.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = __('Create New Order');
        $data['plan'] = Plan::findOrFail(1);
        $data['customer'] = User::orderby('id', 'desc')->has('addresses')->get();
        $data['serviceLevels'] = ServiceLevel::get();

        $data['total_cards'] = 0;

        return view('admin.order.create', $data);
    }

    function store(OrderStoreRequest $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.order.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $user_id = $request->user_id;
        $userData = User::find($user_id, ['phone','dial_code']);
        DB::beginTransaction();
        $__userAddress = collect(User::with('defaultAddress')->find($user_id)->defaultAddress)
            ->except([
                'created_at','updated_at','id','is_default','user_id'
            ])
            ->toArray();

        try {

            // order_id
            $tmp_order_id = Order::max('order_number') + 1;
            $tmp_order_id = $tmp_order_id < 1000 ? 1000 : $tmp_order_id;
            $order = new Order;

            $serviceLevel = ServiceLevel::findOrFail($request->service_level);
            $order->order_number = $tmp_order_id;
            $order->user_id = $user_id;
            $order->item_type = $request->item_type;
            $order->submission_type = $request->submission_type;
            $order->service_level_id = $request->service_level;
            $order->service_level_name = $serviceLevel->name;
            $order->est_day = $serviceLevel->estimated_days;
            $order->payment_status = 1;
            $order->pay_date = Carbon::now('America/Vancouver');
            $order->status = 5;
            $order->plan_id = 0;
            $order->is_custom_order = 1;
            $order->plan_details = null;
            $order->note = $request->get('comments');
            // $order->discount_percentage = 0;
            // $order->discount = 0;
            // $order->payment_fee = 0;
            // $order->vat = 0;

            $order->unit_price = $request->custom_unit_price;
            $order->total_order_qty = array_sum($request->get('quantity'));
            $order->extra_price = 0;
            $order->net_unit_price = $request->custom_unit_price;

            $order->save();

            // set order details
            // `${year} ${brand} ${cardNumber} ${playerName}`

            foreach ($request->get('year') as $key => $item) {
                $orderDetails = new OrderDetail();
                $orderDetails->order_id = $order->id;
                $orderDetails->year = $request->get('year')[$key];
                $orderDetails->brand_name = $request->get('brand')[$key];
                $orderDetails->brand_id = 1;
                $orderDetails->card = $request->get('cardNumber')[$key];
                $orderDetails->card_name = $request->get('playerName')[$key];
                $orderDetails->notes = $request->get('notes')[$key];
                $orderDetails->qty = $request->get('quantity')[$key];
                $orderDetails->line_price = $request->get('quantity')[$key] * $order->net_unit_price;
                $orderDetails->item_name = "{$orderDetails->year} {$orderDetails->brand_name} {$orderDetails->card} {$orderDetails->card_name}";
                $orderDetails->save();
            }


            // set transaction
            $tmp_trx_id = (int) Transaction::max('transaction_number') + 1;
            $tmp_trx_id = $tmp_trx_id < 1000 ? 1000 : $tmp_trx_id;

            $transaction = new Transaction;
            $transaction->transaction_number = $tmp_trx_id < 1000 ? 1000 : $tmp_trx_id;
            $transaction->user_id = $user_id;
            $transaction->amount =  count($request->get('quantity')) * $order->net_unit_price;
            $transaction->pay_date = Carbon::now('America/Vancouver');
            $transaction->plan_id = 0;
            $transaction->payment_method = $request->payment_mode;
            $transaction->currency = 'USD';
            $transaction->details = $request->comments;
            $transaction->status = 1;
            $transaction->order_id = $order->id;

            $userAddress = [
                'shippingName' => ($__userAddress['first_name']??'') . ' ' . ($__userAddress['last_name']??''),
                'shippingAddress' => ($__userAddress['street']??'') . ', ' . ($__userAddress['apt_unit']??''),
                'shippingCity' => $__userAddress['city'] ?? '',
                'shippingState' => $__userAddress['state'] ?? '',
                'shippingZip' => $__userAddress['zip_code'] ?? '',
                'shippingCountry' => $__userAddress['country'] ?? '',
                'shippingPhone' => ('+'.$userData->dial_code.$userData->phone)
            ];

            $transaction->shipping_data = ($userAddress);
            // $transaction->type = 'purchase';
            // $order->payment_method = $request->payment_mode;
            // $order->payment_status = 5;
            // $order->billing_address = $this->formatShippingInfo($request);


            $transaction->save();

        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
            toastr()->error('Order Failed due to internal error');
            return back();

        }
        DB::commit();
        toastr()->success('Custom Order Created successfully');
        return to_route('admin.order.index');
    }

    public function edit($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.order.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = __('Edit Order');
        $data['plan'] = Plan::findOrFail(1);
        $data['customer'] = User::orderby('id', 'desc')->has('addresses')->get();
        // $data['serviceLevels'] = ServiceLevel::get();
        $data['order'] = $order = Order::findOrFail($id);
        $data['charges'] =DB::table('order_additional_cost')->where('order_id', $id)->get();

        // if($order->is_custom_order){
        //     $data['title'] = __('Edit Custom Order');
        //     return view('admin.order.edit.custom', $data);
        // }else{
            return view('admin.order.edit.default', $data);
        // }

    }


    public function updateCustomOrder(OrderUpdateRequest $request, $id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.order.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }
        // dd($request->all()); 

            $user_id = $request->user_id;
            DB::beginTransaction();
            try {


                // order_id
                $order = Order::findOrFail($id);

                $serviceLevel = ServiceLevel::findOrFail($request->service_level);
                $order->user_id = $user_id;
                $order->item_type = $request->item_type;
                $order->submission_type = $request->submission_type;
                $order->service_level_id = $request->service_level;
                $order->service_level_name = $serviceLevel->name;
                $order->est_day = $serviceLevel->estimated_days;
                $order->payment_status = $request->payment_status;
                $order->status = $request->order_status;
                $order->plan_id = 0;
                $order->is_custom_order = 1;
                $order->plan_details = null;
                $order->note = $request->get('comments');
                // $order->discount_percentage = 0;
                // $order->discount = 0;
                // $order->payment_fee = 0;
                // $order->vat = 0;

                $order->admin_tracking_id = $request->admin_tracking_id;
                $order->admin_tracking_note = $request->admin_tracking_note;
                $order->shipping_method = $request->shipping_method; // for admin
                $order->customer_tracking_url = $request->customer_tracking_url;
                $order->customer_tracking_note = $request->customer_tracking_note;

                $order->unit_price = $request->custom_unit_price;
                $order->total_order_qty = array_sum($request->get('quantity'));
                $order->extra_price = 0;
                $order->net_unit_price = $request->custom_unit_price;

                $order->save();

                // set order details
                // `${year} ${brand} ${cardNumber} ${playerName}`

                $removedCard = $order->details()->whereNotIn('id', array_keys($request->get('year')));

                foreach ($removedCard->get() as $key => $value) {
                    $value->cards()->delete();
                }
                $removedCard->delete();


                foreach ($request->get('year') as $key => $item) {
                    $orderDetails = OrderDetail::findOrNew($key);
                    if($orderDetails->order_id != $id){
                        $orderDetails = new OrderDetail();
                    }
                    $orderDetails->order_id = $order->id;
                    $orderDetails->year = $request->get('year')[$key];
                    $orderDetails->brand_name = $request->get('brand')[$key];
                    $orderDetails->brand_id = 1;
                    $orderDetails->card = $request->get('cardNumber')[$key];
                    $orderDetails->card_name = $request->get('playerName')[$key];
                    $orderDetails->notes = $request->get('notes')[$key];
                    $orderDetails->qty = $request->get('quantity')[$key];
                    $orderDetails->line_price = $request->get('quantity')[$key] * $order->net_unit_price;
                    $orderDetails->item_name = "{$orderDetails->year} {$orderDetails->brand_name} {$orderDetails->card} {$orderDetails->card_name}";
                    $orderDetails->save();
                }


                // set transaction


                $transaction = $order->transaction;
                $transaction->status = 1;

                $__userAddress = collect(User::with('defaultAddress')->find($user_id)->defaultAddress)
                ->except([
                    'created_at','updated_at','id','is_default','user_id'
                ])
                ->toArray();



                $userAddress = $transaction->shipping_data;

                $userAddress['shippingName'] = $request->shippingName;
                $userAddress['shippingAddress'] = $request->shippingAddress;
                $userAddress['shippingCity'] = $request->shippingCity;
                $userAddress['shippingState'] = $request->shippingState;
                $userAddress['shippingZip'] = $request->shippingZip;
                $userAddress['shippingCountry'] = $request->shippingCountry;
                $userAddress['shippingPhone'] = '+'.$request->dial_code.$request->shippingPhone;

                $transaction->shipping_data = ($userAddress);
                // $transaction->type = 'purchase';

                // $order->payment_method = $request->payment_mode;
                // $order->payment_status = 5;
                // $order->billing_address = $this->formatShippingInfo($request);


                $transaction->save();

            } catch (\Throwable $th) {
                throw $th;
                DB::rollBack();
                toastr()->error('Order updating Failed due to internal error');
                return back();

            }
            DB::commit();
            toastr()->success('Custom Order Updated successfully');
            return redirect()->route('admin.order.index');
    }

    // public function update(OrderUpdateRequest $request, $id)
    // {
    //     // if (is_null($this->user) || !$this->user->can('admin.order.edit')) {
    //     //     abort(403, 'Sorry !! You are Unauthorized.');
    //     // }

    //     $user_id = $request->user_id;
    //     DB::beginTransaction();

    //     try {

    //         $order = Order::find($id);

    //         $planData = Plan::find($order->plan_id);

    //         $serviceLevel = ServiceLevel::find($request->service_level);

    //         $order->user_id = $user_id;
    //         $order->item_type = $request->item_type;
    //         $order->submission_type = $request->submission_type;
    //         $order->service_level_id = $request->service_level;
    //         $order->service_level_name = $serviceLevel->name;
    //         $order->est_day = $serviceLevel->estimated_days;
    //         $order->payment_status = $request->payment_status;
    //         $order->status = $request->order_status;
    //         $order->note = $request->get('comments');
    //         // $order->discount_percentage = 0;
    //         // $order->discount = 0;
    //         // $order->payment_fee = 0;
    //         // $order->vat = 0;

    //         $order->customer_tracking_id = $request->customer_tracking_id;
    //         $order->admin_tracking_note = $request->admin_tracking_note;
    //         $order->customer_tracking_url = $request->customer_tracking_url;
    //         $order->customer_tracking_note = $request->customer_tracking_note;

    //         $order->unit_price = $planData->price;

    //         $order->total_order_qty = array_sum($request->get('quantity'));

    //         $order->extra_price = $serviceLevel->extra_price;
    //         $order->net_unit_price = $planData->price + $serviceLevel->extra_price;

    //         $order->save();

    //         // set order details
    //         // `${year} ${brand} ${cardNumber} ${playerName}`

    //         $order->details()->whereNotIn('id', array_keys($request->get('year')))->delete();

    //         foreach ($request->get('year') as $key => $item) {
    //             $orderDetails = OrderDetail::findOrNew($key);
    //             $orderDetails->order_id = $order->id;
    //             $orderDetails->year = $request->get('year')[$key];
    //             $orderDetails->brand_name = $request->get('brand')[$key];
    //             $orderDetails->brand_id = 1;
    //             $orderDetails->card = $request->get('cardNumber')[$key];
    //             $orderDetails->card_name = $request->get('playerName')[$key];
    //             $orderDetails->notes = $request->get('notes')[$key];
    //             $orderDetails->qty = $request->get('quantity')[$key];
    //             $orderDetails->line_price = $request->get('quantity')[$key] * $order->net_unit_price;
    //             $orderDetails->item_name = "{$orderDetails->year} {$orderDetails->brand_name} {$orderDetails->card} {$orderDetails->card_name}";
    //             $orderDetails->save();
    //         }


    //         // set transaction

    //         $transaction = $order->transaction;
    //         $transaction->status = 1;

    //         $__userAddress = collect(User::with('defaultAddress')->find($user_id)->defaultAddress)
    //         ->except([
    //             'created_at','updated_at','id','is_default','user_id'
    //         ])
    //         ->toArray();

    //         $userAddress = [
    //             'shippingName' => $request->shippingName,
    //             'shippingAddress' => $request->shippingAddress,
    //             'shippingCity' => $request->shippingCity,
    //             'shippingState' => $request->shippingState,
    //             'shippingZip' => $request->shippingZip,
    //             'shippingCountry' => $request->shippingCountry,
    //             'shippingPhone' => '+'.$request->dial_code.$request->shippingPhone
    //         ];

    //         $transaction->shipping_data = ($userAddress);
    //         // $transaction->type = 'purchase';

    //         // $order->payment_method = $request->payment_mode;
    //         // $order->payment_status = 5;
    //         // $order->billing_address = $this->formatShippingInfo($request);


    //         $transaction->save();

    //     } catch (\Throwable $th) {
    //         throw $th;
    //         DB::rollBack();
    //         toastr()->error('Order updating Failed due to internal error');
    //         return back();

    //     }
    //     DB::commit();
    //     toastr()->success('Custom Order Updated successfully');
    //     return redirect()->route('admin.order.index');
    // }
    public function update(Request $request, $id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.order.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }
        // dd($request->all());
        // First, delete old additional costs for this order
        // dd( $request->all()); 

        $user_id = $request->user_id;
        DB::beginTransaction();
        try {

            // save order details info
            $itemIds = $request->get('item_id');
            foreach ($itemIds as $k => $itemId) {
                $details = OrderDetail::find($itemId);
                if ($details) {
                    $details->year = $request->get('year')[$k] ?? $details->year;
                    $details->brand_name = $request->get('brand')[$k] ?? $details->brand_name;
                    $details->card = $request->get('cardNumber')[$k] ?? $details->card;
                    $details->card_name = $request->get('playerName')[$k] ?? $details->card_name;
                    $details->notes = $request->get('notes')[$k] ?? $details->notes;
                    $details->item_name = "{$details->year} {$details->brand_name} {$details->card} {$details->card_name}";
                    $details->save();
                }

            }

            $order = Order::find($id);

            $totalGradedCards = $order->details->sum(function ($detail) {
                return $detail->gradedCards->count();
            });

            if ($totalGradedCards <= 0 && $request->order_status >= 20) {
                toastr()->error('Grading is still incomplete. First, grade all the cards.');
                return back();
            } elseif (
                $order->cards->where('is_no_grade', 0)->count() != $order->cards->whereNotNull('front_page')->count()
                && $request->order_status >= 30
            ) {
                toastr()->error('Slabbing is still incomplete. Please Take photos of cards.');
                return back();
            } elseif ($request->order_status == 40 && $request->payment_status == 0) {
                toastr()->error("Please ensure the payment for this order has been paid before changing the status to 'Order Shipped'.");
                return back();
            }


            $order->payment_status = $request->payment_status;

            if($request->payment_status == 1) {
                $order->pay_date = Carbon::now('America/Vancouver');
                $transaction = $order->transaction;
                $transaction->status = 1;
                $transaction->save();
            }
            
            $order->status = $request->order_status;
            if($request->order_status == 10) {
                $order->received_at = Carbon::now('America/Vancouver');
            }
            if($request->order_status == 35) {
                $order->ready_shipping_at = Carbon::now('America/Vancouver');
                $order->ready_shipping_mail = 0;
            }
            $order->admin_tracking_id = $request->admin_tracking_id;
            $order->admin_tracking_note = $request->admin_tracking_note;
            $order->shipping_method = $request->shipping_method; // for admin
            $order->customer_tracking_url = $request->customer_tracking_url;
            $order->customer_tracking_note = $request->customer_tracking_note;
            $order->save();


            OrderAdditionalCost::where('order_id', $id)->delete();
            if (!empty($request->details) && is_array($request->details)) {
                foreach ($request->details as $key => $detail) {
                    $price = $request->price[$key] ?? null;
                    if ($detail || $price) {
                        OrderAdditionalCost::insert([
                            'order_id' => $id,
                            'details' => $detail,
                            'price' => $price,
                        ]);
                    }
                }
            }

            $status = config('static_array.status');

            $setting = Setting::first();

            $body = "The status of your order {$order->order_number} has been updated.";

            if($order->status == 10) {
                $msg = 'Your order has been received in our office. We will start working on it shortly!';
            } elseif($order->status == 15) {
                $msg = 'We have started the grading process and will send you an update once we are complete!';
            } elseif($order->status == 20) {
                $msg = 'Your order has completed the grading process! We will update you once the encapsulation process is complete.';
            } elseif($order->status == 25) {
                $msg = 'We have started slabbing your cards and will update you once we are complete!';
            } elseif($order->status == 30) {
                $msg = 'We have now finished encapsulating your cards. We will update you once your order is ready to be shipped!';
            } elseif($order->status == 35) {
                $msg = "Congratulations! Your order is now complete. You now have the option to view your grades. Please <a href=\"" . route('login') . "\">login</a> to your account to pay for your order and we will have it shipped out right away.";
            } elseif($order->status == 40) {
                $msg = 'We have shipped out your order. Please log into your account to view your tracking <br>information. Thank you for submitting your cards with '.$setting->site_name.'. We truly appreciate it and look forward to serving you in the future!';
            } else {
                $msg = 'Undefined Status';
            }

            $data = [
                'subject' => 'Order Update From '.$setting->site_name.': '.$status[$order->status],
                'greeting' => 'Hi, '.$order->rUser?->name.' '.$order->rUser?->last_name,
                'body' => $body,
                'order_number' => $order->order_number,
                'status' => $status[$order->status],
                'site_name' => $setting->site_name ?? config('app.name'),
                'thanks' => $msg,
                'site_url' => url('/'),
                'footer' => 1,
            ];

            if (!in_array($order->status, [0, 5, 50]))
            {
                try {
                    Mail::to($order->rUser?->email)->send(new OrderMail($data));
                } catch (\Exception $e) {
                    Log::alert('Order mail not sent. Error: ' . $e->getMessage());
                }
            }
            // $transaction = $order->transaction;
            // $transaction->status = 1;
            // $transaction->save();

        } catch (\Throwable $th) {
            // throw $th;
            // dd($th);
            DB::rollBack();
            toastr()->error('Order updating Failed due to internal error');
            return back();

        }
        DB::commit();
        toastr()->success('Order Updated successfully');
        return redirect()->route('admin.order.index');
    }

    public function updateAddress(Request $request, $id)
    {

        $rules = [
            'address1' => 'required|max:255',
            'city' => 'required|max:255',
            'state' => 'required|max:255',
            'zip' => 'required|max:255',
            'country' => 'required|max:255',
        ];

        $messages = [
            'address1.required' => 'The shipping address is required.',
            'city.required' => 'The shipping city is required.',
            'state.required' => 'The shipping state is required.',
            'zip.required' => 'The shipping postal code is required.',
            'country.required' => 'The shipping country is required.',
        ];

        $validatedData = $request->validate($rules, $messages);
        $order = Order::find($id);

        $suiteParts = explode('|', $request->suite_id);

        $shippingMethod = $suiteParts[0]; // 'canada_post' or 'ups'
        $serviceCodeJson = $suiteParts[1]; // '{"DOM.EP":"Expedited Parcel"}'

        $shippingServiceCode = json_decode($serviceCodeJson, true); // converts to associative array

        $order->shipping_method = $shippingMethod;
        $order->shipping_method_service_code = $shippingServiceCode;
        $order->shipping_notes = $request->shipping_notes;

        if($shippingMethod == 'local_pickup') {
            $order->status = 40;
        }

        $order->save();


        if($request->trackingID){
            $order->admin_tracking_id = $request->trackingID;
            $order->status = 40;
            $order->save();

            $status = config('static_array.status');
            $setting = Setting::first();
            $body = "The status of your order {$order->order_number} has been updated.";
            $msg = 'We have shipped out your order. Please log into your account to view your tracking <br>information. Thank you for submitting your cards with '.$setting->site_name.'. We truly appreciate it and look forward to serving you in the future!';

            $data = [
                'subject' => 'Order Update From '.$setting->site_name.': '.$status[$order->status],
                'greeting' => 'Hi, '.$order->rUser?->name.' '.$order->rUser?->last_name,
                'body' => $body,
                'order_number' => $order->order_number,
                'status' => $status[$order->status],
                'site_name' => $setting->site_name ?? config('app.name'),
                'thanks' => $msg,
                'site_url' => url('/'),
                'footer' => 1,
            ];

            try {
                Mail::to($order->rUser?->email)->send(new OrderMail($data));
            } catch (\Exception $e) {
                Log::alert('Order mail not sent. Error: ' . $e->getMessage());
            }
        }
        $transaction = $order->transaction;
        $shippingData = $transaction->shipping_data;
        $shippingData['shippingAddress'] = $validatedData['address1'];
        $shippingData['shippingCity'] = $validatedData['city'];
        $shippingData['shippingState'] = $validatedData['state'];
        $shippingData['shippingZip'] = $validatedData['zip'];
        $shippingData['shippingCountry'] = $validatedData['country'];
        $transaction->shipping_data = $shippingData;
        $transaction->save();

        toastr()->success('Shipping information updated successfully');
        return redirect()->back();
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);

            // Delete related order details
            OrderDetail::where('order_id', $order->id)->delete();

            $orderCards = OrderCard::where('order_id', $order->id)->get();

            foreach ($orderCards as $card) {
                if ($card->front_page && File::exists(public_path($card->front_page))) {
                    File::delete(public_path($card->front_page));
                }
                if ($card->back_page && File::exists(public_path($card->back_page))) {
                    File::delete(public_path($card->back_page));
                }
            }

            // Delete the order card records
            OrderCard::where('order_id', $order->id)->delete();

            // Delete related transactions
            Transaction::where('order_id', $order->id)->delete();

            // Delete the order itself
            $order->delete();

            DB::commit();
            Toastr::success(__('Order deleted successfully'), 'Success');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting order ID {$id}: " . $e->getMessage());
            Toastr::error(__('An error occurred while deleting the order'), 'Error');
        }

        return redirect()->back();
    }

}

