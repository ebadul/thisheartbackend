<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddValidateCodeBeneficiary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('beneficiaries', function($table) {
            $table->integer('validate_code')->default(0)->after('invite_code');
            $table->string('access_url')->after('validate_code');
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
        Schema::table('beneficiaries', function($table) {
            $table->dropColumn('validate_code');
        });
    }
}
