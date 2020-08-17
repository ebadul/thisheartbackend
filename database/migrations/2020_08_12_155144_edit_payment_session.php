<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditPaymentSession extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        
        Schema::table('payment_sessions', function (Blueprint $table) {
            $table->tinyInteger('validated')->default(0)->after('payment_details_id');
            $table->string('mode')->nullable()->after('payment_details_id');
            $table->string('customer')->nullable()->after('payment_details_id');
            $table->string('setup_intent')->nullable()->after('payment_details_id');
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
