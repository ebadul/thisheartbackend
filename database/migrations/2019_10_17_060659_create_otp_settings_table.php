<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtpSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otp_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->enum('otp_method',['sms','email','googleauth']);
            $table->boolean('otp_enable')->default(false);
            $table->string('google_key')->nullable();
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
        Schema::dropIfExists('otp_settings');
    }
}
