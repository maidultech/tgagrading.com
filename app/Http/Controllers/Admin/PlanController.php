<?php


namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    protected $plan;
    public $user;

    public function __construct(Plan $plan)
    {
        $this->plan     = $plan;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }


    public  function index()
    {

        $data['title'] = 'Manage Plan';
        $data['plan'] = Plan::orderBy('order_number','asc')->get();
        return view('admin.plan.index', $data);
    }
    public  function create()
    {


        $data['title'] = __('messages.plan.create_plan');
        $data['currency'] = Currency::get();
        return view('admin.plan.create', $data);
    }

    public  function store(Request $request)
    {

        $request->validate([
            'name' => 'required|unique:plans,name',
            'is_badge' => 'required',
            'custom_text' =>  'required_if:is_badge,custom',
            'minimum_card' => 'required_if:type,!subscription|nullable|numeric',
            'price' => 'required',
            'type' => 'required',
            'feature_name.*' => 'required',
            'subscription_year' => 'required_if:type,subscription|nullable|numeric',
            'subscription_peryear_card' => 'required_if:type,subscription|nullable|numeric',
        ], [
            'feature_name.*.required' => 'The feature name field is required.',
            'subscription_year.required_if' => 'The subscription year field is required.',
            'subscription_peryear_card.required_if' => 'The number of cards field is required.',
            'custom_text.required_if' => 'The custom badge text field is required.',
            'minimum_card.required_unless' => 'The minimum card field is required',
            'minimum_card.numeric' => 'The minimum card field must be a number.',
        ]);

        DB::beginTransaction();
        try {
            $plan = new Plan();
            $this->savePlanInfo($request, $plan);
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error(__('messages.toastr.plan_create_error'), 'Error', ["positionClass" => "toast-top-center"]);
            return back()->withInput();
        }
        DB::commit();
        Toastr::success(__('messages.toastr.plan_create_success'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.plan.index');
    }

    public  function edit($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.plan.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = __('messages.plan.edit_plan');
        $data['row'] = Plan::find($id);
        $data['features'] = PlanFeature::where('plan_id', $id)->get();
        $data['currency'] = Currency::get();
        return view('admin.plan.edit', $data);
    }

    public  function update($id, Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.plan.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $request->validate([
            'name' => 'required|unique:plans,name,' . $id . ',id',
            'is_badge' => 'required',
            'custom_text' =>  'required_if:is_badge,custom',
            'minimum_card' => 'required_if:type,!subscription|nullable|numeric',
            'price' => 'required',
            'feature_name.*' => 'required',
            'type' => 'required',
            'subscription_year' => 'required_if:type,subscription|nullable|numeric',
            'subscription_peryear_card' => 'required_if:type,subscription|nullable|numeric',
        ],[
            'feature_name.*.required' => 'The feature name field is required.',
            'subscription_year.required_if' => 'The subscription year field is required.',
            'subscription_peryear_card.required_if' => 'The number of cards field is required.',
            'minimum_card' => 'required_if:type,!subscription|nullable|numeric',
            'custom_text.required_if' => 'The custom badge text field is required.',
            'minimum_card.required_unless' => 'The minimum card field is required',
            'minimum_card.numeric' => 'The minimum card field must be a number.',
        ]);

        DB::beginTransaction();
        try {
            $orderNumberExist = Plan::where('id', '!=', $id)->where('order_number', $request->order_number)->exists();
            if($orderNumberExist)
            {
                Toastr::error('Order number is already exist', 'Error', ["positionClass" => "toast-top-center"]);
                return redirect()->back();
            }
            $plan   = Plan::find($id);
            $this->savePlanInfo($request, $plan);
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error(__('messages.toastr.plan_update_error'), 'Error', ["positionClass" => "toast-top-center"]);
            return back()->withInput();
        }
        DB::commit();
        Toastr::success(__('messages.toastr.plan_update_success'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.plan.index');
    }

    public function delete($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.plan.delete')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        DB::beginTransaction();
        try {

            // Check if any user is using this plan
            $usersUsingPlan = Order::where('plan_id', $id)->exists();

            if ($usersUsingPlan) {
                DB::rollback();
                Toastr::error(__('This Plan is already purchase can not delete this plan.'), 'Error', ["positionClass" => "toast-top-right"]);
                return redirect()->route('admin.plan.index');
            }

            $plan = Plan::find($id);
            $plan->delete();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('An unexpected error occured while deleting plan', 'Error', ["positionClass" => "toast-top-right"]);
            return redirect()->route('admin.plan.index');
        }
        DB::commit();
        Toastr::success('Plan Deleted Successfully !', 'Success', ["positionClass" => "toast-top-right"]);
        return redirect()->route('admin.plan.index');
    }

    /**
     * @param Request $request
     * @param Plan $plan
     * @return void
     */
    private function savePlanInfo(Request $request, Plan $plan): void
    {
        $plan->name = $request->name;
        $plan->minimum_card = $request->minimum_card ?? 0;
        $plan->price = $request->price ?? 0;
        $plan->status = $request->status;
        $plan->order_number = $request->order_number ?? Plan::max('order_number') + 1;
        $plan->is_badge = $request->is_badge ?? 'none';
        if($request->is_badge == 'custom')
        {
            $plan->custom_text = $request->custom_text;
        }
        $plan->type = $request->type;

        if($request->type == 'subscription')
        {
            $plan->subscription_year = $request->subscription_year;
            $plan->subscription_peryear_card = $request->subscription_peryear_card;
        } else {
            $plan->subscription_year = 0;
            $plan->subscription_peryear_card = 0;
        }

        $plan->save();
        PlanFeature::where('plan_id', $plan->id)->delete();
        if ($request->has('feature_name')) {
            $featureNames = $request->get('feature_name', []);
            foreach ($featureNames as $index => $featureName) {
                $feature = new PlanFeature();
                $feature->plan_id = $plan->id;
                $feature->feature_name = $featureName;
                $feature->save();
            }
        }
    }
}
