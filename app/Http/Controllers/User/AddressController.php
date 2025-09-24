<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'required',
            'apt_unit' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'country' => 'required',
            'state' => 'required',
        ]);

        Address::create([
            'user_id' => auth()->id(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'street' => $request->street,
            'apt_unit' => $request->apt_unit,
            'city' => $request->city,
            'zip_code' => $request->zip_code,
            'country' => $request->country,
            'state' => $request->state,
        ]);

        Toastr::success('Address added successfully');
        return redirect()->back();
    }

    public function edit($id)
    {
        $address = Address::find($id);
        $html = view('user.address-book-form', compact('address'))->render();
        return response()->json(['html' => $html]);

    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'required',
            'apt_unit' => 'nullable',
            'city' => 'required',
            'zip_code' => 'required',
            'country' => 'required',
            'state' => 'required',
        ]);

        $address = Address::findOrFail($id);
        $address->update($request->all());
        Toastr::success('Address updated successfully');
        return redirect()->back();
    }

    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        $address->delete();

        Toastr::success('Address deleted successfully');
        return redirect()->back();
    }

    public function setDefault($id)
    {
        Address::where('user_id', auth()->id())->update(['is_default' => false]);
        $address = Address::findOrFail($id);
        $address->is_default = true;
        $address->save();

        Toastr::success('Address set as default successfully');
        return redirect()->back();
    }
}
