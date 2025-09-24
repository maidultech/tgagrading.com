<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImageContent;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImageContentController extends Controller
{
    public $user;
    protected $imageContent;
    public function __construct(ImageContent $imageContent)
    {
        $this->image_content = $imageContent;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index(Request $request)
    {

        $data['rows'] = ImageContent::orderBy('order_id', 'asc')->get();
        return view('admin.image_content.index', $data);
    }

    public function create()
    {

        return view('admin.image_content.create');
    }

    public function store(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.image_content.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $request->validate([
            'name' => 'required|max:155|unique:image_contents,name',
            'image' => 'required|max:256|image|mimes:png,jpg,jpeg,webp',
        ]);

        DB::beginTransaction();
        try {
            $imageContent = new ImageContent();

            $imageContent->name = $request->name;
            $imageContent->link = $request->link;
            $imageContent->status = $request->status;
            $imageContent->order_id = ImageContent::max('order_id') ? ImageContent::max('order_id') + 1 : 1;

            if ($request->hasFile('image')) {
                $images = $request->file('image');

                $imageName = rand(1, 99999999) . '.' . $images->getClientOriginalExtension();
                $imagePath = 'assets/uploads/image_content/';
                $images->move($imagePath, $imageName);

                $imageContent->image = $imagePath . $imageName;
            }

            $imageContent->save();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Failed to create image_content', 'Error', ["positionClass" => "toast-top-right"]);
            return back();
        }
        DB::commit();
        Toastr::success('ImageContent created successfully', 'Success', ["positionClass" => "toast-top-right"]);
        return redirect()->route('admin.image-content.index');
    }


    public function edit(Request $request, $id)
    {

        $data['row'] = ImageContent::findOrFail($id);
        return view('admin.image_content.edit', $data);
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|max:155|unique:image_contents,name,' . $id,
            'image' => 'image|mimes:png,jpg,jpeg,webp',
            'order_id' => 'required|integer|unique:image_contents,order_id,' . $id,
        ]);

        DB::beginTransaction();
        try {
            $imageContent = ImageContent::findOrFail($id);

            $imageContent->name = $request->name;
            $imageContent->status = $request->status;
            $imageContent->link = $request->link;
            $imageContent->order_id = $request->order_id;

            if ($request->hasFile('image')) {
                $images = $request->file('image');

                if (!empty($imageContent->image) && file_exists($imageContent->image)) {
                    @unlink($imageContent->image);
                }

                $imageName = rand(1, 99999999) . '.' . $images->getClientOriginalExtension();
                $imagePath = 'assets/uploads/image_content/';
                $images->move($imagePath, $imageName);

                $imageContent->image = $imagePath . $imageName;
            }

            $imageContent->update();
        } catch (\Exception $e) {
            DB::rollback();
            // dd($e);
            Toastr::error('Failed to update image_content', 'Error', ["positionClass" => "toast-top-right"]);
            return back();
        }
        DB::commit();
        Toastr::success('Image Content Update successfully', 'Success', ["positionClass" => "toast-top-right"]);
        return redirect()->route('admin.image-content.index');
    }

    public function delete($id)
    {

        $imageContent = ImageContent::findOrFail($id);
        if (!empty($imageContent->image) && file_exists($imageContent->image)) {
            @unlink($imageContent->image);
        }
        $imageContent->delete();

        Toastr::success("Image Content Delete Successfully", 'Success', ["positionClass" => "toast-top-right"]);
        return redirect()->back();
    }
}
