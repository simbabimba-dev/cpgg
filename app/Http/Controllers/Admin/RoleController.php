<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use App\Models\Role;

class RoleController extends Controller
{

    const READ_PERMISSION = "admin.roles.read";
    const CREATE_PERMISSION = "admin.roles.create";
    const EDIT_PERMISSION = "admin.roles.edit";
    const DELETE_PERMISSION = "admin.roles.delete";
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function index(Request $request)
    {

        $allConstants = (new \ReflectionClass(__CLASS__))->getConstants();
        $this->checkAnyPermission($allConstants);

        //datatables
        if ($request->ajax()) {
            return $this->dataTableQuery();
        }

        $html = $this->dataTable();
        return view('admin.roles.index', compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $this->checkPermission(self::CREATE_PERMISSION);

        $permissions = Permission::all();

        return view('admin.roles.edit', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkPermission(self::CREATE_PERMISSION);

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:60', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'color' => ['required', 'string', 'regex:/^#(?:[A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
            'power' => 'required|integer|min:0|max:2147483647',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        if ($data['power'] > $this->getCurrentUserMaxRolePower()) {
            return back()->with('error', __('You dont have enough Power to create that Role'));
        }

        $role = Role::create([
            'name' => $data['name'],
            'color' => $data['color'],
            'power' => $data['power']
        ]);

        if (!empty($data['permissions'])) {
            $collectedPermissions = collect($data['permissions'])->map(fn($val)=>(int)$val);
            $role->givePermissionTo($collectedPermissions);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role saved'));
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Role $role
     * @return Application|Factory|View
     */
    public function edit(Role $role)
    {
        $this->checkPermission(self::EDIT_PERMISSION);

        if ($this->getCurrentUserMaxRolePower() < (int) $role->power) {
            return back()->with("error","You dont have enough Power to edit that Role");
        }

        $permissions = Permission::all();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Role $role
     * @return RedirectResponse
     */
    public function update(Request $request, Role $role)
    {
        $this->checkPermission(self::EDIT_PERMISSION);

        if ($this->getCurrentUserMaxRolePower() < (int) $role->power) {
            return back()->with("error","You dont have enough Power to edit that Role");
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:60', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'color' => ['required', 'string', 'regex:/^#(?:[A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
            'power' => 'required|integer|min:0|max:2147483647',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        if ($data['power'] > $this->getCurrentUserMaxRolePower()) {
            return back()->with('error', __('You dont have enough Power to set that role power'));
        }

        if (!empty($data['permissions'])) {
            if($role->id != 1){ //disable admin permissions change
                $collectedPermissions = collect($data['permissions'])->map(fn($val)=>(int)$val);
                $role->syncPermissions($collectedPermissions);
            }
        }

        //if($role->id == 1 || $role->id == 3 || $role->id == 4){ //dont let the user change the names of these roles
        //    $role->update([
        //        'color' => $request->color
        //    ]);
        //}else{
            $role->update([
                'name' => $data['name'],
                'color' => $data['color'],
                'power' => $data['power']
            ]);
        //}

        //if($role->id == 1){
        //    return redirect()->route('admin.roles.index')->with('success', __('Role updated. Name and Permissions of this Role cannot be changed'));
        //}elseif($role->id == 4 || $role->id == 3){
        //    return redirect()->route('admin.roles.index')->with('success', __('Role updated. Name of this Role cannot be changed'));
       // }else{
            return redirect()
                ->route('admin.roles.index')
                ->with('success', __('Role saved'));
        //}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return RedirectResponse
     */
    public function destroy(Role $role)
    {
        $this->checkPermission(self::DELETE_PERMISSION);

        if($role->id == 1 || $role->id == 3 || $role->id == 4){ //cannot delete the hard coded roles
            return back()->with("error","You cannot delete that role");
        }

        $users = User::role($role)->get();

        foreach($users as $user){
            //$user->syncRoles(['Member']);
            $user->syncRoles(4);
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role removed'));
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function dataTable()
    {
        $this->checkPermission(self::READ_PERMISSION);

        $query = Role::query()->withCount(['users', 'permissions'])->get();

        return datatables($query)
            ->editColumn('id', function (Role $role) {
                return $role->id;
            })
            ->addColumn('actions', function (Role $role) {
                return '
                            <a title="Edit" href="'.route("admin.roles.edit", $role).'" class="btn btn-sm btn-info"><i
                                    class="fa fas fa-edit"></i></a>
                            <form class="d-inline" method="post" action="'.route("admin.roles.destroy", $role).'">
                            ' . csrf_field() . '
                            ' . method_field("DELETE") . '
                                <button title="Delete" type="submit" class="btn btn-sm btn-danger confirm"><i
                                        class="fa fas fa-trash"></i></button>
                            </form>
                ';
            })

            ->editColumn('name', function (Role $role) {
                $color = preg_match('/^#(?:[A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', (string) $role->color) ? $role->color : '#ccc';
                return "<span style='background-color: " . e($color) . "' class='badge'>" . e($role->name) . "</span>";
            })
            ->editColumn('users_count', function ($query) {
                return $query->users_count;
            })
            ->editColumn('permissions_count', function ($query){
                return $query->permissions_count;
            })
            ->editColumn('power', function (Role $role){
                return $role->power;
            })
            ->rawColumns(['actions', 'name'])
            ->make(true);
    }

    private function getCurrentUserMaxRolePower(): int
    {
        $user = Auth::user();
        if (!$user) {
            return 0;
        }

        return (int) $user->roles()->max('power');
    }
}
