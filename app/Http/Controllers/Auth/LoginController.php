<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use \App\Models\User;
use App\Models\UsersTime;
use App\Models\Sessions;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    private $salt = 'IAmPartOfThatPower';
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    protected $username = 'login';
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'login';
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {

        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        //delete methods with md5 after transformation password into laravel hsh(bcrypt)
        $password = $this->getHashPassword($request->password);

        $user = \DB::table('users')->select('id', 'ban', 'name', 'surname', 'role_id', 'password_updated')
            ->where('login', $request->only('login'))
            ->where('password', $password)
            ->first();
        //delete methods with md5 after transformation password into laravel hsh(bcrypt)
        if ($user && !$user->password_updated) {
            $laravelHashedPassword = \Hash::make($request->password);
            if (User::where('login', $request->input('login'))->update([
                'password'         => $laravelHashedPassword,
                'password_updated' => 1
            ])) {
                return $this->sendLoginResponse($request);
            }
        }

        if ($this->attemptLogin($request)) {
            if (auth()->user()->ban == 1) {
                $request->session()->flush();

                return redirect()->route('login')->with('message', 'Пользователь заблокирован!');
            } else {

                $usersTimeModel = new UsersTime();

                /**
                 * удаляем старую сессия
                 * и создаме новую
                 */

                if ($request->isMethod('post') && $request->newSession) {
                    $usersTimeModel->setTime(['datetime_end' => now()], auth()->user()->id, 'crm');
                    (new Sessions)->deleteOtherSession($request->session()->getId(), auth()->user()->id);
                    /**
                     * записываем время
                     */
                    $timeData = [
                        'user_id'        => auth()->user()->id,
                        'type'           => 'crm',
                        'datetime_start' => now()
                    ];
                    $usersTimeModel->addTime($timeData);
                    return response()->json(['new_session' => true]);
                }

                /**
                 * проверяем на количество сессий на одного пользователя
                 */
                if (count((new Sessions)->getAllSessionOneUser(auth()->user()->id))) {
                    $request->session()->flush();
                    return redirect()->route('login')->with('sessions', true)->withInput();
                }

                if (!count($usersTimeModel->getUserTime(auth()->user()->id, 'crm'))) {
                    /**
                     * записываем время
                     */
                    $timeData = [
                        'user_id'        => auth()->user()->id,
                        'type'           => 'crm',
                        'datetime_start' => now()
                    ];
                    $usersTimeModel->addTime($timeData);
                }

                $this->clearLoginAttempts($request);

                return $this->authenticated($request, $this->guard()->user())
                    ?: redirect()->intended($this->redirectPath());
            }
        }
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    //delete methods with md5 after transformation password into laravel hsh(bcrypt)
    private function getHashPassword($password)
    {
        return md5(md5($this->salt . $password));
    }

    public function logout(Sessions $sessionsModel, UsersTime $usersTimeModel, Request $request)
    {
        if (auth()->user()) {
            $usersTimeModel->setTime(['datetime_end' => now()], auth()->user()->id, 'crm');
            $sessionsModel->deleteAllSessionsOneUser(auth()->user()->id);
            $request->session()->flush();
        }

        return redirect()->route('index');
    }
}
