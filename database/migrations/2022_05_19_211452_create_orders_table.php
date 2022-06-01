<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->json("products");
            $table->decimal('total_price', 13, 4)->default(0);
            $table->boolean("status")->default(0);
            $table->boolean("delivery_type");
            $table->foreignId('user_id')->nullable();
            $table->foreign('user_id')->on('users')->references('id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreignId('address_id')->nullable();
            $table->foreign('address_id')->on('addresses')->references('id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
