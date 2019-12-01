<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('users', 'email_verified'))
        {
            Schema::table('users', function (Blueprint $table)
            {
                $table->dropColumn('email_verified');
            });
        }

        Schema::table('users', function (Blueprint $table) {

            $table->string('mobile')->nullable()->change();
            $table->smallInteger('active')->default(0)->change();
            $table->tinyInteger('email_verified')->default(0)->after('mobile');
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
