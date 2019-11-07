<?php

use Illuminate\Database\Seeder;

class UserType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\UserType::create([
            'user_type'=>'admin'
        ]);
        App\UserType::create([
            'user_type'=>'primary'
        ]);
        App\UserType::create([
            'user_type'=>'beneficiary'
        ]);
    }
}
