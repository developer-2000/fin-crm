<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\RolePermission;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends BaseController
{
    protected $permissionRepository;

    public function __construct(PermissionRepository $permissionRepository)
    {
        parent::__construct();
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * страница всех ролей и пермишинов
     */
    public function rolesPermissions()
    {
        return view(
            'roles.roleAndPermission',
        [
          'data' => Permission::with('roles')->withCount('roles')->get()->groupBy('section'),
          'roles' => Role::all()
        ]
        );
    }

    public function setRoleToPermission(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            return response()->json(
              $this->permissionRepository->setRolePermission(
                $id,
                $request->get('role'),
                  $request->get('status')
              )
            );
        }
        abort(404);
    }

    public function index(Role $roleModel)
    {
        return view('roles.index', [
            'roles'    => $roleModel->getAllRoles(),
            'projects' => Project::all()
        ]);
    }

    public function getAllRoles()
    {
        $roles = Role::all();
        $res = [];
        if ($roles) {
            foreach ($roles as $role) {
                $res[] = [
                    'value' => $role->id,
                    'text'  => $role->name,
                ];
            }
        }
        return response()->json($res);
    }

    public function findByName(Request $request)
    {
        $companies = $request->input('company_id');
        $term = trim($request->input('query'));

        $roles = RoleRepository::findByName($term, $companies);
        $formatted_roles = [];

        foreach ($roles as $role) {
            $formatted_roles[] = ['id' => $role->id, 'text' => $role->name];
        }

        return \Response::json($formatted_roles);
    }

    public function usersRolesCreateAjax(Request $request)
    {
        if ($request->isMethod('POST')) {
            $this->validate($request, [
                'name' => 'required|min:1|max:255',
            ]);
            $newRole = Role::create(
              ['name' => $request->name,
              'project_id' => $request->project_id
            ]
            );

            $result = true;
            if ($newRole && $request->delegated_permissions) {
                foreach ($request->delegated_permissions as $permission) {
                    $newRolePermissions = RolePermission::create(
                      [
                        'role_id'       => $newRole->id,
                        'permission_id' => $permission
                      ]
                    );

                    if ($newRolePermissions) {
                        $result = true;
                    } else {
                        $result = false;
                    }
                }
            }
            return response()->json([
                'success'   => $result,
                'tableHtml' => view('roles.roles-table', [
                    'roles' => Role::all(),
                ])->render()
            ]);
        }
    }
}
