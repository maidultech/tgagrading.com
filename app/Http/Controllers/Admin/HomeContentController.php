<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeContent;
use App\Models\HomepageBanner;
use App\Models\HomepageStep;
use App\Models\OrderCard;
use App\Models\OrderDetail;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class HomeContentController extends Controller
{
    public function index()
    {
        $data['title'] = __('messages.common.home_page_content');
        $home = HomeContent::first();
        $banners = HomepageBanner::all();
        $steps = HomepageStep::all();
        return view('admin.home_content.index', compact('data', 'home', 'banners', 'steps'));
    }

    public function update(Request $request)
    {
        //    dd($request->all());


        try {
            $validatedData = $request->validate([
                'banner_image' => 'nullable',
                'banner_image.*' => 'nullable|image|max:2048',
                'banner_heading' => 'nullable',
                'banner_heading.*' => 'nullable|string|max:255',
                'banner_description' => 'nullable',
                'hw_heading' => 'required|string|max:255',
                'hw_sub_heading' => 'required|string|max:255',
                'service_title' => 'required|string|max:300',
                'service_subtitle' => 'required|string|max:1000',
                'why_title' => 'required|string|max:255',
                'why_subtitle' => 'required|string|max:1000',
                'sb_title' => 'required|string|max:255',
                'sb_details' => 'required|string|max:1000',
                'sb_button_text' => 'nullable|string|max:200',
                'sb_button_links' => 'nullable',
                'pricing_heading' => 'required|string|max:255',
                'pricing_sub_heading' => 'required|string|max:255',
                'verification_heading' => 'required|string|max:255',
                'verification_sub_heading' => 'required|string|max:255',
                'step_icon.*' => 'nullable|image|max:2048',
                'step_heading.*' => 'required|string|max:255',
                'step_subheading.*' => 'required|string|max:255'
            ]);
            // Handel steps
            $step_ids = $request->get('step_id', []);
            $homeStepIds = HomepageStep::pluck('id')->toArray();
            $deleteStepIds = array_diff($homeStepIds, $step_ids);
            if (!empty($deleteStepIds)) {
                HomepageStep::whereIn('id', $deleteStepIds)->delete();
            }
            foreach ($request->steps as $index => $step) {
                $homeStep = HomepageStep::find($step_ids[$index] ?? 0);
                if (!$homeStep) {
                    $homeStep = new HomepageStep();
                }
                if ($request->hasFile('step_icon') && isset($request->file('step_icon')[$index])) {
                    $homeStep->image = uploadGeneralImage($request->file('step_icon')[$index], 'steps', $homePageContent->steps[$index]->image ?? null);
                }
                $homeStep->title = $request->step_heading[$index] ?? null;
                $homeStep->description = $request->step_subheading[$index] ?? null;
                $homeStep->save();
            }

            // Handel banners
            $bannerIds = $request->get('banner_id', []);
            $homeBannerIds = HomepageBanner::pluck('id')->toArray();
            $deleteBannerIds = array_diff($homeBannerIds, $bannerIds);
            if (!empty($deleteBannerIds)) {
                HomepageBanner::whereIn('id', $deleteBannerIds)->delete();
            }
            foreach ($request->banners as $index => $banner) {
                $homeBanner = HomepageBanner::find($bannerIds[$index] ?? 0);
                if (!$homeBanner) {
                    $homeBanner = new HomepageBanner();
                }
                $homeBanner->title = $request->banner_heading[$index] ?? '';
                $homeBanner->info_active = $request->banner_info_active[$index] ?? 0;
                $homeBanner->description = $request->banner_description[$index] ?? '';
                if ($request->hasFile('banner_image') && isset($request->file('banner_image')[$index])) {
                    $homeBanner->image = uploadGeneralImage($request->file('banner_image')[$index], 'banners', $homePageContent->banner_image[$index] ?? null);
                }
                $homeBanner->save();
            }

            // Update home page content
            $homePageContent = HomeContent::firstOrFail();
            $homePageContent->hw_heading = $validatedData['hw_heading'];
            $homePageContent->hw_sub_heading = $validatedData['hw_sub_heading'];
            $homePageContent->sb_title = $validatedData['sb_title'];
            $homePageContent->sb_details = $validatedData['sb_details'];
            $homePageContent->sb_button_text = $validatedData['sb_button_text'];
            $homePageContent->sb_button_links = $validatedData['sb_button_links'];
            $homePageContent->why_title = $validatedData['why_title'];
            $homePageContent->why_subtitle = $validatedData['why_subtitle'];
            $homePageContent->pricing_heading = $validatedData['pricing_heading'];
            $homePageContent->pricing_sub_heading = $validatedData['pricing_sub_heading'];
            $homePageContent->verification_heading = $validatedData['verification_heading'];
            $homePageContent->verification_sub_heading = $validatedData['verification_sub_heading'];
            $homePageContent->service_title = $validatedData['service_title'];
            $homePageContent->service_subtitle = $validatedData['service_subtitle'];

            // Save the updated data
            $homePageContent->save();


        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->back()->with('error', __('messages.toastr.home_content_error'));
        }
        Toastr::success(__('messages.toastr.home_content_success'), 'Success', ["positionClass" => "toast-top-right"]);
        return redirect()->back();
    }

    function cardSearch(Request $request, $q)
    {

        if (strlen($q) > 1) {
            $query = OrderCard::select([
                    'year',
                    'brand_name',
                    'card',
                    'card_name',
                    'notes'
                ])
                ->distinct('item_name')
                ->whereAny([
                    'year',
                    'brand_name',
                    'card',
                    'card_name',
                    'item_name'
                ], 'LIKE', "%$q%")
                ->whereHas('details.order', function ($q) {
                    $q->where(function ($q2) {
                        $q2->where('status', 40)
                        ->orWhere(function ($q3) {
                            $q3->where('status', 35)
                                ->where('payment_status', 1);
                        });
                    });
                })
                ->orderBy('year', 'asc')
                ->get();

            return response([
                'success' => true,
                'data' => $query
            ]);
        } else {
            return response([
                'success' => false,
                'data' => [],
                'message' => 'Min 2 char required to search'
            ]);
        }
    }
}
