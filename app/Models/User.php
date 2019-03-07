<?php

namespace App\Models;

use App\Permissions\HasPermissionsTrait;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Exception;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Models\Campaign;

class User extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Notifiable, Authenticatable, Authorizable, CanResetPassword, HasPermissionsTrait;

    private $salt = 'IAmPartOfThatPower';
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login', 'login_sip', 'name', 'surname', 'middle', 'photo', 'phone', 'birthday', 'password',
        'password_md5', 'ban', 'elastix_id', 'elastix_role', 'role_id', 'rank_id', 'password_elastix',
        'group', 'nat', 'company_id', 'campaign_id', 'email', 'password_updated', 'remember_token', 'project_id',
        'sub_project_id', 'time_zone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }

    /*get user for order opened*/
    public function orderOpened()
    {
        return $this->hasOne('App\Models\OrdersOpened');
    }

    /*get company by user*/
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /*get role by user*/
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /*get feedback */
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    /*get feedback */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * get project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function subProject()
    {
        return $this->belongsTo(Project::class, 'sub_project_id');
    }

    /**
     * get posts
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function collectorLogs()
    {
        return $this->hasMany(CollectorLog::class);
    }

    public function orderLogs()
    {
        return $this->hasMany(OrdersLog::class);
    }

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }

    public function ordOrder()
    {
        return $this->hasMany(OrdOrder::class, 'order_user', 'id');
    }

    /**
     * get ips
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ips()
    {
        return $this->hasMany(Ip::class);
    }

    public function callLogs()
    {
        return $this->hasMany(CallProgressLog::class);
    }

    public function ordersTarget()
    {
        return $this->hasMany(Order::class, 'target_user');
    }

    public function passes()
    {
        return $this->hasMany(Pass::class);
    }

    public function movings()
    {
        return $this->hasMany(Moving::class);
    }

    public function scopeWithCollectorRole( $query )
    {
        return $query->where('role_id', 14);
    }

    public function scopeModeratorsActive( $query )
    {
        return $query->where('role_id', 3)->where('ban', 0);
    }

    /**
     * Получаем хеш + соль пароля
     * @param string $password пароль
     * @return string
     */
    private function getHashPassword( $password )
    {
        return md5(md5($this->salt . $password));
    }

    /**
     * Получаем хеш пароля
     * @param string $password пароль
     * @param string $password пароль
     * @return string
     */
    function getMD5Password( $password )
    {
        return md5($password);
    }

    function getOperatorName( $companyId = 0 )
    {
        $result = DB::table($this->table)->select('id', 'name', 'surname');
        if ($companyId) {
            $result = $result->where('company_id', $companyId);
        }

        return collect(
            $result->get()
        )->keyBy('id');
    }

    /**
     * Получаем операторов
     * @return object
     */
    function getOperators( $companyId = 0 )
    {
        $result = User::select('id', 'name', 'surname', 'campaign_id', 'login_sip', 'company_id')
            ->whereIn('role_id', [1, 16]);

        if ($companyId) {
            $result = $result->where('company_id', $companyId);
        }
        return $result->get();
    }

    /**
     * Получаем данные по операторам
     * @return array
     */
    function getAgentsMonitor()
    {
        $agents = $this->apiElastixProcessing2('getOnlineAgent');
        if ($agents->status == 200) {
            foreach ($agents->data as &$agent) {
                $time = time() - (strtotime($agent->datetime_init) + 3600);
                $agent->datetime_init = date('H:i:s', $time);
            }
        }

        return $agents;
    }

    /**
     * Получаем мониторинг по компаниям
     * @return array
     */
    function getCampaignMonitor()
    {
        return $this->apiElastixProcessing2('getCompanyMonitoring');
    }

    function registration( $data, $file )
    {
        $result = [
            'errors' => 0,
            'status' => 0,
        ];
        \Validator::extend('without_spaces', function ( $attr, $value ) {
            return preg_match('/^\S*$/u', $value);
        });
        \Validator::extend('check_rank', function ( $attr, $value, $params, $validator ) {
            $roleId = $validator->getData()[$params[0]];
            $rank = Rank::where('id', $value)->where('role_id', $roleId)->first();
            if (!$rank) {
                return false;
            }
            return true;
        }, 'Поле "Ранг" не соответсвует роли');
        $rules = [
            'company' => 'nullable|numeric',
            'sub_project_id' => 'nullable|numeric',
            'role' => 'required|numeric',
            'rank' => 'nullable|numeric|check_rank:role',
            'login' => 'required|max:50|min:2|regex:/^[a-zA-Z0-9\_\-\@\!\#\$\%\^\&\*\.]*$/|without_spaces',
            'password' => 'required|max:35|min:8|regex:/^[a-zA-Z0-9\_\-\@\!\#\$\%\^\&\*\.]*$/|without_spaces',
            'surname' => 'required|max:255|min:2',
            'name' => 'required|max:255|min:2',
            'middle' => 'max:255|min:1',
            'phone' => 'numeric|digits_between:9,20',
            'block' => 'numeric|max:2',
            'time_zone' => 'required|in:' . implode(',', timezone_identifiers_list()),
            'birthday' => 'regex:/(^[0-9]{0,2}\/[0-9]{0,2}\/[0-9]{0,4})$/',
        ];
        if (Rank::where('role_id', $data['role'])->first()) {
            $rules['rank'] .= '|required';
        }
        $validator = \Validator::make($data, $rules);
        if ($validator->fails()) {
            $result['errors'] = $validator->errors();
            return $result;
        }

        $role = Role::find($data['role']);

        $company = $role ? $role->company : 0;
        if ($company && !$data['company']) {
            $result['errors'] = ['company' => true];
            return $result;
        }

        $login = DB::table($this->table)
            ->where('login', $data['login'])
            ->first();
        if ($login) {
            $result['errors'] = ['login' => true];
            return $result;
        }
        try {
            $projectId = $role ? $role->project_id : null;

            $user = User::create([
                'login' => $data['login'],
                'name' => $data['name'],
                'surname' => $data['surname'],
                'middle' => $data['middle'],
                'phone' => $data['phone'],
                'ban' => $data['block'],
                'company_id' => $data['company'],
                'role_id' => $data['role'],
                'birthday' => $data['birthday'],
                'project_id' => $projectId,
                'time_zone' => $data['time_zone'],
                'sub_project_id' => isset($data['sub_project_id']) ? $data['sub_project_id'] : 0,
                'photo' => '/img/users/photos/default.png',
                'password' => !empty($data['password']) ? \Hash::make($data['password']) : NULL,
                'password_md5' => !empty($data['password']) ? $this->getMD5Password($data['password']) : NULL,
            ]);
            $id = $user->id;


            if ($data['user_permissions'] && $id) {

                //dd($data['user_permissions']);
            }
            $result['status'] = $id;
        } catch (Exception $e) {
            $result['errors'] = ['file' => true];
        }
        if ($file) {
            if (($file['type'] == 'image/jpeg' || $file['type'] == 'image/png' || $file['type'] == 'image/jpg') && $file['size'] < 2 * 1024 * 1024) {
                $name = $id . '.jpg';
                move_uploaded_file($file['tmp_name'], '/img/users/photos/' . $name);
                $update['photo'] = '/img/users/photos/' . $name;
                DB::table($this->table)
                    ->where('id', $id)
                    ->update($update);
            } else {
                $result['errors'] = ['file' => true];
                return $result;
            }
        }
        return $result;
        abort(404);
    }

    function getAllUsers( $filter )
    {
//        $result = DB::table($this->table . ' AS u')->select('u.id', 'u.name', 'u.surname', 'u.login', 'u.ban', 'u.elastix_role',
//            'r.name AS role', 'u.elastix_id', 'u.photo', 'c.name AS company', 'ra.name AS rankName')
//            ->leftJoin('role AS r', 'u.role_id', '=', 'r.id')
//            ->leftJoin('companies AS c', 'c.id', '=', 'u.company_id')
//            ->leftJoin('ranks AS ra', 'u.rank_id', '=', 'ra.id');
        $result = User::with('roles', 'rank', 'company', 'project', 'subproject');
        if (auth()->user()->company_id) {
            $result->whereHas('company', function ( $query ) {
                $query->where('company_id', auth()->user()->company_id);
            });
        }
        if (auth()->user()->project_id) {
            $result->whereHas('project', function ( $query ) {
                $query->where('project_id', auth()->user()->project_id);
            });
        }
        if (auth()->user()->sub_project_id) {
            $result->whereHas('subproject', function ( $query ) {
                $query->where('sub_project_id', auth()->user()->sub_project_id);
            });
        }

        if ($filter['id']) {
            $result = $result->where('id', $filter['id']);
        }

        if ($filter['login']) {
            $result = $result->where('login', 'like', $filter['login'] . '%');
        }

        if ($filter['surname']) {
            $result = $result->where('surname', 'like', $filter['surname'] . '%');
        }
        if ($filter['name']) {
            $result = $result->where('name', 'like', $filter['name'] . '%');
        }
        if ($filter['company']) {
            $company = explode(',', $filter['company']);
            $result->whereHas('company', function ( $query ) use ( $company ) {
                $query->whereIn('company_id', $company);
            });
        }
        if ($filter['role']) {
            $roles = explode(',', $filter['role']);
            $result->whereHas('roles', function ( $query ) use ( $roles ) {
                $query->whereIn('roles.id', $roles);
            });
        }
        if ($filter['status']) {
            if ($filter['status'] == 2) {
                $result = $result->whereIn('ban', [0, 1]);
            } else {
                $result = $result->where('ban', $filter['status']);
            }
        } else {
            $result = $result->where('ban', 0);
        }
//        if ($filter['sip']) {
//            $result = $result->whereNotNull('u.elastix_id');
//        }
        $result = $result
            ->orderBy('id')
            ->paginate(50);
        return [
            'allUsers' => $result->appends(Input::except('page')),
            'count' => $result->total()
        ];
    }

    function getOneUser( $id )
    {
        $user = DB::table($this->table . ' AS u')->select('u.id', 'u.login', 'u.ban', 'r.name AS role', 'u.group', 'u.name',
            'u.surname', 'u.nat', 'u.elastix_id', 'u.elastix_role AS sip', 'u.login_sip', 'u.birthday',
            'u.company_id', 'u.photo', 'u.middle', 'u.phone', 'u.campaign_id', 'r.company AS reqComp', 'u.rank_id', 'u.role_id AS role_id')
            ->leftJoin('role AS r', 'u.role_id', '=', 'r.id')
            ->where('u.id', $id)
            ->first();
        $user->ips = DB::table('iptable')
            ->where('user_id', $user->id)
            ->get();
        return $user;
    }

    public function getOneUserProfile( $id )
    {
        return DB::table($this->table . ' AS u')
            ->select('u.id', 'u.name', 'u.surname', 'r.name as role', 'u.role_id AS roleNumb', 'u.photo',
                'u.company_id', 'c.billing_type', 'time_zone')
            ->leftJoin('companies AS c', 'c.id', '=', 'u.company_id')
            ->leftJoin('role AS r', 'u.role_id', '=', 'r.id')
            ->where('u.id', $id)
            ->first();
    }

    public function getAllOperatorsInCampaigns()
    {
        $operators = DB::table($this->table . ' AS u')
            ->select('u.name', 'u.surname', 'r.name AS role', 'u.id', 'u.campaign_id', 'u.login_sip')
            ->leftJoin('role AS r', 'r.id', '=', 'u.role_id')
            ->whereIn('u.role_id', [1, 16, 14])
            ->where('u.login_sip', '>', 0)
            ->where('ban', 0);

        if (auth()->user()->company_id) {
            $operators = $operators->where('u.company_id', auth()->user()->company_id);
        }
        $operators = $operators->get();

        $res = [];
        if ($operators) {
            foreach ($operators as $operator) {
                $res[$operator->campaign_id][] = $operator;
            }
        }
        return $res;
    }

    public function searchOperators( $search )
    {
        $operators = DB::table($this->table)
            ->whereIn('role_id', [1, 14])
            ->where(function ( $query ) use ( $search ) {
                $query->where('name', 'like', $search . '%')
                    ->orWhere('surname', 'like', $search . '%')
                    ->orWhere('login_sip', 'like', $search . '%');
            });

        if (auth()->user()->company_id) {
            $operators = $operators->where('company_id', auth()->user()->company_id);
        }
        $operators = $operators->get();
        $res = [];
        if ($operators) {
            foreach ($operators as $operator) {
                $res[$operator->campaign_id][] = $operator;
            }
        }
        return $res;
    }

    public function changeUserInformation( $data, $file, $id )
    {
        $user = DB::table($this->table)->where('id', $id)->first();
        if ($user) {
            $result = [
                'errors' => 0,
                'status' => 0,
            ];
            \Validator::extend('without_spaces', function ( $attr, $value ) {
                return preg_match('/^\S*$/u', $value);
            });
            \Validator::extend('check_rank', function ( $attr, $value, $params, $validator ) {
                $roleId = $validator->getData()[$params[0]];
                $rank = Rank::where('id', $value)->where('role_id', $roleId)->first();
                if (!$rank) {
                    return false;
                }
                return true;
            }, 'Поле "Ранг" не соответсвует роли');
            $rules = [
                'company' => 'nullable|numeric',
                'roles' => 'required',
                'rank' => 'nullable|numeric|check_rank:role',
                'login' => 'required|max:50|min:2|regex:/^[a-zA-Z0-9\_\-\@\!\#\$\%\^\&\*\.]*$/|without_spaces',
                'password' => 'max:35|min:8|regex:/^[a-zA-Z0-9\_\-\@\!\#\$\%\^\&\*\.]*$/|without_spaces',
                'surname' => 'required|max:255|min:2',
                'name' => 'required|max:255|min:2',
                'middle' => 'max:255|min:1',
                'phone' => 'numeric|digits_between:9,20',
                'block' => 'numeric|max:2',
                'sub_project_id' => 'nullable|numeric',
                'time_zone' => 'required|in:' . implode(',', timezone_identifiers_list()),
                'birthday' => 'regex:/(^[0-9]{0,2}\/[0-9]{0,2}\/[0-9]{0,4})$/',
            ];

            //:todo
//            if (Rank::where('role_id', $data['role'])->first()) {
//                $rules['rank'] .= '|required';
//            }

            $validator = \Validator::make($data, $rules);
            if ($validator->fails()) {
                $result['errors'] = $validator->errors();
                return $result;
            }

//            $role = Role::find($data['role']);
//
//            $company = $role ? $role->company : 0;
//            if ($company && !$data['company']) {
//                $result['errors'] = ['company' => true];
//                return $result;
//            }

            if ($user->login != $data['login']) {
                $login = DB::table($this->table)
                    ->where('login', $data['login'])
                    ->where('id', '!=', $id)
                    ->first();
                if ($login) {
                    $result['errors'] = ['login' => true];
                    return $result;
                }
            }


            //   $projectId = $role ? $role->project_id : null;
            $projectId = isset($data['project_id']) ? $data['project_id'] : 0;

            //save user roles
            $userRoles = DB::table('users_roles')->where('user_id', $id)->pluck('role_id')->toArray();

            foreach (explode(',', $data['roles']) as $role) {
                if (!in_array($role, $userRoles)) {
                    DB::table('users_roles')
                        ->insert(['user_id' => $id, 'role_id' => $role]);
                }
            }

            $update = [
                'login' => $data['login'],
                'name' => $data['name'],
                'surname' => $data['surname'],
                'middle' => $data['middle'],
                'phone' => $data['phone'],
                'ban' => $data['block'],
                'company_id' => $data['company'],
                'role_id' => isset($data['role']) ? $data['role'] : NULL,
                'rank_id' => $data['rank'],
                'birthday' => $data['birthday'],
                'project_id' => $projectId,
                'time_zone' => $data['time_zone'],
                'sub_project_id' => isset($data['sub_project_id']) ? $data['sub_project_id'] : 0,
            ];

            if ($data['name'] != $user->name) {
                if ($user->login_sip) {
                    $this->apiElastixProcessing2('changeUser', [
                        'login' => $user->login_sip,
                        'name' => $data['name'],
                    ]);
                }
            }

            if ($data['password']) {
                if ($user->login_sip) {
                    $this->apiElastixProcessing2('changeUser', [
                        'login' => $user->login_sip,
                        'password' => $data['password'],
                    ]);
                }
                $update['password'] = \Hash::make($data['password']);
                $update['password_md5'] = $this->getMD5Password($data['password']);
            }

            if ($file) {
                if (($file['type'] == 'image/jpeg' || $file['type'] == 'image/png' || $file['type'] == 'image/jpg') && $file['size'] < 2 * 1024 * 1024) {
                    $name = $user->id . '.jpg';
                    move_uploaded_file($file['tmp_name'], '/img/users/photos/' . $name);
                    $update['photo'] = '/img/users/photos/' . $name;
                } else {
                    $result['errors'] = ['file' => true];
                    return $result;
                }
            }
//            if(auth()->user()->id == 987593){
//                dd($_SERVER);
//            }

            //write log to file in storage  app
            try {
                // log changed data with queque and operator to storage/logs/info.log
                $currentUser = auth()->user();

                Storage::append('user-update.log', Carbon::now()->format('Y-m-d H:i:s') . ' ' . $currentUser->name . ' ' . $currentUser->surname . ' IP: ' . $_SERVER['REMOTE_ADDR'] . ' изменил данные: login - ' . $data['login'] . '; name - ' . $data['name'] . '; surname - ' . $data['surname'] . '; middle - ' . $data['middle'] . '; phone - ' . $data['phone'] . 'ban - ' . $data['block'] . '; company_id - ' . $data['company'] . '; role_id - ' . $data['role'] . '; rank_id - ' . $data['rank'] . ';  birthday -' . $data['birthday'] . '; project_id -' . $projectId . '; sub_project_id -' . (isset($data['sub_project_id']) ? $data['sub_project_id'] : 0) . '; pass - ' . (!empty($data['password'] ? 'пароль обновлен' : 'пароль не менялся') . "\n")
                );
            } catch (\Exception $exception) {
                Storage::append('info.log', $exception->getMessage());
            }

            try {
                DB::table($this->table)->where('id', $id)->update($update);
                $result['status'] = 1;
            } catch (Exception $e) {
                $result['errors'] = ['file' => true];
            }
            return $result;
        }
        abort(404);
    }

    public function createNewAccountPbx( $data, $id )
    {
        $user = DB::table($this->table)->where('id', $id)->first();
        if ($user) {
            $result = [
                'errors' => 0,
                'status' => 0,
            ];

            \Validator::extend('without_spaces', function ( $attr, $value ) {
                return preg_match('/^\S*$/u', $value);
            });

            $validator = \Validator::make($data, [
                'login_sip' => 'required|numeric|min:0|digits_between:4,10',
                'nat' => 'required|numeric|max:2|min:0',
                'password_sip' => 'required|max:35|min:25|regex:/^[a-zA-Z0-9\_\-\@\!\#\$\%\^\&\*]*$/|without_spaces',
            ]);
            if ($validator->fails()) {
                $result['errors'] = $validator->errors();
                return $result;
            }

            $userIps = DB::table('iptable')
                ->where('user_id', $user->id)
                ->get();

            if (!isset($data['ips']) && $userIps) {
                foreach ($userIps as $ip) {
                    DB::table('iptable')
                        ->where('user_id', $user->id)
                        ->where('ip', $ip->ip)
                        ->delete();
                    $this->apiElastixProcessing2('deleteIP', [
                        'ip' => $ip->ip,
                    ]);
                }
            } else {
                if (isset($data['ips'])) {
                    foreach ($data['ips'] as $ip) {
                        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                            $result['errors'] = ['ips' => true];
                        }
                    }
                    if ($result['errors']) {
                        return $result;
                    }
                    $ips = [];
                    if ($userIps) {
                        foreach ($userIps as $ip) {
                            DB::table('iptable')
                                ->where('user_id', $id)
                                ->where('ip', $ip->ip)
                                ->delete();
                            $this->apiElastixProcessing2('deleteIP', [
                                'ip' => $ip->ip,
                            ]);
                        }
                    }
                    foreach ($data['ips'] as $ip) {
                        $ips[] = [
                            'user_id' => $id,
                            'ip' => $ip
                        ];
                        $this->apiElastixProcessing2('addIP', [
                            'ip' => $ip,
                        ]);
                    }
                    DB::table('iptable')->insert($ips);
                }
            }
            $res = $this->apiElastixProcessing2('addUserPost', false, [
                'name' => $user->name,
                'login' => $data['login_sip'],
                'password' => $user->password_md5,
                'password_sip' => $data['password_sip'],
                'nat' => $data['nat'] ? 'yes' : 'no',
            ]);
            if (is_object($res)) {
                if ($res->status == 200) {
                    $update = [
                        'login_sip' => $data['login_sip'],
                        'password_elastix' => $this->getMD5Password($data['password_sip']),
                        'elastix_role' => 1,
                        'nat' => $data['nat'],
                    ];
                    $result['status'] = DB::table($this->table)
                        ->where('id', $id)
                        ->update($update);
                } else {
                    $result['errors'] = [
                        'login_sip' => true,
                        'password_sip' => true,
                    ];
                }
            } else {
                $result['errors'] = [
                    'login_sip' => true,
                    'password_sip' => true,
                ];
            }
            return $result;
        }
    }

    public function changeAccountPbx( $data, $id )
    {

        $user = DB::table($this->table)->where('id', $id)->first();
        if ($user) {
            $result = [
                'errors' => 0,
                'status' => 0,
            ];
            \Validator::extend('without_spaces', function ( $attr, $value ) {
                return preg_match('/^\S*$/u', $value);
            });
            $validator = \Validator::make($data, [
                'login_sip' => 'required|numeric|min:0|digits_between:4,10',
                'nat' => 'required|numeric|max:2|min:0',
                'password_sip' => 'max:35|min:25|regex:/^[a-zA-Z0-9\_\-\@\!\#\$\%\^\&\*]*$/|without_spaces',
            ]);
            if ($validator->fails()) {
                $result['errors'] = $validator->errors();
                return $result;
            }
            $userIps = DB::table('iptable')
                ->where('user_id', $user->id)
                ->get();
            if (!isset($data['ips']) && $userIps) {
                foreach ($userIps as $ip) {
                    DB::table('iptable')
                        ->where('user_id', $user->id)
                        ->where('ip', $ip->ip)
                        ->delete();
                    $this->apiElastixProcessing2('deleteIP', [
                        'ip' => $ip->ip,
                    ]);
                }
            } else {
                if (isset($data['ips'])) {
                    foreach ($data['ips'] as $ip) {
                        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                            $result['errors'] = ['ips' => true];
                        }
                    }
                    if ($result['errors']) {
                        return $result;
                    }
                    $ips = [];
                    if ($userIps) {
                        foreach ($userIps as $ip) {
                            DB::table('iptable')
                                ->where('user_id', $user->id)
                                ->where('ip', $ip->ip)
                                ->delete();
                            $this->apiElastixProcessing2('deleteIP', [
                                'ip' => $ip->ip,
                            ]);
                        }
                    }
                    foreach ($data['ips'] as $ip) {
                        $ips[] = [
                            'user_id' => $user->id,
                            'ip' => $ip
                        ];
                        $this->apiElastixProcessing2('addIP', [
                            'ip' => $ip,
                        ]);
                    }
                    DB::table('iptable')->insert($ips);
                }
            }
            if ($user->password_elastix != $data['password_sip']) {
                $this->apiElastixProcessing2('changeUser', [
                    'login' => $data['login_sip'],
                    'password_sip' => $data['password_sip'],
                ]);
            }
            if ($user->nat != $data['nat']) {
                $this->apiElastixProcessing2('changeUser', [
                    'login' => $data['login_sip'],
                    'nat' => $data['nat'] ? 'yes' : 'no'
                ]);
            }
            $update = [
                'password_elastix' => $this->getMD5Password($data['password_sip']),
                'nat' => $data['nat'],
            ];
            DB::table($this->table)
                ->where('id', $id)
                ->update($update);
            $result['status'] = 1;
            return $result;
        }
    }

    public function searchUsersByWord( $term, $company_id, $role_id = null, $rank_id = null )
    {
        $users = DB::table('users')
            ->select('id', 'surname', 'name')
            ->where(function ( $query ) use ( $term ) {
                $query->where('surname', 'LIKE', '%' . $term . '%')
                    ->orWhere('name', 'LIKE', '%' . $term . '%')
                    ->orWhere('login', 'LIKE', '%' . $term . '%')
                    ->orWhere('id', 'LIKE', '%' . $term . '%');
            });
        if ($company_id) {
            if (is_array($company_id)) {
                $users->whereIn('company_id', $company_id);
            } else {
                $users->where('company_id', $company_id);
            }
        }
        if ($role_id) {
            if (is_array($role_id)) {
                $users->whereIn('role', $role_id);
            } else {
                $users->where('role', $role_id);
            }
        }
        if ($rank_id) {
            if (is_array($rank_id)) {
                $users->whereIn('rank_id', $rank_id);
            } else {
                $users->where('rank_id', $rank_id);
            }
        }
        return $users->get();
    }

    /**
     * @param $roleNames string | array
     * @return bool
     */
    public function hasRole( $roleNames )
    {
        return (is_array($roleNames))
            ? in_array($this->role->name, $roleNames)
            : ($this->role->name == $roleNames);
    }

    /**
     * @param $permissionNames string | array
     * @return bool
     */
    // если пришёл массив, отвечаем true, если хоть один есть
    public function hasPermission( $permissionNames )
    {
        return $this->hasPermissionTo(Permission::where('name', $permissionNames)->first());
    }

    /**
     * @param $moving Moving
     * @return bool
     */
    // является ли текущий юзер получателем движения (или юзером с расширенными правами)
    public function isReceiverHandling( $moving )
    {
        return (!$this->project_id
            || ($moving->receiver_id
                && ($this->project_id == $moving->receiver->parent_id)
                && (!$this->sub_project_id || ($this->sub_project_id == $moving->receiver_id))
            )
            || ($moving->sender_id
                && !$this->sub_project_id
                && ($moving->sender->parent_id == $this->project_id))
        );
    }

    /**
     * @param $moving Moving
     * @return bool
     */
    // является ли текущий юзер отправителем движения (или юзером с расширенными правами)
    public function isSenderHandling( $moving )
    {
        return (!$this->project_id
            || ($moving->sender_id
                && ($this->project_id == $moving->sender->parent_id)
                && (!$this->sub_project_id || $this->sub_project_id == $moving->sender_id)
            )
            || ($moving->receiver_id
                && !$this->sub_project_id
                && ($moving->receiver->parent_id == $this->project_id))
        );
    }

    function changeOperatorQueues( $agent, $queues )
    {
        file_get_contents($this->urlApiElastix4 . "action=changeQueuesDetails&key=$this->keyApiElastix&id=$queues&agent=$agent", false, stream_context_create($this->contextOptions));
        file_get_contents($this->urlApiElastix4 . "action=reloadConfig&key=$this->keyApiElastix", false, stream_context_create($this->contextOptions));
        //write log to file in storage  app
        try {
            $newCampaign = Campaign::find($queues);
            $operator = User::where('login_sip', $agent)->first();

            // log changed data with queque and operator to storage/logs/info.log
            $currentUser = auth()->user();

            Storage::append('info.log', Carbon::now()->format('Y-m-d H:i:s') . ' ' . $currentUser->name . ' ' . $currentUser->surname . ' изменил данные: Оператор ' . $operator->name
                . ' ' . $operator->surname . '(' . $operator->id . ') распределено в ' . (isset($newCampaign->name) ? $newCampaign->name : 'Не назначено') . ' (campaign_id: ' . (isset($newCampaign->id) ? $newCampaign->id : '0') . ')');
        } catch (\Exception $exception) {
            Storage::append('info.log', $exception->getMessage());
        }

        return DB::table($this->table)
            ->where('login_sip', $agent)
            ->update(['campaign_id' => $queues]);
    }

    function getOperatorsData( $data )
    {
        $userIds = [];
        if ($data) {
            foreach ($data as $row)
                $userIds[] = $row['user'];
        }
        $result = collect(User::select('login_sip', 'name', 'surname')->whereIn('login_sip', $userIds)->get())->keyBy('id');
        return $result;

    }

    function setDataFromArray( $array )
    {
        try {
            if (count($array)) {
                foreach ($array as $property => $value) {
                    if (isset($this->getAttributes()[$property]) && $value) {
                        $this->$property = $value;
                    }
                }
            }
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function userPermissions( $user )
    {
        return DB::table('permissions AS p')
            ->select('p.name')
            ->leftJoin('role_permissions AS rp', 'rp.permission_id', '=', 'p.id')
            ->leftJoin('users_permissions AS up', 'up.permission_id', '=', 'p.id')
            ->whereIn('rp.role_id', $user->roles->pluck('id')->toArray())
            ->orWhere('up.user_id', $user->id)
            ->get();
    }

    public function setUsetPermission( $permissionId )
    {
        $data = request();
        $userPermisisons = DB::table('users_permissions')->where('user_id', $data->user_id)->pluck('permission_id')->toArray();
        if (!in_array($permissionId, $userPermisisons)) {
            DB::table('users_permissions')
                ->insert(['user_id' => $data->user_id, 'permission_id' => $permissionId]);
            return true;
        }else{
            DB::table('users_permissions')->where('user_id', $data->user_id)
                ->where('permission_id', $permissionId)->delete();
        }
    }
}