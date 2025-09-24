<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemBrand;
use App\Models\Category;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemBrandController extends Controller
{

    public $user;
    protected $brand;
    public function __construct(ItemBrand $brand)
    {
        $this->brand   = $brand;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index(Request $request) 
    {
        // if (is_null($this->user) || !$this->user->can('admin.brand.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['rows'] = ItemBrand::orderBy('name','asc')->get();
        return view('admin.item_brand.index', $data);
    }

    public function create()
    {
        // if (is_null($this->user) || !$this->user->can('admin.brand.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        return view('admin.item_brand.create');
    }

    public function store(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.brand.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $request->validate([
            'name' => 'required|max:155|unique:item_brands,name',
        ]);

        DB::beginTransaction();
        try {
            $brand                     = new ItemBrand();
            $brand->name               = $request->name;
            $brand->status             = $request->status;
            $brand->order_id           = ItemBrand::max('order_id') ? ItemBrand::max('order_id') + 1 : 1;
            $brand->save();
        } 
        catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Failed to create brand', 'Error', ["positionClass" => "toast-top-right"]);
            return back();
        }
        DB::commit();
        Toastr::success('Brand created successfully', 'Success', ["positionClass" => "toast-top-right"]);
        return redirect()->route('admin.item-brand.index');
    }

 
    public function edit(Request $request, $id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.brand.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['row'] = ItemBrand::findOrFail($id);
        return view('admin.item_brand.edit', $data);
    }
 
    public function update(Request $request, $id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.brand.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $request->validate([
            'name' => 'required|max:155|unique:item_brands,name,'. $id,
            'order_id' => 'required|integer|unique:item_brands,order_id,'. $id,
        ]);

        DB::beginTransaction();
        try {
            $brand                     = ItemBrand::findOrFail($id);

            $brand->name               = $request->name;
            $brand->status             = $request->status;
            $brand->order_id           = $request->order_id;

            $brand->update();
        } 
        catch (\Exception $e) {
            DB::rollback();
            // dd($e);
            Toastr::error('Failed to update brand', 'Error', ["positionClass" => "toast-top-right"]);
            return back();
        }
        DB::commit();
        Toastr::success('Brand Update successfully', 'Success', ["positionClass" => "toast-top-right"]);
        return redirect()->route('admin.item-brand.index');
    }

    public function delete($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.brand.delete')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $brand = ItemBrand::findOrFail($id);
        $brand->delete();

        Toastr::success("Brand Delete Successfully", 'Success', ["positionClass" => "toast-top-right"]);
        return redirect()->back();
    }
}
        

    

