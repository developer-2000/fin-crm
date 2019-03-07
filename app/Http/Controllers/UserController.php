<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSettingsRequest;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Rank;
use App\Models\UsersGroup;
use Illuminate\Http\Request;
use \App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use  App\Models\Transaction;
use App\Models\Payout;

class UserController extends BaseController
{
    /* Страница всех пользователей */ /* true */
    public function index( Request $request, Role $roleModel )
    {
        $filter = [
            'id' => $request->get('id'),
            'login' => $request->get('login'),
            'surname' => $request->get('surname'),
            'name' => $request->get('name'),
            'company' => $request->get('company'),
            'status' => $request->get('status'),
            'sip' => $request->get('sip'),
            'role' => $request->get('role'),
        ];
        if ($request->isMethod('post')) {
            header('Location: ' . route('users') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $data = (new User)->getAllUsers($filter);
        $data['companies'] = Company::all();
        $data['roles'] = $roleModel->getAllRoles(auth()->user()->company_id);
        return view('users.index', $data);
    }

    public function show(
        Request $request,
        Order $orderModel,
        Transaction $transactionModel,
        Payout $payoutModel,
        $id,
        $tab = null
    )
    {
        $user = (new User)->getOneUserProfile($id);
        if (!$user || ($user && auth()->user()->role_id == 1 && $user->id != auth()->user()->id)) {
            abort(404);
        }
        $page = $request->input('page');
        $requestObject = 'query';
        if ($request->isMethod('post')) {
            $requestObject = 'request';
        }
        $filter = [
            'date_start' => $request->$requestObject->get('date_start'),
            'date_end' => $request->$requestObject->get('date_end'),
        ];

        if ($tab == 'transaction') {
            $data['orders'] = $transactionModel->getOrdersOneOperator($user->id, $filter, $page);
        } elseif ($tab == 'payout') {
            $data = $payoutModel->getAllPayoutOneUser($user->id, $page);
        }
        $data['salary'] = $transactionModel->getBalance($user->id);
        $data['tab'] = $tab;
        $data['userProfile'] = $user;
        $data['statistic'] = $orderModel->statisticOneOperator($user->id, $filter);

        return view('users.show', $data);
    }

    public function edit( $id )
    {
        $user = User::with('subProject', 'ips', 'roles');

        if (Auth::user()->project_id) {
            $user->where('project_id', Auth::user()->project_id);
        }
        if (Auth::user()->sub_project_id) {
            $user->where('sub_project_id', Auth::user()->sub_project_id);
        }
        $user = $user->findOrFail($id);

        $userRoles = array_map(function ( $element ) {
            return $elements = [
                'id' => "" . $element['id'] . "",
                'text' => $element['name']
            ];
        }, $user->roles->toArray());

        return view('users.edit', [
            'userOne' => $user,
            'userRolesJson' =>  json_encode($userRoles, JSON_UNESCAPED_UNICODE),
            'roles' => (new Role)->getAllRoles(),
            'companies' => Company::all(),
            'ranks' => Rank::all()
        ]);
    }

    public function editPermissions( $id )
    {
        $user = User::with( 'roles')->where('id', $id)->first();
        $userPermissions = (new User)->userPermissions($user);
        return view('users.user-permissions', [
            'userOne' => $user,
            'userPermissions' => $userPermissions,
            'data' => Permission::with('roles')->withCount('roles')->get()->groupBy('section'),
            'userRoles' => Role::with('permissions')->whereIn('id', $user->roles->pluck('id')->toArray())->get()
          //  'roles' => Role::all()
        ]);
    }

    /* Страница регистрации пользователя */ /* true */
    public function registration( Role $roleModel )
    {
        return view('users.registration', [
            'roles' => $roleModel->getAllRoles(),
            'companies' => Company::all(),
            'ranks' => Rank::all()
        ]);
    }

    public function changeDataUsersAjax( Request $request, User $authModel, $id )
    {
        if ($request->isMethod('post')) {

            $data = $request->get('inputs');
            $data = json_decode($data, true);
            $file = isset($_FILES['file']) ? $_FILES['file'] : [];

            return response()->json(
                $authModel->changeUserInformation($data, $file, $id)
            );
        }
        abort(404);
    }

    public function createAccountElastixAjax( Request $request, $id )
    {
        if ($request->isMethod('post')) {
            return response()->json(
                (new User)->createNewAccountPbx($request->all(), $id)
            );
        }
        abort(404);
    }

    public function changeAccountElastixAjax( Request $request, User $authModel, $id )
    {
        if ($request->isMethod('post')) {
            return response()->json(
                $authModel->changeAccountPbx($request->all(), $id)
            );
        }
        abort(404);
    }

    public function registrationDataUsersAjax( Request $request, User $authModel )
    {
        if ($request->isMethod('post')) {
            $data = json_decode($request->get('inputs'), true);
            $data['user_permissions'] = $request->get('user_permissions');
            $file = isset($_FILES['file']) ? $_FILES['file'] : [];
            return response()->json(
                $authModel->registration($data, $file)
            );
        }
        abort(404);
    }

    /**
     * поиск всех операторов по селекту
     */
    public static function findByWord( Request $request, User $authModel )
    {
        $term = is_string($request->input('query')) ? trim($request->input('query')) : $request->input('query');
        $company_id = is_string($request->input('company_id')) ? trim($request->input('company_id')) : $request->input('company_id');
        $role_id = is_string($request->input('role_id')) ? trim($request->input('role_id')) : $request->input('role_id');
        $rank_id = is_string($request->input('rank_id')) ? trim($request->input('rank_id')) : $request->input('rank_id');

        if (empty($term)) {
            return response()->json([]);
        }

        $users = $authModel->searchUsersByWord($term, $company_id, $role_id, $rank_id);
        $formatted_users = [];

        foreach ($users as $user) {
            $formatted_users[] = ['id' => $user->id, 'text' => $user->surname . "  " . $user->name];
        }

        return response()->json($formatted_users);

    }

    /**
     * поиск всех группы операторов по селекту
     */
    public static function findGroupByWord( Request $request, UsersGroup $usersGroupModel )
    {

        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }

        $groups = $usersGroupModel->searchGroupsByWord($term);
        $formatted_users_groups = [];

        foreach ($groups as $group) {
            $formatted_users_groups[] = ['id' => $group->id, 'text' => $group->name];
        }

        return \Response::json($formatted_users_groups);
    }

    public function getUsersByParametersAjax( Request $request )
    {
        $result = [];
        $users = [];
        $usersNotIn = [];
        $data = [];
        $usersArray = [];
        $usersArrayCount = 0;
        if (!empty($request->rowsData)) {
            foreach ($request->rowsData as $key => $rowsData) {
                if ($rowsData['type'] == 'on') {
                    $filters['companiesIds'] = (!empty($rowsData['companiesIds'])) ? "AND company_id IN(" . $rowsData['companiesIds'] . ")" : '';
                    $filters['rolesIds'] = (!empty($rowsData['rolesIds'])) ? "AND role IN(" . $rowsData['rolesIds'] . ")" : '';
                    $filters['ranksIds'] = (!empty($rowsData['ranksIds'])) ? "AND rank_id IN(" . $rowsData['ranksIds'] . ")" : '';
                    $filters['usersIds'] = (!empty($rowsData['usersIds'])) ? "AND id IN(" . $rowsData['usersIds'] . ")" : '';
                    $filters = "
                        " . $filters['companiesIds'] . "
                        " . $filters['rolesIds'] . "
                        " . $filters['ranksIds'] . "
                        " . $filters['usersIds'] . "
                        ";
                    $users = collect(DB::select('SELECT * FROM users  WHERE id <> 0 ' . $filters . ' ORDER BY company_id, NAME ASC'))->toArray();
                    foreach ($users as $item) {
                        $usersIn[$item->id] = get_object_vars($item);
                    }
                    $data[$key]['count'] = count($users);
                    $data[$key]['type'] = $rowsData['type'];
                    $data[$key]['rule_id'] = $key;
                    $usersArrayCount += count($users);
                    $result[] = $usersIn;
                    foreach ($result as $s) {
                        foreach ($s as $row) {
                            $previousUsersIds[] = $row['id'];
                            $previousUsers[$row['id']] = $row;
                        }
                    }
                }
            }
            foreach ($request->rowsData as $key2 => $rows) {
                if ($rows['type'] == 'off') {
                    $antiFilters['companiesIds'] = (!empty($rows['companiesIds'])) ? "AND company_id  IN(" . $rows['companiesIds'] . ")" : '';
                    $antiFilters['rolesIds'] = (!empty($rows['rolesIds'])) ? "AND role  IN(" . $rows['rolesIds'] . ")" : '';
                    $antiFilters['ranksIds'] = (!empty($rows['ranksIds'])) ? "AND rank_id  IN(" . $rows['ranksIds'] . ")" : '';
                    $antiFilters['usersIds'] = (!empty($rows['usersIds'])) ? "AND id  IN(" . $rows['usersIds'] . ")" : '';
                    $antiFilters = "
                        " . $antiFilters['companiesIds'] . "
                        " . $antiFilters['rolesIds'] . "
                        " . $antiFilters['ranksIds'] . "
                        " . $antiFilters['usersIds'] . "
                        ";
                    $users = collect(DB::select('SELECT * FROM users  WHERE id IN (' . implode(',', $previousUsersIds) . ') ' . $antiFilters . ' ORDER BY company_id, NAME ASC'))->toArray();
                    foreach ($users as $item) {
                        $usersNotIn[$item->id] = get_object_vars($item);
                    }
                    $usersArrayCount -= count($users);
                    $users = array_diff_key($previousUsers, $usersNotIn);
                    $data[$key2]['count'] = count($usersNotIn);
                    $data[$key2]['type'] = $rowsData['type'];
                    $data[$key2]['rule_id'] = $key;
                    $result = [];
                    $result[] = $users;
                }
            }

            foreach ($result as $key => $s) {
                foreach ($s as $row) {
                    $usersArray[$row['id']] = $row;
                    $usersIds[] = $row['id'];
                }
            }

            foreach ($usersArray as $user) {
                $user['role_name'] = (!empty($user['role']) && $user['role'] != 0 && $user['role'] != '') ? Role::where('id', $user['role'])->first()->name : NULL;
                $user['rank'] = !empty($user['rank_id']) ? Rank::where('id', $user['rank_id'])->first()->name : NULL;
                $user['company'] = (!empty($user['company_id']) && $user['company_id'] != 0 && $user['company_id'] != '') ?
                    Company::where('id', $user['company_id'])
                        ->first()->name : NULL;
                $newUsersArray[] = $user;
            }
        }
        return response()->json(['users' => $users, 'data' => $data, 'usersArray' => $newUsersArray, 'usersArrayCount' => $usersArrayCount]);
    }

    public function userSettings( UserSettingsRequest $request )
    {
        $user = User::find($request->get('id'));

        $dataRequest = $request->except(['id']);

        $user->setDataFromArray($dataRequest);

        return response()->json([
            'success' => $user->save(),
        ]);
    }
    public function setUsetPermission( $permissionId )
    {
        (new User)->setUsetPermission($permissionId);

        return response()->json([
            'success' => true,
        ]);
    }
}
