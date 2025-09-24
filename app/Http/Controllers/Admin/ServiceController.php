<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceExtra;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Symfony\Component\Console\Input\Input;

class ServiceController extends Controller
{
    public $user;
    protected $service;
    public function __construct(Service $service)
    {
        $this->service = $service;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index()
    {

        $data['title'] = 'Services';
        $data['rows'] = Service::orderBy('id', 'desc')->get();

        return view('admin.service.index', compact('data'));
    }

    public function create()
    {
        $data['title'] = 'Service Create';

        return view('admin.service.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required|max:300',
            'sub_title' => 'required|max:1000',
            'status' => 'required',
            'details' => 'required',
            'thumb' => 'required',
            'top_bg' => 'required',
            'top_links' => 'nullable',
        ]);
        DB::beginTransaction();
        try {
            $service = new Service();
            if ($request->hasFile('thumb')) {

                // Upload and save the new thumb
                $thumb = $request->file('thumb');
                $imageName = time() . '-' . $request->file('thumb')->getClientOriginalName();
                $extension = $thumb->getClientOriginalExtension();
                $file_path = 'uploads/service';
                $thumb->move(public_path($file_path), $imageName);
                $thumb = $file_path . '/' . $imageName;
                $service->thumb = $thumb;

            }
            if ($request->hasFile('top_bg')) {

                // Upload and save the new top_bg
                $top_bg = $request->file('top_bg');
                $imageName = 'banner' . '-' . time() . '-' . $request->file('top_bg')->getClientOriginalName();
                $extension = $top_bg->getClientOriginalExtension();
                $file_path = 'uploads/service';
                $top_bg->move(public_path($file_path), $imageName);
                $top_bg = $file_path . '/' . $imageName;
                $service->top_bg = $top_bg;

            }

            if ($request->hasFile('sa_img')) {

                // Upload and save the new sa_img
                $sa_img = $request->file('sa_img');
                $imageName = 'banner' . '-' . time() . '-' . $request->file('sa_img')->getClientOriginalName();
                $extension = $sa_img->getClientOriginalExtension();
                $file_path = 'uploads/service';
                $sa_img->move(public_path($file_path), $imageName);
                $sa_img = $file_path . '/' . $imageName;
                $service->sa_img = $sa_img;

            }


            if ($request->hasFile('sb_img')) {

                // Upload and save the new sb_img
                $sb_img = $request->file('sb_img');
                $imageName = 'banner' . '-' . time() . '-' . $request->file('sb_img')->getClientOriginalName();
                $extension = $sb_img->getClientOriginalExtension();
                $file_path = 'uploads/service';
                $sb_img->move(public_path($file_path), $imageName);
                $sb_img = $file_path . '/' . $imageName;
                $service->sb_img = $sb_img;

            }


            if ($request->hasFile('sc_img')) {

                // Upload and save the new sc_img
                $sc_img = $request->file('sc_img');
                $imageName = 'banner' . '-' . time() . '-' . $request->file('sc_img')->getClientOriginalName();
                $extension = $sc_img->getClientOriginalExtension();
                $file_path = 'uploads/service';
                $sc_img->move(public_path($file_path), $imageName);
                $sc_img = $file_path . '/' . $imageName;
                $service->sc_img = $sc_img;

            }

            if ($request->hasFile('sd_img')) {

                // Upload and save the new sd_img
                $sd_img = $request->file('sd_img');
                $imageName = 'banner' . '-' . time() . '-' . $request->file('sd_img')->getClientOriginalName();
                $extension = $sd_img->getClientOriginalExtension();
                $file_path = 'uploads/service';
                $sd_img->move(public_path($file_path), $imageName);
                $sd_img = $file_path . '/' . $imageName;
                $service->sd_img = $sd_img;

            }

            if ($request->hasFile('se_img')) {

                // Upload and save the new se_img
                $se_img = $request->file('se_img');
                $imageName = 'banner' . '-' . time() . '-' . $request->file('se_img')->getClientOriginalName();
                $extension = $se_img->getClientOriginalExtension();
                $file_path = 'uploads/service';
                $se_img->move(public_path($file_path), $imageName);
                $se_img = $file_path . '/' . $imageName;
                $service->se_img = $se_img;

            }

            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $count = 1;
            // Check if a record with this slug already exists
            while (Service::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }


            $service->title = $request->title;
            $service->sub_title = $request->sub_title;
            $service->slug = $slug;
            $service->type = $request->type ? '2' : '1';
            $service->status = $request->status;
            $service->top_links = $request->top_links;
            $service->details = $request->details;

            $service->sa_title = $request->sa_title;
            $service->sa_details = $request->sa_details;

            $service->sb_title = $request->sb_title;
            $service->sb_details = $request->sb_details;

            $service->sc_title = $request->sc_title;
            $service->sc_details = $request->sc_details;

            $service->sd_title = $request->sd_title;
            $service->sd_details = $request->sd_details;

            $service->se_title = $request->se_title;
            $service->se_details = $request->se_details;

            $service->asking_title = $request->asking_title;
            $service->asking_link = $request->asking_link;
            $service->sb_main_title = $request->sb_main_title;

            $service->meta_title = $request->meta_title;
            $service->meta_key = $request->meta_key;
            $service->meta_description = $request->meta_description;
            $service->schema_markup = $request->schema_markup;

            $service->pricing_sub_heading = $request->pricing_sub_heading;
            $service->pricing_heading = $request->pricing_heading;

            $service->save();
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            Toastr::error(trans('Service not Created !'), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.service.create');
        }
        DB::commit();
        Toastr::success(trans('Service Created Successfully !'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.service.index');
    }

    public function edit($id)
    {

        $data['title'] = 'Service Edit';
        $data['service'] = Service::find($id);
        if ($data['service']->slug === 'trading-card-grading-service') {
            return view('admin.service.card_grading_service_edit', compact('data'));
        } elseif ($data['service']->slug === 'sports-card-grading-service') {
            return view('admin.service.sports_grading_service_edit', compact('data'));
        } elseif ($data['service']->slug === 'crossover-card-grading-service') {
            return view('admin.service.crossover_grading_service_edit', compact('data'));
        } else {
            return view('admin.service.edit', compact('data'));
        }
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required|max:300',
            'sub_title' => 'nullable|max:1000',
            'status' => 'required',
            'details' => 'required',
            'thumb' => 'nullable',
            'top_bg' => 'nullable',
            'top_links' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $service = Service::findOrFail($id);

            // Handle thumb image update
            if ($request->hasFile('thumb')) {
                // Delete old thumb if exists
                if ($service->thumb && file_exists(public_path($service->thumb))) {
                    unlink(public_path($service->thumb));
                }

                // Upload and save new thumb
                $thumb = $request->file('thumb');
                $imageName = time() . '-' . $thumb->getClientOriginalName();
                $file_path = 'uploads/service';
                $thumb->move(public_path($file_path), $imageName);
                $service->thumb = $file_path . '/' . $imageName;
            }

            // Handle top_bg image update
            if ($request->hasFile('top_bg')) {
                // Delete old top_bg if exists
                if ($service->top_bg && file_exists(public_path($service->top_bg))) {
                    unlink(public_path($service->top_bg));
                }

                // Upload and save new top_bg
                $top_bg = $request->file('top_bg');
                $imageName = 'banner-' . time() . '-' . $top_bg->getClientOriginalName();
                $file_path = 'uploads/service';
                $top_bg->move(public_path($file_path), $imageName);
                $service->top_bg = $file_path . '/' . $imageName;
            }

            // Handle section images (SA to SE)
            $sections = ['sa', 'sb', 'sc', 'sd', 'se'];
            foreach ($sections as $section) {
                $imgField = $section . '_img';
                if ($request->hasFile($imgField)) {
                    // Delete old image if exists
                    if ($service->$imgField && file_exists(public_path($service->$imgField))) {
                        unlink(public_path($service->$imgField));
                    }

                    // Upload and save new image
                    $image = $request->file($imgField);
                    $imageName = $section . '-' . time() . '-' . $image->getClientOriginalName();
                    $file_path = 'uploads/service';
                    $image->move(public_path($file_path), $imageName);
                    $service->$imgField = $file_path . '/' . $imageName;
                }
            }

            // Generate new slug only if title changed
            if ($request->type != 3) {
                if ($service->title != $request->title) {
                    $slug = Str::slug($request->title);
                    $originalSlug = $slug;
                    $count = 1;

                    while (Service::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                        $slug = $originalSlug . '-' . $count++;
                    }
                    $service->slug = $slug;
                }
            }

            // Update all other fields
            $service->title = $request->title;
            $service->sub_title = $request->sub_title;
            if ($request->type == 3) {
                $service->type = $request->type;
            } else {
                $service->type = $request->type ? '2' : '1';
            }
            $service->status = $request->status;
            $service->top_links = $request->top_links;
            $service->details = $request->details;

            // Update section details
            $service->sa_title = $request->sa_title;
            $service->sa_details = $request->sa_details;
            $service->sb_title = $request->sb_title;
            $service->sb_details = $request->sb_details;
            $service->sc_title = $request->sc_title;
            $service->sc_details = $request->sc_details;
            $service->sd_title = $request->sd_title;
            $service->sd_details = $request->sd_details;
            $service->se_title = $request->se_title;
            $service->se_details = $request->se_details;

            $service->asking_title = $request->asking_title;
            $service->asking_link = $request->asking_link;
            $service->sb_main_title = $request->sb_main_title;

            // Update meta fields
            $service->meta_title = $request->meta_title;
            $service->meta_key = $request->meta_key;
            $service->meta_description = $request->meta_description;
            $service->schema_markup = $request->schema_markup;

            $service->pricing_sub_heading = $request->pricing_sub_heading;
            $service->pricing_heading = $request->pricing_heading;

            $service->sa_link = $request->sa_link;
            $service->sb_link = $request->sb_link;
            $service->sc_link = $request->sc_link;
            $service->sd_link = $request->sd_link;
            $service->se_link = $request->se_link;

            $service->save();

            if ($request->extra_fields == 1) {
                // Create serviceExtra if not exists
                if (!$service->serviceExtra) {
                    $serviceExtra = new ServiceExtra();
                    $serviceExtra->service_id = $service->id;
                } else {
                    $serviceExtra = $service->serviceExtra;
                }

                // Handle section images (SF to SH)
                $sections = ['sf', 'sg', 'sh'];
                foreach ($sections as $section) {
                    $imgField = $section . '_img';
                    if ($request->hasFile($imgField)) {
                        // Delete old image if exists
                        if ($serviceExtra->$imgField && file_exists(public_path($serviceExtra->$imgField))) {
                            unlink(public_path($serviceExtra->$imgField));
                        }

                        // Upload and save new image
                        $image = $request->file($imgField);
                        $imageName = $section . '-' . time() . '-' . $image->getClientOriginalName();
                        $file_path = 'uploads/service';
                        $image->move(public_path($file_path), $imageName);
                        $serviceExtra->$imgField = $file_path . '/' . $imageName;
                    }
                }

                // Assign text content
                $serviceExtra->extra_title = $request->extra_title;
                $serviceExtra->sf_title = $request->sf_title;
                $serviceExtra->sf_details = $request->sf_details;
                $serviceExtra->sg_title = $request->sg_title;
                $serviceExtra->sg_details = $request->sg_details;
                $serviceExtra->sh_title = $request->sh_title;
                $serviceExtra->sh_details = $request->sh_details;

                $serviceExtra->save();
            }


        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            Toastr::error(trans('Service not Updated!'), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.service.edit', $id);
        }

        DB::commit();
        Toastr::success(trans('Service Updated Successfully!'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.service.index');
    }

    public function view($id)
    {

        $data['title'] = 'Post Edit';
        $data['row'] = Service::find($id);

        return view('admin.service.view', compact('data'));
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $service = Service::findOrFail($id);

            // List of all image fields to delete
            $imageFields = [
                'thumb',
                'top_bg',
                'sa_img',
                'sb_img',
                'sc_img',
                'sd_img',
                'se_img'
            ];

            // Delete all associated files
            foreach ($imageFields as $field) {
                if ($service->$field && file_exists(public_path($service->$field))) {
                    unlink(public_path($service->$field));
                }
            }

            // Delete the service record
            $service->delete();

            DB::commit();
            Toastr::success(trans('Service deleted successfully!'), 'Success', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.service.index');

        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error(trans('Failed to delete service!'), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.service.index');
        }
    }
}
