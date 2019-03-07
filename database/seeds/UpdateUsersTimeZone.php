<?php

use Illuminate\Database\Seeder;

class UpdateUsersTimeZone extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table(\App\Models\User::tableName())
            ->where('ban',0)
            ->where('time_zone', '!=', 'Europe/Kiev')
            ->update(['time_zone' => 'Europe/Kiev']);
    }
}
