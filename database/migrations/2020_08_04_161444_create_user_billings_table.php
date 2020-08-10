<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_billings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id')->unique();
            $table->string('package_id');
            $table->float('package_cost');
            $table->date('subscribe_date');
            $table->date('expire_date')->nullable();
            $table->string('payment_type');
            $table->string('recurring_type');
            $table->tinyInteger('subscribe_status')->default(0);
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
        Schema::dropIfExists('user_billings');
    }
}
