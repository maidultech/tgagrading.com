<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinalGrading;
use App\Models\ManualLabel;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManualLabelController extends Controller
{
    public function index(Request $request)
    {

        $data['title'] = "Manual Label List";
        $data['rows'] = ManualLabel::get();
        return view('admin.manual_label.index', $data);
    }

    public function create()
    {
        $data['title'] = "Create New Manual Label";
        return view('admin.manual_label.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'years' => 'required|max:4',
            'brand_name' => 'required|max:20',
            'card' => 'required|max:10',
            'card_name' => 'required|max:20',
            // 'item_name' => 'required|max:500',
            'card_number' => 'required|max:10|lte:2147483647|unique:manual_labels,card_number',
            // 'grade' => 'required|numeric',
            // 'grade_name' => 'required|string|max:500',
            'qr_link' => 'required|url|max:500',
            'surface' => $request->is_authentic_check ? 'nullable|numeric' : 'required|numeric',
            'centering' => $request->is_authentic_check ? 'nullable|numeric' : 'required|numeric',
            'corners' => $request->is_authentic_check ? 'nullable|numeric' : 'required|numeric',
            'edges' => $request->is_authentic_check ? 'nullable|numeric' : 'required|numeric',
            'notes' => 'nullable|max:25', 
            'info' => 'nullable|max:100',
        ]);

        // Start DB transaction
        DB::beginTransaction();
        try {

            $manual_label = new ManualLabel();

            $manual_label->year = $request->years;
            $manual_label->brand_name = $request->brand_name;
            $manual_label->card = $request->card;
            $manual_label->card_name = $request->card_name;
            // $manual_label->item_name = $request->item_name;
            $manual_label->item_name = $request->years . ' ' . $request->brand_name . ' ' . $request->card . ' ' . $request->card_name;
            $manual_label->card_number = $request->card_number;
            // $manual_label->grade = $request->grade;
            // $manual_label->grade_name = $request->grade_name;

            if($request->is_authentic_check == 1) {
                $manual_label->grade = 'A';
                $manual_label->grade_name = 'AUTHENTIC';
                $manual_label->is_authentic = 1;
            } else {
                $final_grading = calculateFinalGrade([$request->centering, $request->corners, $request->edges, $request->surface]);
                $manual_label->grade = $final_grading;
                if($request->centering == '10' && $request->corners == '10' &&  $request->edges == '10' && $request->surface == '10' && $final_grading == '10'){
                    $grad = DB::table('finalgrading_name')->where('id',21)->first();
                }else{
                    $grad = DB::table('finalgrading_name')->where('finalgrade',$final_grading)->first();
                }
                $manual_label->grade_name = $grad->name ?? '';
                $manual_label->is_authentic = 0;
            }

            $manual_label->qr_link = $request->qr_link;
            $manual_label->surface = $request->surface ?? 0;
            $manual_label->centering = $request->centering ?? 0;
            $manual_label->corners = $request->corners ?? 0;
            $manual_label->edges = $request->edges ?? 0;
            $manual_label->notes = $request->notes;
            $manual_label->created_by = auth('admin')->id();  // Assuming admin auth

            $manual_label->save();

        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollback();
            Toastr::error('Manual Label creation error: ' . $e->getMessage(), 'Error', ["positionClass" => "toast-top-center"]);
            return back();
        }

        // Commit the transaction if everything went well
        DB::commit();
        Toastr::success('Manual Label created successfully', 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.manual-label.index');
    }

    public function edit($id)
    {

        $data['title'] = "Edit Manual Label";
        $data['row'] = ManualLabel::find($id);
        return view('admin.manual_label.edit', $data);
    }

    public function download($id)
    {

        $data['title'] = "Download Manual Label";
       

         try {
            $finalGradings = FinalGrading::get(['name', 'finalgrade'])->groupBy('finalgrade')->map(function ($items) {
                return $items->pluck('name')->toArray();
            });
            $cert = ManualLabel::where('id',$id)->first();

            if(collect([$cert->centering, $cert->corners, $cert->edges, $cert->surface])->sum()==40){
                $html = view('admin.order.print-label.label', [
                    'cert' => $cert,
                    'finalGradings' => $finalGradings,
                    'isManual' => true
                    ])->render();
            }else{
                $html = view('admin.order.print-label.label_white', [
                    'cert' => $cert,
                    'finalGradings' => $finalGradings,
                    'isManual' => true
                    ])->render();
            }
            // return $html;
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

    
    public function update(Request $request, $id)
    {
        $request->validate([
            'years' => 'required|max:4',
            'brand_name' => 'required|max:20',
            'card' => 'required|max:10',
            'card_name' => 'required|max:20',
            // 'item_name' => 'required|max:500', 
            'card_number' => 'required|max:10|lte:2147483647|unique:manual_labels,card_number,' . $id,
            // 'grade' => 'required|numeric',
            // 'grade_name' => 'required|max:500',
            'qr_link' => 'required|url|max:500',
            'surface' => $request->is_authentic_check ? 'nullable|numeric' : 'required|numeric',
            'centering' => $request->is_authentic_check ? 'nullable|numeric' : 'required|numeric',
            'corners' => $request->is_authentic_check ? 'nullable|numeric' : 'required|numeric',
            'edges' => $request->is_authentic_check ? 'nullable|numeric' : 'required|numeric',
            'notes' => 'nullable|max:25',
            'info' => 'nullable|max:100',
        ]);

        DB::beginTransaction();
        try {
            
            $manual_label = ManualLabel::findOrFail($id);
            $manual_label->year = $request->years;
            $manual_label->brand_name = $request->brand_name;
            $manual_label->card = $request->card;
            $manual_label->card_name = $request->card_name;
            // $manual_label->item_name = $request->item_name;
            $manual_label->item_name = $request->years . ' ' . $request->brand_name . ' ' . $request->card . ' ' . $request->card_name;  // Concatenate item_name
            $manual_label->card_number = $request->card_number;
            // $manual_label->grade = $request->grade;
            // $manual_label->grade_name = $request->grade_name;

            if($request->is_authentic_check == 1) {
                $manual_label->grade = 'A';
                $manual_label->grade_name = 'AUTHENTIC';
                $manual_label->is_authentic = 1;
            } else {
                $final_grading = calculateFinalGrade([$request->centering, $request->corners, $request->edges, $request->surface]);
                $manual_label->grade = $final_grading;
                if($request->centering == '10' && $request->corners == '10' &&  $request->edges == '10' && $request->surface == '10' && $final_grading == '10'){
                    $grad = DB::table('finalgrading_name')->where('id',21)->first();
                }else{
                    $grad = DB::table('finalgrading_name')->where('finalgrade',$final_grading)->first();
                }
                $manual_label->grade_name = $grad->name ?? '';
                $manual_label->is_authentic = 0;
            }

            $manual_label->card_number = $request->card_number;
            $manual_label->qr_link = $request->qr_link;
            $manual_label->surface = $request->surface ?? 0;
            $manual_label->centering = $request->centering ?? 0;
            $manual_label->corners = $request->corners ?? 0;
            $manual_label->edges = $request->edges ?? 0;
            $manual_label->notes = $request->notes;
            $manual_label->updated_by = auth('admin')->id();  // Assuming the logged-in user is an admin

            $manual_label->save();


        } catch (\Exception $e) {
            // If something goes wrong, rollback the transaction
            DB::rollback();
            Toastr::error('Manual Label update error: ' . $e->getMessage(), 'Error', ["positionClass" => "toast-top-center"]);
            return back();
        }

        // Commit the transaction
        DB::commit();
        Toastr::success('Manual Label updated successfully', 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.manual-label.index');
    }



    public function view($id)
    {

        $data['title'] = 'Manual Label View List';
        $data['row'] = ManualLabel::find($id);

        return view('admin.manual_label.view', $data);
    }


    public function delete($id)
    {

        $manual_label = ManualLabel::findOrFail($id);
        $manual_label->delete();
        Toastr::success('Manual Label deleted successfully', 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->back();
    }

    public function manualScanAndSave(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp',
        ]);

        try {

            $manual_label = ManualLabel::findOrFail($request->card_id);
            $imagePath = uploadGeneralImage($request->image, 'card');

            if ($request->page_type === 'front_page') {
                $manual_label->front_page = $imagePath;
            } elseif ($request->page_type === 'back_page') {
                $manual_label->back_page = $imagePath;
            }

            $manual_label->save();

        } catch (\Exception $e) {
            dd($e);
            Toastr::error('Error uploading image', 'Error');
            return back();
        }

        Toastr::success('Image uploaded successfully', 'Success');
        return back();
    }
}
