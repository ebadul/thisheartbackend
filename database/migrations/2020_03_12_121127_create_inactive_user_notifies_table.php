<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInactiveUserNotifiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inactive_user_notifies', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id');
            $table->dateTime('last_login');
            $table->dateTime('first_send_email');
            $table->dateTime('second_send_email');
            $table->dateTime('send_sms');
            $table->dateTime('make_phone_call');
            $table->dateTime('send_email_beneficiary_user');
            $table->dateTime('send_sms_beneficiary_user');
            $table->dateTime('final_make_call');
            $table->text('notes');
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
        Schema::dropIfExists('inactive_user_notifies');
    }
}
