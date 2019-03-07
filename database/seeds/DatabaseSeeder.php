<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //   $this->call(TargetConfigSeeder::class);
        //   $this->call(CompaniesPricesSeeder::class);
        //   $this->call(UpdateTargetValueSeeder::class);
       // $this->call(CategoriesSeeder::class);
       // $this->call(AddedProcStatus::class);
       // $this->call(PermissionSectionTableSeeder::class);
        //   $this->call(UpdateProjects::class);
        //   $this->call(UpdateCountryInProjects::class);
      //  $this->call(FillIntegrationCodesStatusesTable::class);
     //   $this->call(TranslateSeeder::class);
        $this->call(FillUsersRolesTable::class);
    }
}
