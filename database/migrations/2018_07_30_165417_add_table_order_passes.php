<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableOrderPasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_passes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->index();
            $table->integer('pass_id')->index();
            $table->decimal('cost_return', 20, 10)->nullable();
            $table->timestamps();
        });
        $orders = \App\Models\Order::where('pass_id', '>', 0)->get();
        if ($orders) {
            foreach ($orders as $order) {
                $orderPass = new \App\Models\OrdersPass();
                $orderPass->pass_id = $order->pass_id;
                $orderPass->order_id = $order->id;
                $orderPass->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders_passes');
    }
}
