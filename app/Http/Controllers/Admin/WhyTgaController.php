<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhyTga;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WhyTgaController extends Controller
{

    public $user;
    protected $whyTga;
    public function __construct(WhyTga $whyTga)
    {
        $this->bcat = $whyTga;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index()
    {

        $data['title'] = 'Why Tga';
        $data['rows'] = WhyTga::orderBy('order_id', 'asc')->get();
        return view('admin.why_tga.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:why_tga,title|max:100',
            'status' => 'required'
        ]);

        DB::beginTransaction();
        try {

            $why_tga = new WhyTga();

            $why_tga->title = $request->title;
            $why_tga->destails = $request->destails;
            $why_tga->order_id = WhyTga::max('order_id') + 1;
            $why_tga->status = $request->status;
            $why_tga->save();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error(trans('Post Why Tga not Created !'), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.why-tga.index');
        }
        DB::commit();
        Toastr::success(trans('Why Tga Added Successfully!'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.why-tga.index');
    }
    public function edit($id)
    {

        $why_tga = WhyTga::find($id);
        // dd($why_tga);
        $html = view('admin.why_tga.edit', compact('why_tga'))->render();
        return response()->json($html);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:100|unique:why_tga,title,' . $id,
            'order_id' => 'required|unique:why_tga,order_id,' . $id,
            'status' => 'required'
        ]);

        DB::beginTransaction();
        try {

            $why_tga = WhyTga::find($id);
            $why_tga->title = $request->title;
            $why_tga->destails = $request->destails;
            $why_tga->order_id = $request->order_id;
            $why_tga->status = $request->status;
            $why_tga->save();
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            Toastr::error(trans('Why Tga not Updated !'), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.why-tga.index');
        }
        DB::commit();
        Toastr::success(trans('Why Tga Updated Successfully !'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.why-tga.index');
    }

    public function delete($id)
    {

        DB::beginTransaction();
        try {
            $why_tga = WhyTga::find($id);
            $why_tga->delete();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error(trans('Why Tga not Deleted !'), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.why-tga.index');
        }
        DB::commit();
        Toastr::success(trans('Why Tga Deleted Successfully !'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.why-tga.index');
    }

}
