<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditPackageEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_entities', function (Blueprint $table) {
           if(Schema::hasColumn('package_entities','package_id')){
                $table->dropColumn('package_id');
           }
           if(Schema::hasColumn('package_entities','package_entities_id')){
                $table->dropColumn('package_entities_id');
           }
           if(Schema::hasColumn('package_entities','entity_status')){
                $table->dropColumn('entity_status');
           }
           if(Schema::hasColumn('package_entities','entity_title')){
                $table->dropColumn('entity_title');
           }
           if(Schema::hasColumn('package_entities','entity_value')){
                $table->dropColumn('entity_value');
           }
           if(Schema::hasColumn('package_entities','entity_description')){
                $table->dropColumn('entity_description');
           }
  
        });

        Schema::table('package_entities', function (Blueprint $table) {
           
         
            $table->integer('package_id');
            $table->string('package_entities_id');
            $table->string('entity_value')->nullable();
            $table->string('entity_status')->default(1);
            
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
