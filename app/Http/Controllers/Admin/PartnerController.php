<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::all();
        return view('admin.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('admin.partners.create');
    }

    public function store(Request $request)
    {
        // validate the request
        $request->validate([
            'title' => 'required',
            'image' => 'required|image|max:2048',
        ]);

        // store the partner
        $partner = new Partner();
        $partner->title = $request->title;
        $partner->description = $request->description;
        $partner->image = uploadGeneralImage($request->file('image'), 'partners');
        $partner->save();
        Toastr::success('Partner created successfully');

        return redirect()->route('admin.partner.index');
    }

    public function edit($id)
    {
        // if (!Auth::user()->can('admin.partner.edit')) {
        //     abort(403, 'You are not authorized to perform this action');
        // }
        $partner = Partner::find($id);
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(Request $request, $id)
    {
        // validate the request
        $request->validate([
            'title' => 'required',
            'image' => 'image|max:2048',
        ]);

        // store the partner
        $partner = Partner::find($id);
        $partner->title = $request->title;
        $partner->description = $request->description;
        $partner->status = $request->status;
        if ($request->hasFile('image')) {
            $partner->image = uploadGeneralImage($request->file('image'), 'partners', $partner->image);
        }
        $partner->save();
        Toastr::success('Partner updated successfully');
        return redirect()->route('admin.partner.index');
    }
}
