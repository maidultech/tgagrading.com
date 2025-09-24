<?php

namespace App\Http\Controllers\Admin;

use App\Models\GradingScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class GradingScaleController extends Controller
{
    protected $gradingScale;
    public $user;

    public function __construct(GradingScale $gradingScale)
    {
        $this->gradingScale     = $gradingScale;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index(Request $request)
    {

        // if (is_null($this->user) || !$this->user->can('admin.faq.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = "Grading Scale List";
        $data['rows'] = GradingScale::orderBy('order_id', 'asc')->get();
        return view('admin.grading_scale.index', $data);
    }

    public function create()
    {
        // if (is_null($this->user) || !$this->user->can('admin.faq.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }
        $data['title'] = "Create New Grading Scale";
        return view('admin.grading_scale.create', $data);
    }

    public function store(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.faq.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $request->validate([
            'title' => 'required|string|max:255|unique:grading_scale,title',
            'body' => 'required|max:1024',
        ]);

        DB::beginTransaction();
        try {
            $grading_scale                   = new GradingScale();

            $grading_scale->title            = $request->title;
            $grading_scale->body             = $request->body;
            $grading_scale->order_id         = GradingScale::max('order_id') ? GradingScale::max('order_id') + 1 : 1;
            $grading_scale->created_by       = Auth::user()->id;
            $grading_scale->update_by        = Auth::user()->id;
            $grading_scale->created_at       = date('Y-m-d H:i:s');
            $grading_scale->updated_at       = date('Y-m-d H:i:s');
            // $grading_scale->is_active        = $request->is_active;

            $grading_scale->save();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Grading scale created error', 'Error', ["positionClass" => "toast-top-center"]);
            return back();
        }
        DB::commit();
        Toastr::success('Grading scale created successfully', 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.grading-scale.index');
    }


    public function edit($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.faq.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = "Edit Grading Scale";
        $data['row'] = GradingScale::find($id);
        return view('admin.grading_scale.edit', $data);
    }

    public function update(Request $request, $id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.faq.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $request->validate([
            'title' => 'required|string|max:255|unique:grading_scale,title,'. $id,
            'body' => 'required|max:1024',
            'order_id' => 'integer|unique:grading_scale,order_id,'. $id,
        ]);

        DB::beginTransaction();
        try {
            $grading_scale                   = GradingScale::findOrFail($id);

            $grading_scale->title            = $request->title;
            $grading_scale->body             = $request->body;
            $grading_scale->order_id         = $request->order_id;
            $grading_scale->update_by        = Auth::user()->id;
            $grading_scale->updated_at       = date('Y-m-d H:i:s');
            // $grading_scale->is_active        = $request->is_active;

            $grading_scale->update();
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            Toastr::error('Grading scale updated error', 'Error', ["positionClass" => "toast-top-center"]);
            return back();
        }
        DB::commit();
        Toastr::success('Grading scale updated successfully', 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.grading-scale.index');
    }


    public function view($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.faq.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = 'Grading Scale View List';
        $data['row'] = GradingScale::find($id);

        return view('admin.grading_scale.view', $data);
    }


    public function delete($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.faq.delete')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $grading_scale = GradingScale::findOrFail($id);
        $grading_scale->delete();
        Toastr::success('Grading scale deleted successfully', 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->back();
    }
}