<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderMail;
use App\Models\FinalGrading;
use App\Models\ManualLabel;
use App\Models\Order;
use App\Models\OrderCard;
use App\Models\OrderDetail;
use App\Models\Plan;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\PDF;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class OrderCertificateController extends Controller
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

    function index( Order $order )
    {
        $data['title'] = 'Manage Card Certificate';
        abort_if(!$order, 404);
        $data['order'] = $order->load('details.cards');
        
        $data['finalGradings'] = FinalGrading::get(['name', 'finalgrade'])->groupBy('finalgrade')->map(function ($items) {
            return $items->pluck('name')->toArray();
        });
        
        return view('admin.order.certificate', $data);
    }

    public function update(Request $request, Order $order )
    {
        $hasGradingValues = $request->filled(['centering', 'corners', 'edges', 'surface']);

        $rules = [];
        if ($request->is_authentic == 1 || $request->cert_no_grade == 1) {
            $rules = [
                'card' => 'nullable|min:1',
                'centering' => 'nullable|numeric|max:10.00',
                'corners' => 'nullable|numeric|max:10.00',
                'edges' => 'nullable|numeric|max:10.00',
                'surface' => 'nullable|numeric|max:10.00',
                'card_year' => 'nullable|max:4',
                'card_brand_name' => 'nullable|max:20',
                'card_card' => 'nullable|max:10',
                'card_card_name' => 'nullable|max:20',
                'card_notes' => 'nullable|max:25',
                'admin_card_notes_2' => 'nullable|max:25',
            ];
        } else {
            $rules = [
                'card' => 'required|min:1',
                'card_year' => 'nullable|max:4',
                'card_brand_name' => 'nullable|max:20',
                'card_card' => 'nullable|max:10',
                'card_card_name' => 'nullable|max:20',
                'card_notes' => 'nullable|max:25',
                'admin_card_notes_2' => 'nullable|max:25',
            ];

            if ($hasGradingValues) {
                $rules['centering'] = 'required|numeric|max:10.00';
                $rules['corners']   = 'required|numeric|max:10.00';
                $rules['edges']     = 'required|numeric|max:10.00';
                $rules['surface']   = 'required|numeric|max:10.00';
            } else {
                $rules['centering'] = 'nullable|numeric|max:10.00';
                $rules['corners']   = 'nullable|numeric|max:10.00';
                $rules['edges']     = 'nullable|numeric|max:10.00';
                $rules['surface']   = 'nullable|numeric|max:10.00';
                $rules['card']   = 'nullable';
            }
        }

        $rules['cert_no_grade.*'] = 'sometimes';
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with([
                    'scrollToIndex' => $request->scrollToIndex - 1
                ]);
        }

        $allCNo = collect($request->get('card'))->flatten()->filter();

        if ($allCNo->duplicates()->count() > 0) {
            toastr()->error('Duplicate Card Certification Number Found');
            return back();
        }

        abort_if(!$order, 404);
        $order->load('cards');

        DB::beginTransaction(); 
        try {
            $card_number = $request->card;
            $cardInfo = breakCertificateNo($card_number);
            if ($request->has('card_id') && !is_null($request->card_id)) {
                $cardCheck = OrderCard::where('id', $request->card_id)->first();
                $card_number = $cardCheck->card_number ?? $card_number;
            } elseif(!empty($card_number)) {
                $cardCheck = OrderCard::where('card_number', $card_number)->first();
            } else {
                $cardCheck = null;
            }
            if (!is_null($card_number) && !is_null($cardCheck)) {
                if ($cardCheck->order_id != $order->id) {
                    toastr()->error('Duplicate Card Certification Number Found');
                    return back();
                } elseif ($cardCheck->order_details_id == $request->details_id) {
                    $card = $cardCheck;
                }
            } elseif (is_null($card_number) && !is_null($cardCheck) && $cardCheck->is_no_grade == 1) {
                if ($cardCheck->order_id != $order->id) {
                    dd($cardCheck, $order->id);
                    toastr()->error('Duplicate Card Certification Number Found');
                    return back();
                } elseif ($cardCheck->order_details_id == $request->details_id) {
                    $card = $cardCheck;
                }
            } else {
                $card = new OrderCard();
            }

            $card->order_id = $order->id;
            $card->order_details_id = $request->details_id;
            $card->is_no_grade = isset($request->cert_no_grade) && $request->cert_no_grade != 0 ? 1 : 0;
            $card->is_authentic = isset($request->is_authentic) && $request->is_authentic != 0 ? 1 : 0;

            if (!$hasGradingValues && $card->is_no_grade == 0 && $card->is_authentic == 0) {
                $card->is_graded = 0;
                $card->card_number = $card_number;
            } else {
                if($card->is_no_grade==0 && $card->is_authentic==0){

                    $card->card_number = $card_number;
                    $card->centering = $request->centering;
                    $card->corners = $request->corners;
                    $card->edges = $request->edges;
                    $card->surface = $request->surface;
                    $final_grading = calculateFinalGrade([$card->centering,$card->corners,$card->edges,$card->surface]);
                    $card->final_grading = $final_grading;
                    if($card->centering == '10' && $card->corners == '10' &&  $card->edges == '10' && $card->surface == '10' && $final_grading == '10'){
                        $grad = DB::table('finalgrading_name')->where('id',21)->first();
                    }else{
                        $grad = DB::table('finalgrading_name')->where('finalgrade',$final_grading)->first();
                    }
                    $card->final_grading_name = $grad->name ?? '';

                } elseif($card->is_authentic == 1) {

                    $grad = DB::table('finalgrading_name')->where('finalgrade','A')->first();
                    $card->card_number = $card_number;
                    $card->centering = 0;
                    $card->corners = 0;
                    $card->edges = 0;
                    $card->surface = 0;
                    $card->final_grading = 'A';
                    $card->final_grading_name = $grad->name ?? '';
                    
                } else{
                    $card->card_number = null;
                    $card->centering = 0;
                    $card->corners = 0;
                    $card->edges = 0;
                    $card->surface = 0;
                    $card->final_grading = 0;
                    $card->final_grading_name = '';
                    $card->front_page = null;
                    $card->back_page = null;

                }

                $card->is_graded = 1;
            }

            $card->no_grade_reason = $request->cert_no_grade_reason ?? null;
            $card->prefix = $cardInfo['prefix'];
            $card->rand_num = $cardInfo['random'];
            $card->postfix = $cardInfo['postfix'];
            
            $card->year = $request->card_year;
            $card->brand_name = $request->card_brand_name;
            $card->card = $request->card_card;
            $card->card_name = $request->card_card_name;
            $card->notes = $request->card_notes;
            $card->admin_notes = $request->admin_card_notes;
            $card->admin_notes_2 = $request->admin_card_notes_2;
            $card->item_name = "{$request->card_year} {$request->card_brand_name} {$request->card_card} {$request->card_card_name}";

            $card->save();
            
            $order = Order::withSum('details', 'qty')->where('id', $card->order_id)->first();

            if (!$hasGradingValues && $card->is_no_grade == 0 && $card->is_authentic == 0) {
            } else {
                if ($order->status <= 20) {
                    if($order->details_sum_qty == $order->isGradedCards->count())
                    {
                        if($order->status != 20) {
    
                            $status = config('static_array.status');
                            $setting = getSetting();
                            $body = "The status of your order {$order->order_number} has been updated.";
                            $msg = 'Your order has completed the grading process! We will update you once the encapsulation process is complete.';
                
                            $data = [
                                'subject' => 'Order Update From '.$setting->site_name.': '.$status[20],
                                'greeting' => 'Hi, '.$order->rUser?->name.' '.$order->rUser?->last_name,
                                'body' => $body,
                                'order_number' => $order->order_number,
                                'status' => $status[20],
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
                        $order->status = 20;
                    } else {
                        if($order->status != 15) {
    
                            $status = config('static_array.status');
                            $setting = getSetting();
                            $body = "The status of your order {$order->order_number} has been updated.";
                            $msg = 'We have started the grading process and will send you an update once we are complete!';
                
                            $data = [
                                'subject' => 'Order Update From '.$setting->site_name.': '.$status[15],
                                'greeting' => 'Hi, '.$order->rUser?->name.' '.$order->rUser?->last_name,
                                'body' => $body,
                                'order_number' => $order->order_number,
                                'status' => $status[15],
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
    
                            $order->status = 15;
                        } 
                    }
                }
            }

            $order->save();

        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            toastr()->error('Card Certification Number Not Updated');
            return back();
        }

       DB::commit();
       toastr()->success('Card Certification Number Updated');
       return redirect()->back()->with([
            'scrollToIndex' => $card->is_graded == 1 ? $request->scrollToIndex : 0
        ]);
    } 


    public function getLabel(Request $request, $order, $id)
    {
        try {
            $cert = OrderCard::with('details', 'finalGrade')->where('is_no_grade', '0')->where('id', $id)->first();

            // $note = $cert->details->notes;
            $note = $cert->notes;
            // dd($note);
            if (strlen($note) > 25) {
                Toastr::error('Note length should be less than 25 characters', 'Error');
                return back();
            }
            
            if (!$cert) {
                Toastr::success('Certificate with grade not found', 'Error');
                return back();
            }
            
            $finalGradings = FinalGrading::get(['name', 'finalgrade'])->groupBy('finalgrade')->map(function ($items) {
                return $items->pluck('name')->toArray();
            });
            // dd($finalGradings);

            if(collect([$cert->centering, $cert->corners, $cert->edges, $cert->surface])->sum()==40){
                $html = view('admin.order.print-label.label', [
                    'cert' => $cert,
                    'finalGradings' => $finalGradings,
                    'isManual' => false
                    ])->render();
            }elseif(collect([$cert->centering, $cert->corners, $cert->edges, $cert->surface])->sum() < 40){
           
                $html = view('admin.order.print-label.label_white', [
                    'cert' => $cert,
                    'finalGradings' => $finalGradings,
                    'isManual' => false
                    ])->render();
            }else{
                Toastr::error('Grading maybe wrong', 'Error');
                return back();
            }

            // return $html;
            // return view('admin.order.print-label.label', [
            //     'certs' => $certs,
            //     'finalGradings' => $finalGradings,
            //     ]);

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

            return $pdf->download('label'.$id.'.pdf');

        } catch (\Exception $e) {
            dd($e);
            Toastr::error('Error Generating Label', 'Error');
            return back();
        }
    }


    public function manualScanAndSave(Request $request, Order $order)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp',
        ]);
        try {

            $card = OrderCard::findOrFail($request->card_id);

            $imagePath = uploadGeneralImage($request->image, 'card');

            if ($request->page_type === 'front_page') {
                $card->front_page = $imagePath;
            } elseif ($request->page_type === 'back_page') {
                $card->back_page = $imagePath;
            }

            $card->save();


            if ($order->status >= 20 && $order->status <= 30) {
                if ($order->cards->where('is_no_grade', 0)->count() == $order->cards->whereNotNull('front_page')->count())
                {
                    if($order->status != 30) {
    
                        $status = config('static_array.status');
                        $setting = getSetting();
                        $body = "The status of your order {$order->order_number} has been updated.";
                        $msg = 'We have now finished encapsulating your cards. We will update you once your order is ready to be shipped!';
            
                        $data = [
                            'subject' => 'Order Update From '.$setting->site_name.': '.$status[30],
                            'greeting' => 'Hi, '.$order->rUser?->name.' '.$order->rUser?->last_name,
                            'body' => $body,
                            'order_number' => $order->order_number,
                            'status' => $status[30],
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
    
                        $order->status = 30;
                    }
                    
                } else {
                    if($order->status != 25) {
    
                        $status = config('static_array.status');
                        $setting = getSetting();
                        $body = "The status of your order {$order->order_number} has been updated.";
                        $msg = 'We have started slabbing your cards and will update you once we are complete!';
            
                        $data = [
                            'subject' => 'Order Update From '.$setting->site_name.': '.$status[25],
                            'greeting' => 'Hi, '.$order->rUser?->name.' '.$order->rUser?->last_name,
                            'body' => $body,
                            'order_number' => $order->order_number,
                            'status' => $status[25],
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
    
                        $order->status = 25;
                    }
                }
            }

            $order->save();

        } catch (\Exception $e) {
            // dd($e);
            Toastr::error('Error uploading image', 'Error');
            return redirect()->back()->with([
                'scrollToIndex' => $request->scrollToIndex
            ]);
        }

        Toastr::success('Image uploaded successfully', 'Success');
        return redirect()->back()->with([
            'scrollToIndex' => $request->scrollToIndex
        ]);
    }
    public function scanAndSave(Request $request, Order $order)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp',
            'page_type' => 'required|in:front_page,back_page',
        ]);

        try {

            $card = OrderCard::findOrFail($request->card_id);


            $imagePath = uploadGeneralImage($request->image, 'card');
        
            if ($request->page_type == 'front_page') {
                $card->front_page = $imagePath;
            } else{
                $card->back_page = $imagePath;
            }

            $card->save();


            if ($order->status >= 20 && $order->status <= 30) {
                if ($order->cards->where('is_no_grade', 0)->count() == $order->cards->whereNotNull('front_page')->count())
                {
                    if($order->status != 30) {
    
                        $status = config('static_array.status');
                        $setting = getSetting();
                        $body = "The status of your order {$order->order_number} has been updated.";
                        $msg = 'We have now finished encapsulating your cards. We will update you once your order is ready to be shipped!';
            
                        $data = [
                            'subject' => 'Order Update From '.$setting->site_name.': '.$status[30],
                            'greeting' => 'Hi, '.$order->rUser?->name.' '.$order->rUser?->last_name,
                            'body' => $body,
                            'order_number' => $order->order_number,
                            'status' => $status[30],
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
    
                        $order->status = 30;
                    }
                    
                } else {
                    if($order->status != 25) {
    
                        $status = config('static_array.status');
                        $setting = getSetting();
                        $body = "The status of your order {$order->order_number} has been updated.";
                        $msg = 'We have started slabbing your cards and will update you once we are complete!';
            
                        $data = [
                            'subject' => 'Order Update From '.$setting->site_name.': '.$status[25],
                            'greeting' => 'Hi, '.$order->rUser?->name.' '.$order->rUser?->last_name,
                            'body' => $body,
                            'order_number' => $order->order_number,
                            'status' => $status[25],
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
    
                        $order->status = 25;
                    }
                }
            }

            $order->save();


        } catch (\Exception $e) {
            // dd($e);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return [
            'success' => true,
            'message' => 'Card '.($request->page_type === 'front_page' ? 'Front' : 'Back').' Page uploaded successfully',
        ];
    }

    public function cardStore(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'year' => 'required',
            'brand' => 'required|string|max:255',
            'cardNumber' => 'required',
            'playerName' => 'required|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);
    
        $setting = getSetting();
    
        DB::beginTransaction();
    
        try {
            $order = Order::where('id', $request->order_id)->first();
            if (!$order) {
                throw new \Exception('Order not found');
            }
    
            $unit_price = $order->unit_price;
            $payment_status = $order->payment_status;
            $is_extra = 0;

            $currentSubscription = $order->rUser->getCurrentSubscription();
            if ($currentSubscription) {
                if($currentSubscription->order_card_peryear < $currentSubscription->subscription_card_peryear && $order->is_redeemed == 1) {
                    $currentSubscription->order_card_peryear += 1;
                    $currentSubscription->save();
                    $order->total_order_qty += 1;
                } elseif($currentSubscription->order_card_peryear >= $currentSubscription->subscription_card_peryear && $order->is_redeemed == 1) {
                    $transaction = Transaction::where('order_id', $order->id)->first();

                    if($order->total_order_qty >= Plan::where('type', 'general')->where('status', 1)->first()->minimum_card) 
                    {
                        $plan = Plan::where('type', 'general')->where('status', 1)->first();
                        $unit_price = $plan->price;

                        $total_amount = 0;
                        $orderDetails = OrderDetail::where('order_id', $order->id)->where('is_extra', 1)->get();

                        foreach ($orderDetails as $orderDetail) {
                            $line_price = $unit_price * $orderDetail->qty;
                            $orderDetail->line_price = $line_price;
                            $orderDetail->save();

                            $total_amount += $line_price;
                        }
                        $total_amount += $unit_price * 1;
                        $gst = ($order->gst_tax * $total_amount) / 100;
                        $pst = ($order->pst_tax * $total_amount) / 100;
                        $total_tax = $gst + $pst;

                        $order->net_unit_price = $unit_price;
                        $order->unit_price = $unit_price;
                        $order->extra_price = $total_tax;
                        $order->total_price = $total_amount + $total_tax;
                        $transaction->amount = $order->total_price;

                    } else {
                        $plan = Plan::where('type', 'single')->where('status', 1)->first();
                        $unit_price = $plan->price;

                        $gst = $order->gst_tax * $unit_price / 100;
                        $pst = $order->pst_tax * $unit_price / 100;
                        $total_tax = $gst + $pst;
                        
                        $order->extra_price += $total_tax;
                        $order->total_price += ($unit_price + $total_tax);
                        $transaction->amount += $unit_price + ($order->gst_tax * $unit_price / 100 + $order->pst_tax * $unit_price / 100);
                    }
                    
                    $payment_status = 0;
                    $transaction->status = $payment_status;
                    $transaction->save();
                    $order->extra_cards += 1;
                    $is_extra = 1;

                    $order->total_order_qty += 1;
                    $order->payment_status = $payment_status;
                }
            } else {
                $payment_status = 0;

                $transaction = Transaction::where('order_id', $order->id)->first();
                $transaction->amount += $unit_price + ($order->gst_tax * $unit_price / 100 + $order->pst_tax * $unit_price / 100);
                $transaction->status = $payment_status;
                $transaction->save();
                $order->extra_cards += 1;
                $is_extra = 1;

                $gst = $order->gst_tax * $unit_price / 100;
                $pst = $order->pst_tax * $unit_price / 100;
                $total_tax = $gst + $pst;
    
                $order->total_order_qty += 1;
                $order->extra_price += $total_tax;
                $order->total_price += ($unit_price + $total_tax);
                $order->payment_status = $payment_status;
            }

            $order->save();
    
            $orderDetails = new OrderDetail();
            $orderDetails->order_id = $order->id;
            $orderDetails->year = $request->year;
            $orderDetails->brand_name = $request->brand;
            $orderDetails->brand_id = 1;
            $orderDetails->card = $request->cardNumber;
            $orderDetails->card_name = $request->playerName;
            $orderDetails->notes = $request->notes;
            $orderDetails->qty = 1;
            if($is_extra == 1) {
                $orderDetails->line_price = 1 * $unit_price;
            } else {
                $orderDetails->line_price = 0.00;
            }
            $orderDetails->is_extra = $is_extra;
            $orderDetails->item_name = "{$request->year} {$request->brand} {$request->cardNumber} {$request->playerName}";
            $orderDetails->save();

        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            toastr()->error('An unexpected error occured while creating card');
            return back();
        }

        DB::commit();
        toastr()->success('New card created successfully');
        return back();
    }

    public function scanCard()
    {
        // dd(session('is_manual'));
        $data['title'] = "Scan Image";
        $data['is_manual'] = session('is_manual', 0); // Retrieve from session
        $data['pageType'] = session('page_type', ''); // Retrieve from session
        $data['cardId'] = session('card_id', 0); // Retrieve from session
        $data['order_id'] = session('order_id', 0); // Retrieve from session
        $data['scrollToIndex'] = session('scroll_index', 0); // Retrieve from session
        
        $data['finalGradings'] = FinalGrading::get(['name', 'finalgrade'])->groupBy('finalgrade')->map(function ($items) {
            return $items->pluck('name')->toArray();
        });
        
        if($data['is_manual'] == 1) {
            if (empty($data['pageType']) || empty($data['cardId'])) {
                abort(404);
            }
            $card = ManualLabel::where('id', $data['cardId'])->first();
            $data['is_manual'] = 1;
            $data['card'] = $card;
        } else {
            if (empty($data['pageType']) || empty($data['cardId']) || empty($data['order_id'])) {
                abort(404);
            }
            $card = OrderCard::where('id', $data['cardId'])->first();
            $data['is_manual'] = 0;
            $data['card'] = $card->load('details');
        }

        // $order = Order::where('id', $data['order_id'])->first();
        // $data['order'] = $order->load('details');


        return view('admin.order.scan_card', $data);
    }

    public function delete($order_details_id, $card_number, Request $request)
    {
        DB::beginTransaction();
        try {
            $details = OrderDetail::find($order_details_id);
            $order = $details->order;
            
            if ($order->total_order_qty <= 1) {
                Toastr::warning(__('At least one card must remain. You cannot delete all cards.'), 'Warning');
                return redirect()->back();
            }
            
            $card = OrderCard::where('card_number', $card_number)->first();
            $transaction = Transaction::where('order_id', $order->id)->first();
            $setting = getSetting();
            
            // Delete the card if it exists
            if ($card) {
                if ($card->front_page && File::exists(public_path($card->front_page))) {
                    File::delete(public_path($card->front_page));
                }
                if ($card->back_page && File::exists(public_path($card->back_page))) {
                    File::delete(public_path($card->back_page));
                }
                $card->delete();
            }
            
            if ($order) {
                $item_price = $details->line_price / $details->qty;
            
                // Adjust order details
                if ($details->qty > 1) {
                    if ($details->is_extra == 1) {
                        $order->extra_cards = max(0, $order->extra_cards - 1);
                    }
            
                    $details->qty = max(0, $details->qty - 1);
                    $details->line_price = $details->qty * $item_price;
                    $details->save();
                } else {
                    if ($details->is_extra == 1) {
                        $order->extra_cards = max(0, $order->extra_cards - 1);
                    }
            
                    $details->delete();
                }
            
                $order->total_order_qty = max(0, $order->total_order_qty - 1);
                $currentSubscription = $order->rUser->getCurrentSubscription();
            
                // Recalculate totals and transaction amount
                $total_price = $order->total_order_qty * $item_price;
            
                if ($order->is_redeemed == 1) {
                    if ($order->extra_cards < 1) {
                        $gst = $order->gst_tax * $total_price / 100;
                        $pst = $order->pst_tax * $total_price / 100;
                        $transaction_amount = 0.00;
            
                        $currentSubscription->order_card_peryear -= 1;
                        $currentSubscription->save();
                    } else {
                        $extra_card_total = $item_price * $order->extra_cards;
                        $gst = $order->gst_tax * $order->total_price / 100;
                        $pst = $order->pst_tax * $order->total_price / 100;
            
                        $transaction_amount = $extra_card_total 
                                            + ($order->gst_tax * $extra_card_total / 100) 
                                            + ($order->pst_tax * $extra_card_total / 100);
                    }
                } else {
                    $gst = $order->gst_tax * $total_price / 100;
                    $pst = $order->pst_tax * $total_price / 100;
                    $transaction_amount = $order->total_price + $gst + $pst;
                }
            
                $order->extra_price = $gst + $pst;
                $order->total_price = $total_price;
                $order->save();
            
                // Update transaction if it exists
                if ($transaction) {
                    $transaction->amount = $transaction_amount;
                    $transaction->save();
                }
            }

            DB::commit();
            Toastr::success(__('Card deleted successfully'), 'Success');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting card {$card_number}: " . $e->getMessage());
            Toastr::error(__('An error occurred while deleting the card'), 'Error');
        }

        return redirect()->back()->with([
            'scrollToIndex' => $request->scrollToIndex
        ]);
        return redirect()->back();
    }

}


