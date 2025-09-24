<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessPartner;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;


class BusinessPartnerController extends Controller
{
     public function index(){
        $bussiPartners = BusinessPartner::orderBy('order_id')->get(); 
        return view('admin.business_partner.index',compact('bussiPartners')); 
     }

     public function create(){
        return view('admin.business_partner.create'); 
     }


     public function store(Request $request){
        $request->validate([
            'company_name' => 'required',
            'company_url' => 'required',
            'logo' => 'required|image|max:2048',
        ]);

        // store the partner
        $bussiPartner = new BusinessPartner();
        $bussiPartner->company_name = $request->company_name;
        $bussiPartner->company_url = $request->company_url;
        $bussiPartner->details = $request->details;
        $bussiPartner->status = $request->status;
        $bussiPartner->order_id = BusinessPartner::max('order_id') ? BusinessPartner::max('order_id') + 1 : 1;
        $bussiPartner->logo = uploadGeneralImage($request->file('logo'), 'business-partner');
        $bussiPartner->save();

        Toastr::success('Partner created successfully');
        return redirect()->route('admin.business-partner.index');
     }


     public function edit($id){
        $partner= BusinessPartner::findOrFail($id);  
        return view('admin.business_partner.edit',compact('partner')); 
     }

     public function update(Request $request,$id){
      $bussiPartner =  BusinessPartner::findOrFail($id);
      $logoRule = $bussiPartner->logo ? 'nullable|image|max:2048' : 'required|image|max:2048';
      $request->validate([
            'company_name' => 'required|string',
            'company_url' => 'required',
            'logo' => $logoRule, 
        ]);

      //   $bussiPartner =  BusinessPartner::findOrFail($id);
        $bussiPartner->company_name = $request->company_name;
        $bussiPartner->company_url = $request->company_url;
        $bussiPartner->details = $request->details;
        $bussiPartner->status = $request->status;
        $bussiPartner->order_id = $request->order_id;
      //    if (is_null($bussiPartner->order_id)) {
      //       $bussiPartner->order_id = BusinessPartner::max('order_id') ? BusinessPartner::max('order_id') + 1 : 1;
      //   }
         if ($request->hasFile('logo')) {
            if ($bussiPartner->logo && file_exists(public_path($bussiPartner->logo))) {
                unlink(public_path($bussiPartner->logo));
            }
            $bussiPartner->logo = uploadGeneralImage($request->file('logo'), 'business-partner');
        }
        $bussiPartner->save();

        Toastr::success('Partner updated  successfully');
        return redirect()->route('admin.business-partner.index');
     }

       public function delete($id){
        $bussiPartner= BusinessPartner::findOrFail($id);  
        
            if ($bussiPartner->logo && file_exists(public_path($bussiPartner->logo))) {
                unlink(public_path($bussiPartner->logo));
            }
            $bussiPartner->delete(); 

        Toastr::success('Partner delete  successfully');
        return redirect()->route('admin.business-partner.index');
       
     }

}
