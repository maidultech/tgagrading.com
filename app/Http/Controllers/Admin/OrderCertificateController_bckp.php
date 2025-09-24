<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinalGrading;
use App\Models\Order;
use App\Models\OrderCard;
use Barryvdh\DomPDF\Facade\PDF;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        // if (is_null($this->user) || !$this->user->can('admin.order.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = 'Manage Card Certificate';
        abort_if(!$order, 404);
        $data['order'] = $order->load('details.cards');

        $data['finalGradings'] = FinalGrading::get(['name','finalgrade'])->pluck('name','finalgrade');

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

                    $cardCheck = OrderCard::where('card_number',$card_number)->first();

                    if ( !is_null($card_number) && !is_null($cardCheck) ) {
                        if ( $cardCheck->order_id != $order->id ) {
                            toastr()->error('Duplicate Card Certification Number Found');
                            return back();
                        } elseif ( $cardCheck->order_details_id == $details_id ) {
                            $card = $cardCheck;
                        }

                    } else {
                        $card = new OrderCard();
                    }

                    $card->order_id = $order->id;
                    $card->order_details_id = $details_id;
                    $card->is_no_grade = isset($request->cert_no_grade[$details_id][$key]) ? 1 : 0;

                    if($card->is_no_grade==0){
                        $card->card_number = $card_number;
                        $card->centering = $request->centering[$details_id][$key];
                        $card->corners = $request->corners[$details_id][$key];
                        $card->edges = $request->edges[$details_id][$key];
                        $card->surface = $request->surface[$details_id][$key];
                        $card->final_grading = calculateFinalGrade([
                            $card->centering,
                            $card->corners,
                            $card->edges,
                            $card->surface,
                        ]);
                    }else{
                        $card->card_number = null;
                        $card->final_grading = 0;
                    }
                    $card->no_grade_reason = $request->cert_no_grade_reason[$details_id][$key] ?? null;
                    $card->prefix = $cardInfo['prefix'];
                    $card->rand_num = $cardInfo['random'];
                    $card->postfix = $cardInfo['postfix'];
                    $card->save();
                }
            }

        } catch (\Throwable $th) {
                DB::rollBack();
                dd($th);
                toastr()->error('Card Certification Number Not Updated');
                return back();
        }

       DB::commit();
       toastr()->success('Card Certification Number Updated');
       return back();



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
            // dd($certs);

            if (!$certs || $certs->isEmpty()) {
                Toastr::success('Certificate with grade not found', 'Error');
                return back();
            }

            // if ($certs->where('is_no_grade', 1)->count() > 0) {
            //     Toastr::error('No Grade Card Cannot Be Printed', 'Error');
            //     return back();
            // }

            $html = view('admin.order.print-label.label', [
                'certs' => $certs,
            ])->render();

            // Check if HTML output is requested
            if ($request->has('h')) {
                return response()->json([
                    'success' => true,
                    'data' => $html,
                ]);
            }

            // Generate PDF
            $pdf = SnappyPdf::setOptions([
                'margin-top' => 0,
                'margin-left' => 0,
                'margin-right' => 0,
                'margin-bottom' => 0,
                'page-width' => "69",
                'page-height' => "21",
            ])->loadHTML($html);

            return $pdf->download('label'.$id.'.pdf');

        } catch (\Exception $e) {
            dd($e);
            Toastr::error('Error Generating Label', 'Error');
            return back();
        }
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
}
