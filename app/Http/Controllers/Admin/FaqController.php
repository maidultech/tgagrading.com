<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;


class FaqController extends Controller
{
    protected $faq;
    public $user;

    public function __construct(Faq $faq)
    {
        $this->faq     = $faq;
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

        $data['title'] = __('messages.common.faq_list');
        $data['rows'] = Faq::orderBy('order_id', 'asc')->get();
        return view('admin.faq.index', compact('data'));
    }

    public function create()
    {
        // if (is_null($this->user) || !$this->user->can('admin.faq.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }
        $data['title'] = __('messages.common.faq_create');
        $data['services'] = Service::where('status',1)->get();
        // return view('admin.faq.create');
         return view('admin.faq.create', $data);
    }

    public function store(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.faq.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        // return $request->all();

        $request->validate([
            'title' => 'required|string|unique:faqs,title',
            // 'service_id'  => 'required|array',
            'body' => 'required|max:1024',
        ]);
        DB::beginTransaction();
        try {
            $faq                   = new Faq();
            $faq->title            = $request->title;
            $faq->body             = $request->body;
            $faq->is_active        = $request->is_active;
            $faq->order_id         = Faq::max('order_id') + 1;
            $faq->created_by       = Auth::user()->id;
            $faq->created_at       = date('Y-m-d H:i:s');
            $faq->save();

            //for faq_service_table data store
            if ($request->has('service_id')) {
            foreach ($request->service_id as $serviceId) {
                DB::table('faq_service_map')->insert([
                    'faq_id'     => $faq->id,
                    'service_id' => $serviceId,
                    'created_by' => Auth::user()->id,
                    'created_at' => now(),

                ]);
            }
        }

        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error(__('messages.toastr.faq_create_error'), 'Error', ["positionClass" => "toast-top-center"]);
            return back();
        }
        DB::commit();
        Toastr::success(__('messages.toastr.faq_create_success'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.faq.index');
    }


    public function edit($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.faq.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = __('messages.common.faq_edit');
        $data['row'] = Faq::find($id);

        $data['allServices'] = Service::where('status', 1)->get();
        $data['selectedId'] = DB::table('faq_service_map')
            ->where('faq_id', $id)
            ->pluck('service_id')
            ->toArray();

        return view('admin.faq.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        // return $request->all();
        // if (is_null($this->user) || !$this->user->can('admin.faq.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $request->validate([
            'title' => 'required|string|unique:faqs,title,'. $id,
            'body' => 'required|max:1024',
            // 'service_id' => 'required|array',
            'order_id' => 'integer|unique:faqs,order_id,'. $id,
        ]);
        DB::beginTransaction();
        try {
            $faq                   = Faq::findOrFail($id);
            $faq->title            = $request->title;
            $faq->body             = $request->body;
            $faq->is_active        = $request->is_active;
            $faq->order_id         = $request->order_id;
            $faq->updated_by       = Auth::user()->id;
            $faq->updated_at       = date('Y-m-d H:i:s');
            $faq->update();

            // Delete old mappings from faq_service_map
            DB::table('faq_service_map')->where('faq_id', $id)->delete();
            // Insert new mappings
            if ($request->has('service_id') && is_array($request->service_id)) {
                foreach ($request->service_id as $serviceId) {
                    DB::table('faq_service_map')->insert([
                        'faq_id'     => $faq->id,
                        'service_id' => $serviceId,
                        'created_by' => Auth::user()->id,
                        'created_at' => now(),
                    ]);
                }
            }


        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            Toastr::error(__('messages.toastr.faq_update_error'), 'Error', ["positionClass" => "toast-top-center"]);
            return back();
        }
        DB::commit();
        Toastr::success(__('messages.toastr.faq_update_success'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.faq.index');
    }


    public function view($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.faq.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = 'FAQ View List';
        $data['row'] = Faq::find($id);

       // Get selected service IDs
    $selectedIds = DB::table('faq_service_map')
        ->where('faq_id', $id)
        ->pluck('service_id')
        ->toArray();

    // Get full service details
    $data['selectedServices'] = \App\Models\Service::whereIn('id', $selectedIds)->get();

        return view('admin.faq.view', compact('data'));
    }


    public function delete($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.faq.delete')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }
        DB::table('faq_service_map')->where('faq_id', $id)->delete();

        $faq = Faq::findOrFail($id);
        $faq->delete();
        Toastr::success(__('messages.toastr.faq_delete'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->back();
    }
}
