<?php

use Illuminate\Database\Seeder;

class FillUsersRolesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usersData = \App\Models\User::get(['id', 'role_id'])->toArray();
        foreach ($usersData as $userData) {
            $exists = DB::table('users_roles')
                ->where('user_id', $userData['id'])
                ->where('role_id', $userData['role_id'])
                ->exists();

            if (!$exists) {
                DB::table('users_roles')
                    ->insert([
                        'user_id'=> $userData['id'],
                        'role_id'=> $userData['role_id']
                        ]
                    );
            }
        }
    }
}
