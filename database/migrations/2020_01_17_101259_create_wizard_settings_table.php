<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWizardSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wizard_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->tinyInteger('wiz_01')->default(0);
            $table->tinyInteger('wiz_02')->default(0);
            $table->tinyInteger('wiz_03')->default(0);
            $table->tinyInteger('wiz_04')->default(0);
            $table->tinyInteger('wiz_05')->default(0);
            $table->tinyInteger('wiz_06')->default(0);
            $table->tinyInteger('wiz_07')->default(0);
            $table->tinyInteger('wiz_08')->default(0);
            $table->tinyInteger('wiz_09')->default(0);
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
        Schema::dropIfExists('wizard_settings');
    }
}
