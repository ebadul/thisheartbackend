<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFreeAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('free_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->text('activation_code');
            $table->string('requested_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->tinyInteger('verified')->default(0);
            $table->enum('status',['pending','actived','denied']);
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
        Schema::dropIfExists('free_accounts');
    }
}
