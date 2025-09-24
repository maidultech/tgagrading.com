<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinalGrading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinalGradingController extends Controller
{
    public $user;
    protected $granding;
    public function __construct(FinalGrading $granding)
    {
        $this->granding   = $granding;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    function index(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.final-grading.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = "Final Grading";
        $data['rows'] = FinalGrading::orderBy('order_id','ASC')->get();
        
        // dd( $data['rows']);
        return view('admin.final-grading.index',compact('data'));
    }

    function update(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.final-grading.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $request->validate([
            'grading_id' => 'required|numeric|exists:finalgrading_name,id' , 'grading_name' => 'required|string|max:255|unique:finalgrading_name,name,'.$request->grading_id
        ]);

        $granding = FinalGrading::find($request->grading_id, );
        $granding->name = $request->grading_name;
        $granding->save();

        toastr()->success('Final Grading Name has been updated');
        return back();
    }
}
