<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_entities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id');
            $table->string('entity_title');
            $table->string('entity_value');
            $table->string('entity_description');
            $table->string('entity_status');
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
        Schema::dropIfExists('package_entities');
    }
}
