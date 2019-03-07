<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPriceProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('price_products', 10, 0)->index()->default(0)->after('price_input');
            $table->tinyInteger('locked')->index()->default(0)->after('moderation_time');
        });
        \Illuminate\Support\Facades\DB::table(\App\Models\Order::tableName())
            ->update(['price_products' => \Illuminate\Support\Facades\DB::raw('price_total')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
