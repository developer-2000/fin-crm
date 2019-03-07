<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIntegrationActiveInTargetConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('target_configs', function (Blueprint $table) {
            $table->tinyInteger('integration')->default(0)->index()->after('alias');
            $table->string('integration_status', 50)->index()->default(\App\Models\TargetConfig::INTEGRATION_INACTIVE)->after('integration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('target_configs', function (Blueprint $table) {
            //
        });
    }
}
