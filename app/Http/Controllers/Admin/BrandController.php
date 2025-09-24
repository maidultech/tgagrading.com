<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Category;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{

    public $user;
    protected $brand;
    public function __construct(Brand $brand)
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

        $data['rows'] = Brand::orderBy('order_id','asc')->get();
        return view('admin.brand.index', $data);
    }

    public function create()
    {
        // if (is_null($this->user) || !$this->user->can('admin.brand.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        return view('admin.brand.create');
    }

    public function store(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.brand.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $request->validate([
            'name' => 'required|max:155|unique:brands,name',
            'image' => 'required|max:256|image|mimes:png,jpg,jpeg,webp',
        ]);

        DB::beginTransaction();
        try {
            $brand                     = new Brand();

            $brand->name               = $request->name;
            $brand->link               = $request->link;
            $brand->status             = $request->status;
            $brand->order_id           = Brand::max('order_id') ? Brand::max('order_id') + 1 : 1;

            if( $request->hasFile('image') ){
                $images = $request->file('image');
    
                $imageName          =  rand(1, 99999999) . '.' . $images->getClientOriginalExtension();
                $imagePath          = 'assets/uploads/brand/';
                $images->move($imagePath, $imageName);
    
                $brand->image        =  $imagePath . $imageName;
            }

            $brand->save();
        } 
        catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Failed to create brand', 'Error', ["positionClass" => "toast-top-right"]);
            return back();
        }
        DB::commit();
        Toastr::success('Brand created successfully', 'Success', ["positionClass" => "toast-top-right"]);
        return redirect()->route('admin.brand.index');
    }

 
    public function edit(Request $request, $id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.brand.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['row'] = Brand::findOrFail($id);
        return view('admin.brand.edit', $data);
    }
 
    public function update(Request $request, $id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.brand.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $request->validate([
            'name' => 'required|max:155|unique:brands,name,'. $id,
            'image' => 'max:256|image|mimes:png,jpg,jpeg,webp',
            'order_id' => 'required|integer|unique:brands,order_id,'. $id,
        ]);

        DB::beginTransaction();
        try {
            $brand                     = Brand::findOrFail($id);

            $brand->name               = $request->name;
            $brand->status             = $request->status;
            $brand->link               = $request->link;
            $brand->order_id           = $request->order_id;

            if( $request->hasFile('image') ){
                $images = $request->file('image');

                if( !empty($brand->image) && file_exists($brand->image) ){
                     @unlink($brand->image);
                }
    
                $imageName          =  rand(1, 99999999) . '.' . $images->getClientOriginalExtension();
                $imagePath          = 'assets/uploads/brand/';
                $images->move($imagePath, $imageName);
    
                $brand->image        =  $imagePath . $imageName;
            }

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
        return redirect()->route('admin.brand.index');
    }

    public function delete($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.brand.delete')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $brand = Brand::findOrFail($id);
        $brand->delete();

        Toastr::success("Brand Delete Successfully", 'Success', ["positionClass" => "toast-top-right"]);
        return redirect()->back();
    }
}
        

    

