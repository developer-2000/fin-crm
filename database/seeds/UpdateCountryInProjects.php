<?php

use Illuminate\Database\Seeder;
use App\Models\Project;

class UpdateCountryInProjects extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countryData = [
            15 => 1,
            16 => 2,
            17 => 2,
            18 => 8,
            19 => 3,
            20 => 11,
            21 => 2,
            22 => 3,
            23 => 1,
            24 => 8,
            25 => 11,
            26 => 1,
            27 => 2,
        ];

        foreach ($countryData as $key => $row) {
            Project::where('id', $key)->update(['country_id' => $row]);
        }
    }
}
