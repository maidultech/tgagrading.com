<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CertificateVerification;
use App\Models\FinalGrading;
use App\Models\OrderCard;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = CertificateVerification::all();
     
        return view('admin.certificates.index', compact('certificates'));
    }

    public function create()
    {
        return view('admin.certificates.create');
    }

    public function store(Request $request)
    {
        $id = $request->input('id');
        $request->validate([
            'certification_number' => 'required|unique:certificate_verifications,certification_number,' . $id,
        ]);
        try {
            $certificateVerification = CertificateVerification::find($id ?? 0);
            if (!$certificateVerification) {
                $certificateVerification = new CertificateVerification();
            }
            /*$certificateVerification->starting_text = $request->input('starting_text');
            $certificateVerification->title = $request->input('title');*/
            $certificateVerification->certification_number = $request->input('certification_number');
            $certificateVerification->year = $request->input('year');
            $certificateVerification->brand = $request->input('brand');
            $certificateVerification->sport = $request->input('sport');
            $certificateVerification->card_number = $request->input('card_number');
            $certificateVerification->player = $request->input('player');
            $certificateVerification->variety = $request->input('variety');
            $certificateVerification->grade = $request->input('grade');
            // $certificateVerification->status = $request->input('status') ?? 1;


            // Save the model instance to the database
            $certificateVerification->save();

            // Return a success message
            Toastr::success('Certificate verification stored successfully!', 'Success');
            return redirect()->route('admin.certificate.index');
        } catch (Exception $e) {
            dd($e);
            // Catch any exceptions and return an error message
            Toastr::error('An error occurred while storing the certificate verification!', 'Error');
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $certificate_verification = CertificateVerification::find($id);
        return view('admin.certificates.create', compact('certificate_verification'));
    }


    // public function delete($id)
    // {
    //     try {
    //         $certificate_verification = CertificateVerification::find($id);
    //         $certificate_verification->delete();
    //         Toastr::success('Certificate verification deleted successfully!', 'Success');
    //     } catch (Exception $e) {
    //         Toastr::error('An error occurred while deleting the certificate verification!', 'Error');
    //     }
    //     return redirect()->back();
    // }
    
}
