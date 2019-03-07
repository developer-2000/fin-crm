<?php

use Illuminate\Database\Seeder;

class UpdateRoles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::all();

        foreach ($users as $user) {
            $query = \Illuminate\Support\Facades\DB::table('users_roles')
                ->where('user_id', $user->id)
                ->where('role_id', $user->role_id);
            if (!$query->exists()) {
                $query->insert([
                    'user_id' => $user->id,
                    'role_id' => $user->role_id,
                ]);
            }
        }
    }
}
