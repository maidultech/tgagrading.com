<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\States;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class StateController extends Controller
{

    public function index()
    {
     
        $states = States::with('country')
            ->orderByRaw("FIELD(country_id, 1, 2)") // Canada (1) first, then USA (1)
            ->orderBy('name') // Then sort by state name
            ->get();
        $countries = Country::all();

        $stateTitle= 'states';


        //dd($states);

        return view('admin.states.index', compact('states', 'countries','stateTitle'));

    }
    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:country,id',
            'name' => 'required|string|max:255',
            'gst' => 'nullable|numeric|between:0,100',
            'pst' => 'nullable|numeric|between:0,100',
            'status' => 'required|boolean',
        ]);

        // Create and save the new state
        $state = new States();
        $state->country_id = $request->country_id;
        $state->name = $request->name;
        $state->gst = $request->gst ?? 0;
        $state->pst = $request->pst ?? 0;

        $state->status = $request->status;
        $state->save();
        Toastr::success(trans('Category states Successfully!'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.state.index');



    }

    public function edit($id)
    {
        $state = states::find($id);
        // dd($blog_category);
        $html = view('admin.states.edit', compact('state'))->render();
        return response()->json($html);
    }

    public function update(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'country_id' => 'required|exists:country,id',
            'name' => 'required|string|max:255',
            'gst' => 'nullable|numeric|between:0,100',
            'pst' => 'nullable|numeric|between:0,100',
            'status' => 'required|in:0,1',
        ]);

        // Find the state and update it
        $state = States::findOrFail($id);
        $state->country_id = $request->country_id;
        $state->name = $request->name;
        $state->gst = $request->gst ?? 0;
        $state->pst = $request->pst ?? 0;
        $state->status = $request->status;
        $state->save();
        Toastr::success(trans('states upadet Successfully!'), 'Success', ["positionClass" => "toast-top-center"]);

        return redirect()->route('admin.state.index');

    }

    public function delete($id)
    {
        // Find the state by ID
        $state = States::findOrFail($id);

        // Delete the state
        $state->delete();
        Toastr::success(trans('states delete Successfully!'), 'Success', ["positionClass" => "toast-top-center"]);

        return redirect()->route('admin.state.index');



    }

}
