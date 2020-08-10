<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('package_id');
            $table->string('billing_month');
            $table->float('package_cost');
            $table->string('payment_type');
            $table->string('recurring_type');
            $table->text('stripe_session_id')->nullable();
            $table->string('billing_date')->nullable();
            $table->string('next_billing_date')->nullable();
            $table->string('paid_status')->default(0);
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
        Schema::dropIfExists('billing_details');
    }
}
