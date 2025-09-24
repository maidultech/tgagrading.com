<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Symfony\Component\Console\Input\Input;

class BlogPostController extends Controller
{
    public $user;
    protected $blog;
    public function __construct(Blog $blog)
    {
        $this->blog     = $blog;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index()
    {
        // if (is_null($this->user) || !$this->user->can('admin.blog-post.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title']  = 'Blog Post';
        $data['rows']   =  Blog::with('category')->orderBy('id', 'desc')->get();

        return view('admin.blog.post.index', compact('data'));
    }

    public function create()
    {
        // if (is_null($this->user) || !$this->user->can('admin.blog-post.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title']  = 'Blog Post Create';
        $data['bog_category']   = BlogCategory::orderBy('id', 'desc')->get();

        return view('admin.blog.post.create', compact('data'));
    }

    public function store(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.blog-post.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $request->validate([
            'title'         => 'required|max:150',
            'tags'          => 'required',
            'category_id'   => 'required',
            'status'        => 'required',
            'slug'          => 'nullable|unique:blogs,slug',  
            'details'       => 'required',
            'image'         => 'required'
        ]);
        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {

                // Upload and save the new image
                $image = $request->file('image');
                $imageName = time() . '-' . $request->file('image')->getClientOriginalName();
                $extension  = $image->getClientOriginalExtension();
                $file_path  = 'uploads/blog';
                $image->move(public_path($file_path), $imageName);
                $image  = $file_path . '/' . $imageName;

            }
            $blog_post              = new Blog();
            // $blog_post->user_id     = Auth::id();
            $blog_post->title       = $request->title;
            $blog_post->slug        = $request->slug ?? Str::slug($request->title);
            $blog_post->category_id = $request->category_id;
            $blog_post->image       = $image;
            $blog_post->status      = $request->status;
            $blog_post->tags        = json_encode($request->tags, true);
            $blog_post->details     = $request->details;
            $blog_post->meta_title       = $request->meta_title;
            $blog_post->meta_keywords    = json_encode($request->meta_keywords, true);
            $blog_post->meta_description = $request->meta_description;
            $blog_post->schema_markup    = $request->schema_markup;
            $blog_post->save();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            Toastr::error(trans('Blog not Created !'), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.blog-post.create');
        }
        DB::commit();
        Toastr::success(trans('Blog Created Successfully !'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.blog-post.index');
    }

    public function edit($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.blog-post.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = 'Post Edit';
        $data['row'] = Blog::find($id);
        $data['bog_category'] = BlogCategory::orderBy('id', 'desc')->get();
        return view('admin.blog.post.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.blog-post.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }
        $blog = Blog::findOrFail($id);  
        $this->validate($request, [
            'title'         => 'required|max:150',
            'tags'          => 'required',
            'slug'        => [
                    'required',
                    Rule::unique('blogs')->ignore($blog->id), // $blog is the current Blog model instance
                ],            'category_id'   => 'required',
            'status'        => 'required',
            'details'       => 'required',
            'image'         => 'nullable'

        ]);
        DB::beginTransaction();
        try {
            $blog_post =  Blog::find($id);

            if ($request->hasFile('image')) {

                // Delete the existing image file if it exists
                if (File::exists(public_path($blog_post->image))) {
                    File::delete(public_path($blog_post->image));
                }

                // Upload and save the new image
                $image = $request->file('image');
                $base_name  = preg_replace('/\..+$/', '', $image->getClientOriginalName());
                $base_name  = explode(' ', $base_name);
                $base_name  = implode('-', $base_name);
                $base_name  = Str::lower($base_name);
                $image_name = $base_name . "-" . uniqid() . "." . $image->getClientOriginalExtension();
                $extension  = $image->getClientOriginalExtension();
                $file_path  = 'uploads/blog';
                $image->move(public_path($file_path), $image_name);
                $blog_post->image  = $file_path . '/' . $image_name;

            }
            // $blog_post->user_id = Auth::id();
            $blog_post->title       = $request->title;
            $blog_post->slug        = $request->slug ?? Str::slug($request->title);
            $blog_post->category_id = $request->category_id;
            $blog_post->status      = $request->status;
            $blog_post->tags        = json_encode($request->tags, true);
            $blog_post->details     = $request->details;
            $blog_post->meta_title       = $request->meta_title;
            $blog_post->meta_keywords    = json_encode($request->meta_keywords, true);
            $blog_post->meta_description = $request->meta_description;
            $blog_post->schema_markup    = $request->schema_markup;


            // if ($request->hasFile('image')) {
            //     $image = Image::make($request->file('image'));

            //     $imageName = time() . '-' . $request->file('image')->getClientOriginalName();
            //     $destinationPath = public_path('assets/images/blog/');
            //     $image->save($destinationPath . $imageName);

            //     $destinationPathThumbnail = public_path('assets/images/blog/');
            //     if ($request->file('thumbnail')) {
            //         uploadImage($request->thumbnail, 'assets/images/blog/', 250, 200);
            //     }
            //     if ($request->file('banner_image')) {
            //         uploadImage($request->banner_image, 'assets/images/blog/', 850, 400);
            //     }
            //     $image->save($destinationPathThumbnail . $imageName);
            //     $blog_post->image = $imageName;
            // }
            $blog_post->save();
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            Toastr::error(trans('Post not Updated !'), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->back();
        }
        DB::commit();
        Toastr::success(trans('Post Updated Successfully !'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.blog-post.index');
    }

    public function view($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.blog-post.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = 'Post Edit';
        $data['row'] = Blog::find($id);
        $data['bog_category'] = BlogCategory::orderBy('id', 'desc')->get();

        return view('admin.blog.post.view', compact('data'));
    }

    public function delete($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.blog-post.delete')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        DB::beginTransaction();
        try {
            $blog_post = Blog::find($id);
            if (File::exists(public_path($blog_post->image))) {
                File::delete(public_path($blog_post->image));
            }
            $blog_post->delete();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error(trans('Post not Deleted !'), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.blog-post.index');
        }
        DB::commit();
        Toastr::success(trans('Post Deleted Successfully !'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.blog-post.index');
    }
}
