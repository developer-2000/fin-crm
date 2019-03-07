<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSupportPhoneInKazpostSenders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kazpost_senders', function (Blueprint $table) {
            $table->string('support_phone')->after('document')->nullable();

            $columns = [
                'code',
                'doc',
                'doc_num',
                'doc_day',
                'doc_month',
                'doc_year',
                'doc_body',
                'payment_code',
                'document',
            ];

            foreach ($columns as $column) {
                $table->string($column)->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kazpost_senders', function (Blueprint $table) {
            //
        });
    }
}
