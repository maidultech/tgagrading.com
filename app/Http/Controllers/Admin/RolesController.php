<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Actions\Role\CreateRole;
use App\Actions\Role\UpdateRole;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    use ValidatesRequests;
    public $user;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.roles.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $roles = Role::orderBy('id', 'DESC')->paginate(5);
        return view('admin.roles.index', compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if (is_null($this->user) || !$this->user->can('admin.roles.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $permissions = Permission::all();
        $permission_groups = Admin::getPermissionGroup();

        return view('admin.roles.create', compact('permissions', 'permission_groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.roles.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        // $this->validate($request, [
        //     'name' => 'required|unique:roles,name',
        //     'permission' => 'required',
        // ]);

        DB::beginTransaction();
        try {
            CreateRole::create($request);
        } catch (\Throwable $th) {
            Toastr::error(trans($th->getMessage()), 'Error', ["positionClass" => "toast-top-right"]);
            DB::rollback();
            return back();
        }
        DB::commit();
        Toastr::success(trans('Role created successfully'), 'Success', ["positionClass" => "toast-top-right"]);
        return redirect()->route('admin.roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        // if (is_null($this->user) || !$this->user->can('admin.roles.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $role = $role;
        $rolePermissions = $role->permissions;

        return view('admin.roles.show', compact('role', 'rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.roles.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $role = Role::find($id);
        $permissions = Permission::all();
        $permission_groups = Admin::getPermissionGroup();
        return view('admin.roles.edit', compact('role', 'permission_groups', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.roles.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }
        
        DB::beginTransaction();
        try {
            $role = Role::find($id);
            UpdateRole::update($request, $role);
        } catch (\Throwable $th) {
            Toastr::error(trans($th->getMessage()), 'Error', ["positionClass" => "toast-top-right"]);
            // flashError($th->getMessage());
            DB::rollback();
            return back();
        }
        DB::commit();
        Toastr::success(trans('Role updated successfully'), 'Success', ["positionClass" => "toast-top-right"]);
        return redirect()->route('admin.roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $role = Role::find($id);

        $role->delete();
        Toastr::success(trans('Role deleted successfully'), 'Success', ["positionClass" => "toast-top-center"]);
        return redirect()->route('admin.roles.index');
    }
}
