<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditBillingDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_details', function (Blueprint $table) {
            $table->string('cron_payment_charging_id')->nullable()->after('paid_status');
            $table->enum('process_stauts',['pending','fail','success'])->default('pending')->after('paid_status');
            $table->date('process_date')->nullable()->after('paid_status');
            $table->tinyInteger('payment_process_times')->default(0)->after('paid_status');
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
