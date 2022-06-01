<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('token')->nullable();
            $table->boolean('Is_Admin')->default(0);
            $table->boolean('status')->default(0);
            $table->date('date_of_birth');
            $table->timestamps();
        });

        DB::table('users')->insert([
            ['name'=>"mohamed",
            'email'=>'mo@gmail.com',
            'password'=>Hash::make('aaa123'),
            'Is_Admin'=>true,
            'status'=>true,
            'date_of_birth'=>'2000-10-20',
            ]
        ]);

        DB::table('users')->insert([
            ['name'=>"ahmed",
            'email'=>'a@gmail.com',
            'password'=>Hash::make('aaa123'),
            'Is_Admin'=>false,
            'status'=>true,
            'date_of_birth'=>'2000-10-20',

            ]
        ]);

        DB::table('users')->insert([
            ['name'=>"youssef",
            'email'=>'y@gmail.com',
            'password'=>Hash::make('aaa123'),
            'Is_Admin'=>false,
            'status'=>true,
            'date_of_birth'=>'2000-10-20',

            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
