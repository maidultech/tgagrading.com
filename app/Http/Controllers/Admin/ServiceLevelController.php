<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\ServiceLevel;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceLevelController extends Controller
{
    protected $serviceLevel;
    public $user;

    public function __construct(ServiceLevel $serviceLevel)
    {
        $this->serviceLevel     = $serviceLevel;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }
    
    public  function index()
    {
        // if (is_null($this->user) || !$this->user->can('admin.service-level.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = 'Manage Service Level';
        $data['plan'] = ServiceLevel::orderBy('order_id', 'asc')->get();
        return view('admin.service-level.index', $data);
    }
    public  function create()
    {
        // if (is_null($this->user) || !$this->user->can('admin.service-level.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = 'Create New Service Level';
        $data['currency'] = Currency::get();
        return view('admin.service-level.create', $data);
    }

    public  function store(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.service-level.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $request->validate([
            'name' => 'required|string|max:255|unique:service_levels,name',
            'estimated_days' => 'required|integer|min:1',
            'extra_price' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'order_id' => 'nullable|integer|min:1|unique:service_levels,order_id',
        ]);

        DB::beginTransaction();
        try {
            $plan = new ServiceLevel();
            if(!$this->saveServiceLevel($request, $plan)){
                DB::rollback();
                Toastr::error('Failed to add Service Level', 'Error', ["positionClass" => "toast-top-center"]);
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            Toastr::error('Failed to add Service Level', 'Error', ["positionClass" => "toast-top-center"]);
            return back()->withInput();
        }
        DB::commit();
        Toastr::success('Service Level Added Successfully', 'Error', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.service.level.index');
    }

    public  function edit($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.service-level.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = 'Edit Service Level';;
        $data['row'] = ServiceLevel::find($id);
        $data['currency'] = Currency::get();
        return view('admin.service-level.edit', $data);
    }

    public  function update($id, Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.service-level.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $request->validate([
            'name' => 'required|string|max:255|unique:service_levels,name,'.$id,
            'estimated_days' => 'required|integer|min:1',
            'extra_price' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'order_id' => 'nullable|integer|min:1|unique:service_levels,order_id,'.$id,
        ]);

        DB::beginTransaction();
        try {
            $plan = ServiceLevel::findOrFail($id);
            if(!$this->saveServiceLevel($request, $plan)){
                DB::rollback();
                Toastr::error('Failed to edit Service Level', 'Error', ["positionClass" => "toast-top-center"]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Failed to edit Service Level', 'Error', ["positionClass" => "toast-top-center"]);
            return back()->withInput();
        }
        DB::commit();
        Toastr::success('Service Level Edited Successfully', 'Error', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.service.level.index');
    }

    public function delete($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.service-level.delete')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        DB::beginTransaction();
        try {
            $plan = ServiceLevel::find($id);
            $plan->delete();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Failed to delete Service Level', 'Error', ["positionClass" => "toast-top-right"]);
            return redirect()->route('admin.service.level.index');
        }
        DB::commit();
        Toastr::success('Service level delete successfully', 'Error', ["positionClass" => "toast-top-right"]);
        return redirect()->route('admin.service.level.index');
    }

    function saveServiceLevel(Request $request, ServiceLevel $serviceLevel){
        $serviceLevel->name = $request->name;
        $serviceLevel->estimated_days = $request->estimated_days;
        // $serviceLevel->max_declare_value = $request->max_declare_value;
        $serviceLevel->extra_price = $request->extra_price;
        $serviceLevel->status = $request->status;
        $serviceLevel->order_id = $request->order_id ?? ServiceLevel::max('order_id')+1;
        return $serviceLevel->save();
    }


}
