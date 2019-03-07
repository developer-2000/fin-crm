<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStageInProcStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proc_statuses', function (Blueprint $table) {
            $table->integer('stage')->index()->default(0)->after('target_final');
        });

        $update = [
            'sent' => 1,
            'paid-up' => 2,
            'refused' => 2,
            'rejected' => 2,
        ];

        foreach ($update as $action => $stage) {
            \App\Models\ProcStatus::where([
                ['project_id', 0],
                ['parent_id', 0],
                ['locked', 1],
                ['action', $action]
            ])->update(['stage' => $stage]);
        }

        $newStatus = new \App\Models\ProcStatus();
        $newStatus->project_id = 0;
        $newStatus->name = 'Отправка отменена';
        $newStatus->type = \App\Models\ProcStatus::TYPE_SENDERS;
        $newStatus->type = \App\Models\ProcStatus::TYPE_SENDERS;
        $newStatus->locked = 1;
        $newStatus->action = 'cancel_send';
        $newStatus->save();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proc_statuses', function (Blueprint $table) {
            //
        });
    }
}
