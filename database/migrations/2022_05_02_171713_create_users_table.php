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
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
<<<<<<< HEAD:database/migrations/2014_10_12_000000_create_users_table.php
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('token')->nullable();

=======
            $table->boolean('status')->default(true);
            $table->boolean('Is_Admin')->default(false);
>>>>>>> d41871526548a20e72233919843b050815c79a80:database/migrations/2022_05_02_171713_create_users_table.php

            $table->timestamps();
        });

        DB::table('users')->insert([
            ['name'=>"mohamed",
            'email'=>'mo@gamil.com',
            'password'=>Hash::make('123'),
            'Is_Admin'=>true,
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
