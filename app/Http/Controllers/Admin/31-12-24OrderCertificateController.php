<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderMail;
use App\Models\FinalGrading;
use App\Models\Order;
use App\Models\OrderCard;
use App\Models\OrderDetail;
use Barryvdh\DomPDF\Facade\PDF;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderCertificateController_old extends Controller
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
        // if (is_null($this->user) || !$this->user->can('admin.order.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }
      
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
        // if (is_null($this->user) || !$this->user->can('admin.order.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $request->validate([
            'card' => 'required|array|min:1',
            'card.*' => 'required|array|min:1',
            'card.*.*' => 'required_without:cert_no_grade.*.*',
            'centering.*.*' => [
                'required_without:cert_no_grade.*.*','nullable','numeric','max:10.00',
            ],
            'corners.*.*' => [
                'required_without:cert_no_grade.*.*','nullable','numeric','max:10.00',
            ],
            'edges.*.*' => [
                'required_without:cert_no_grade.*.*','nullable','numeric','max:10.00',
            ],
            'surface.*.*' => [
                'required_without:cert_no_grade.*.*','nullable','numeric','max:10.00',
            ],
            'cert_no_grade.*' => 'sometimes',
        ]);


        // $allCNo = collect($request->get('card'))->flatten();

        $allCNo = collect($request->get('card'))->flatten()->filter();

        if ($allCNo->duplicates()->count() > 0) {
            toastr()->error('Duplicate Card Certification Number Found');
            return back();
        }

        abort_if(!$order, 404);
        $order->load('cards');

        DB::beginTransaction();
        try {
            foreach ( $request->card as $details_id => $details ) {
                // $card = new OrderCard();

                foreach ( $details as $key => $card_number ) {
                    $cardInfo = breakCertificateNo($card_number);
                    if ($request->has('card_id') && !is_null($request->card_id)) {
                        $cardCheck = OrderCard::where('id', $request->card_id)->first();
                        $card_number = $cardCheck->card_number ?? $card_number;
                    } elseif(!empty($card_number)) {
                        $cardCheck = OrderCard::where('card_number', $card_number)->first();
                    } else {
                        $cardCheck = null;
                    }


                    // if ( !is_null($card_number) && !is_null($cardCheck) ) {
                    //     if ( $cardCheck->order_id != $order->id ) {
                    //         toastr()->error('Duplicate Card Certification Number Found');
                    //         return back();
                    //     } elseif ( $cardCheck->order_details_id == $details_id ) {
                    //         $card = $cardCheck;
                    //     }

                    // } else {
                    //     $card = new OrderCard();
                    // }

                    if (!is_null($card_number) && !is_null($cardCheck)) {
                        if ($cardCheck->order_id != $order->id) {
                            toastr()->error('Duplicate Card Certification Number Found');
                            return back();
                        } elseif ($cardCheck->order_details_id == $details_id) {
                            $card = $cardCheck;
                        }
                    } elseif (is_null($card_number) && !is_null($cardCheck) && $cardCheck->is_no_grade == 1) {
                        if ($cardCheck->order_id != $order->id) {
                            dd($cardCheck, $order->id);
                            toastr()->error('Duplicate Card Certification Number Found');
                            return back();
                        } elseif ($cardCheck->order_details_id == $details_id) {
                            $card = $cardCheck;
                        }
                    } else {
                        $card = new OrderCard();
                    }

                    $card->order_id = $order->id;
                    $card->order_details_id = $details_id;
                    $card->is_no_grade = isset($request->cert_no_grade[$details_id][$key]) && $request->cert_no_grade[$details_id][$key] != 0 ? 1 : 0;
                    $card->is_authentic = isset($request->is_authentic[$details_id][$key]) && $request->is_authentic[$details_id][$key] != 0 ? 1 : 0;

                    if($card->is_no_grade==0 && $card->is_authentic==0){
                        $card->card_number = $card_number;
                        $card->centering = $request->centering[$details_id][$key];
                        $card->corners = $request->corners[$details_id][$key];
                        $card->edges = $request->edges[$details_id][$key];
                        $card->surface = $request->surface[$details_id][$key];
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
                    }
                    $card->no_grade_reason = $request->cert_no_grade_reason[$details_id][$key] ?? null;
                    $card->prefix = $cardInfo['prefix'];
                    $card->rand_num = $cardInfo['random'];
                    $card->postfix = $cardInfo['postfix'];
                    $card->save();
                }
            }
            $order = Order::where('id', $card->order_id)->first();

            if ($order->status <= 20) {
                if($order->total_order_qty == $order->cards->count())
                {
                    if($order->status != 20) {

                        $status = config('static_array.status');
                        $setting = getSetting();
                        $body = "The status of your order {$order->order_number} has been updated.";
                        $msg = 'Your order has completed the grading process! You can now log in to view your grades. We will update you once the encapsulation process is complete.';
            
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

                        $order->status = 20;
                    } 
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
            $order->save();

        } catch (\Throwable $th) {
                DB::rollBack();
                dd($th);
                toastr()->error('Card Certification Number Not Updated');
                return back();
        }

       DB::commit();

       toastr()->success('Card Certification Number Updated');
       return redirect()->back()->with('scrollToBottom', true);
    //    return redirect()->back()->with('scrollTo', $request->input('item_id'));

    } 

    // public function delete( Request $request, Order $order, $id )
    // {
    //     if ( $order->total_order_qty > 2 ) {
    //         $order_card = OrderCard::findOrFail($id, );
    //         $order_card->details->decrement('qty');
    //         $order_card->delete();
    //         $order->decrement('total_order_qty');
    //         toastr()->success('Card Certification Delete');
    //     } else {
    //         toastr()->error('Min 1 card should be in the card');
    //     }
    //     return back();

    // }
    public function getLabel(Request $request, $order, $id)
    {
        // dd( $order);
        // dd( $id);
        try {
            $certs = OrderCard::with('details', 'finalGrade')->where('is_no_grade', '0')->where('id', $id)->get();
            foreach ($certs as $cert) {
               $note = $cert->details->notes;
               if (strlen($note) > 25) {
                   Toastr::error('Note length should be less than 25 characters', 'Error');
                   return back();
               }
            }

            if (!$certs || $certs->isEmpty()) {
                Toastr::success('Certificate with grade not found', 'Error');
                return back();
            }

            // if ($certs->where('is_no_grade', 1)->count() > 0) {
            //     Toastr::error('No Grade Card Cannot Be Printed', 'Error');
            //     return back();
            // }

            $finalGradings = FinalGrading::get(['name', 'finalgrade'])->groupBy('finalgrade')->map(function ($items) {
                return $items->pluck('name')->toArray();
            });

            $html = view('admin.order.print-label.label', [
                'certs' => $certs,
                'finalGradings' => $finalGradings,
                ])->render();

            // return view('admin.order.print-label.label', [
            //     'certs' => $certs,
            //     'finalGradings' => $finalGradings,
            //     ]);
            // Check if HTML output is requested
            if ($request->has('h')) {
                return response()->json([
                    'success' => true,
                    'data' => $html,
                ]);
            }

            // Generate PDF

            $fontPath = public_path('fonts/microgrammanormal.ttf');

            $pdf = SnappyPdf::setOptions([
                'margin-top' => 0,
                'margin-left' => 0,
                'margin-right' => 0,
                'margin-bottom' => 0,
                'page-width' => "261px",
                'page-height' => "80px",
                'enable-local-file-access' => true,
            ])->loadHTML($html);

            return $pdf->download('label'.$id.'.pdf');

        } catch (\Exception $e) {
            dd($e);
            Toastr::error('Error Generating Label', 'Error');
            return back();
        }
    }


    public function scanAndSave(Request $request, Order $order)
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
                if ($order->cards->where('is_no_grade', 0)->count() == $order->cards->whereNotNull('front_page')->whereNotNull('back_page')->count())
                {
                    if($order->status != 30) {
    
                        $status = config('static_array.status');
                        $setting = getSetting();
                        $body = "The status of your order {$order->order_number} has been updated.";
                        $msg = 'We have finished slabbing your cards and will update you once your order is ready to be shipped! At that point, you can log in and pay for your order!';
            
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
            dd($e);
            Toastr::error('Error uploading image', 'Error');
            return back();
        }

        Toastr::success('Image uploaded successfully', 'Success');
        return back();

    }

    // public function makePDF(Request $request){
    //     if($request->image){
    //         $pdf = PDF::loadHTML('<img src="'.$request->image.'" />')->save(public_path('test.pdf'));

    //         // return $pdf;

    //         return response(
    //             [
    //                 'success' => true,
    //                 'pdf' => asset('test.pdf')
    //             ]
    //         );
    //     }else{
    //         return response(
    //             [
    //                 'success' => false,
    //                 'message' => 'Image Not Found'
    //             ]
    //         );
    //     }
    // }

    public function cardStore(Request $request)
    {
        
        $request->validate([
            'order_id' => 'required',
            'year' => 'required',
            'brand' => 'required|string|max:255',
            'cardNumber' => 'required',
            'playerName' => 'required|string|max:255',
            'notes' => 'nullable|string|max:255',
            // 'centering' => 'required_without:noGrade|nullable|numeric|min:1|max:10',
            // 'corners' => 'required_without:noGrade|nullable|numeric|min:1|max:10',
            // 'edges' => 'required_without:noGrade|nullable|numeric|min:1|max:10',
            // 'surface' => 'required_without:noGrade|nullable|numeric|min:1|max:10',
            // 'reason' => 'nullable|string|max:512',
        ]);
    
        $setting = getSetting();
    
        DB::beginTransaction();
    
        try {
            $order = Order::where('id', $request->order_id)->first();
            if (!$order) {
                throw new \Exception('Order not found');
            }
    
            $unit_price = $order->unit_price;
            $order->total_order_qty += 1;
    
            $gst = $setting->gst_tax * $unit_price / 100;
            $pst = $setting->pst_tax * $unit_price / 100;
            $total_tax = $gst + $pst;
    
            $order->extra_price += $total_tax;
            $order->total_price += ($unit_price + $total_tax);
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
            $orderDetails->line_price = 1 * $unit_price;
            $orderDetails->item_name = "{$request->year} {$request->brand} {$request->cardNumber} {$request->playerName}";
            $orderDetails->save();
    
            // $cardnumber = $request->certNo;
            // if ($cardnumber) {
            //     $postfix = substr($cardnumber, -4);
            //     $random = (int)substr($cardnumber, -5, 1);
            //     $prefix = (int)str($cardnumber)->before($random . $postfix)->value();
            // } else {
            //     $postfix = 0;
            //     $random = 0;
            //     $prefix = 0;
            // }
    
            // $card = new OrderCard();
            // $card->order_id = $order->id;
            // $card->order_details_id = $orderDetails->id;
            // $card->centering = $request->noGrade ? 0 : $request->centering;
            // $card->corners = $request->noGrade ? 0 : $request->corners;
            // $card->edges = $request->noGrade ? 0 : $request->edges;
            // $card->surface = $request->noGrade ? 0 : $request->surface;
            // $card->final_grading = $request->noGrade ? 0 : calculateFinalGrade([
            //     $request->centering,
            //     $request->corners,
            //     $request->edges,
            //     $request->surface,
            // ]);
            // $card->is_no_grade = $request->noGrade ? 1 : 0;
            // $card->no_grade_reason = $request->reason;
            // $card->card_number = $request->noGrade ? null : $request->certNo;
            // $card->prefix = $prefix;
            // $card->rand_num = $random;
            // $card->postfix = $postfix;
            // $card->save();

        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();
            toastr()->error('An unexpected error occured while creating card');
            return back();
        }

        DB::commit();
        toastr()->success('New card created successfully');
        return back();
    }
}
