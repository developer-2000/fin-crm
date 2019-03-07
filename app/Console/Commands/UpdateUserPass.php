<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UpdateUserPass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_user_pass';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user pass';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $usersActive = User::where('role_id', 11)->whereIn('project_id', [12])->where('sub_project_id', [21])->get();

           // $newPassElastix = md5(microtime().rand());

            foreach ($usersActive as $user){
                $newPass = md5(microtime().rand());
                $user->password = \Hash::make($newPass) ;
                $user->password_md5 = (new User())->getMD5Password($newPass);
//                if($user->login_sip && $user->login_sip != ''){
//                    $user->password_elastix = $this->getMD5Password($newPassElastix);
//                }
                $user->password_updated = 1;
                $user->save();

                //write log to file in storage  app
                try {

                    Storage::append('user-pass.log',
                    //    Carbon::now()->format('Y-m-d H:i:s').
                        $user->login . ' ; ' . $user->name .
                        ' ' . $user->surname .'; ' . $newPass .""
                       // ' ' . $user->surname .' pass_elastix: ' . $newPassElastix
                );
                } catch (\Exception $exception) {
                    Storage::append('info.log', $exception->getMessage());
                }
            }


        } catch (\Exception $exception) {
            echo $exception;
        }
    }
}
