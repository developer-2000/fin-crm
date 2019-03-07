<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('countries');

        Schema::create('countries', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name', 255)->default('');
            $table->string('full_name', 255)->nullable();
            $table->string('capital', 255)->nullable();
            $table->string('citizenship', 255)->nullable();
            $table->string('country_code', 3)->default('');
            $table->string('currency', 255)->nullable();
            $table->string('currency_name', 255)->nullable();
            $table->string('currency_sub_unit', 255)->nullable();
            $table->string('currency_symbol', 3)->nullable();
            $table->integer('currency_decimals')->nullable();
            $table->string('code', 2)->default('')->comment('iso_3166_2 в нижнем регистре');
            $table->string('iso_3166_2', 2)->default('');
            $table->string('iso_3166_3', 3)->default('');
            $table->string('region_code', 3)->default('');
            $table->string('sub_region_code', 3)->default('');
            $table->boolean('eea')->default(0);
            $table->string('calling_code', 3)->nullable();
            $table->string('flag', 6)->nullable();

            $table->decimal('exchange_rate')->default('0')->index();
            $table->boolean('use')->default(0)->index()->comment('используется ли');
            $table->unsignedTinyInteger('sequence')->comment('последовательность, очерёдность показа');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });

        DB::statement("ALTER TABLE countries MODIFY country_code CHAR(3) NOT NULL DEFAULT ''");
        DB::statement("ALTER TABLE countries MODIFY code CHAR(2) NOT NULL DEFAULT ''");
        DB::statement("ALTER TABLE countries MODIFY iso_3166_2 CHAR(2) NOT NULL DEFAULT ''");
        DB::statement("ALTER TABLE countries MODIFY iso_3166_3 CHAR(3) NOT NULL DEFAULT ''");
        DB::statement("ALTER TABLE countries MODIFY region_code CHAR(3) NOT NULL DEFAULT ''");
        DB::statement("ALTER TABLE countries MODIFY sub_region_code CHAR(3) NOT NULL DEFAULT ''");

        Schema::table('countries', function(Blueprint $table) {
            $table->index('code');
        });

        $countries = json_decode(file_get_contents(__DIR__ . '/../countries.json'), true);
        $uses = [
            1 => 'UA', 2 => 'RU', 3 => 'KZ', 4 => 'BY', 5 => 'UZ', 6 => 'KG',
            7 => 'PL', 8 => 'VN', 9 => 'AZ', 10 => 'BG', 11 => 'IN'
        ];
        $rows = [];
        $i = 11;
        foreach ($countries as $country) {
            $sequence = in_array($country['iso_3166_2'], $uses)
                ? array_search($country['iso_3166_2'], $uses)
                : ++$i;
            $rows[] = [
                'name'              => $country['name'] ?? '',
                'full_name'         => $country['full_name'] ?? '',
                'capital'           => $country['capital'] ?? '',
                'citizenship'       => $country['citizenship'] ?? '',
                'country_code'      => $country['country-code'] ?? '',
                'currency'          => $country['currency_code'] ?? '',
                'currency_name'     => $country['currency'] ?? '',
                'currency_sub_unit' => $country['currency_sub_unit'] ?? '',
                'currency_symbol'   => $country['currency_symbol'] ?? '',
                'currency_decimals' => $country['currency_decimals'] ?? 0,
                'code'              => mb_strtolower($country['iso_3166_2']) ?? '',
                'iso_3166_2'        => $country['iso_3166_2'] ?? '',
                'iso_3166_3'        => $country['iso_3166_3'] ?? '',
                'region_code'       => $country['region_code'] ?? '',
                'sub_region_code'   => $country['sub_region_code'] ?? '',
                'eea'               => $country['eea'] ?? 0,
                'calling_code'      => $country['calling_code'] ?? '',
                'flag'              => $country['flag'] ?? '',

                'exchange_rate'     => 0,
                'use'               => in_array($country['iso_3166_2'], $uses),
                'sequence'          => $sequence,
            ];
        }

        function cmp($a, $b) {
            return ($a['sequence'] < $b['sequence']) ? -1 : 1;
        }
        usort($rows, "cmp");
        // чтобы сохранить id существующих ранее в таблице стран

        DB::table('countries')->insert($rows);



        DB::table('permissions')->insert([
            ['name' => 'countries', 'alias' => 'Страны', 'group' => 'menu'],
        ]);



        DB::statement("ALTER TABLE cold_call_files MODIFY geo CHAR(2) NOT NULL DEFAULT ''");
        Schema::table('cold_call_files', function(Blueprint $table) {
            $table->dropIndex(['geo']);
            $table->index('geo');
        });
        DB::statement("ALTER TABLE finance_transaction MODIFY geo CHAR(2) NOT NULL DEFAULT ''");
        Schema::table('finance_transaction', function(Blueprint $table) {
            $table->index('geo');
        });
        DB::statement("ALTER TABLE offers MODIFY geo CHAR(2) NOT NULL DEFAULT ''");
        Schema::table('offers', function(Blueprint $table) {
            $table->dropIndex(['geo']);
            $table->index('geo');
        });
        DB::statement("ALTER TABLE offers_products MODIFY geo CHAR(2) NOT NULL DEFAULT ''");
        Schema::table('offers_products', function(Blueprint $table) {
            $table->index('geo');
        });
        DB::statement("ALTER TABLE orders MODIFY geo CHAR(2) NOT NULL DEFAULT ''");
        Schema::table('orders', function(Blueprint $table) {
            $table->index('geo');
        });
        DB::statement("ALTER TABLE script_details MODIFY geo CHAR(2) NOT NULL DEFAULT ''");
        Schema::table('script_details', function(Blueprint $table) {
            $table->dropIndex(['geo']);
            $table->index('geo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
