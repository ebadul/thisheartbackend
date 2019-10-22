<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;
class CreateOtpCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('otp_code');
            $table->boolean('verified')->default(false);
            $table->boolean('expired')->default(false);
            $table->dateTime('expired_time')->default(Carbon::now()->addSeconds(30));
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
        Schema::dropIfExists('otp_codes');
    }
}
