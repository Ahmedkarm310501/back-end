<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('Quantity')->default(1);
            $table->string('details');
            $table->string('photo')->nullable();
            $table->decimal('price', 13, 4)->default(0);
            $table->timestamps();
        });


        DB::table('products')->insert([
            ['name'=>"iphone20",
            'Quantity'=>"150",
            'details'=>"hjwhjbw ewrg3eg egh",
            'price'=>"150000",
            'photo'=>'uploads/product_image/1653483518.jpg'
            ]
        ]);
        DB::table('products')->insert([
            ['name'=>"android20",
            'Quantity'=>"800",
            'details'=>"hjwhwmkwsmfw wsjbw ewrg3eg egh",
            'price'=>"7890000",
            'photo'=>'uploads/product_image/1653483621.jpg'
            ]
        ]);
        DB::table('products')->insert([
            ['name'=>"black berry 20 ",
            'Quantity'=>"800",
            'details'=>"hjwhwmkwsmfw wsjbw ewrg3eg egh",
            'price'=>"780",
            'photo'=>'uploads/product_image/1653496697.jpg'
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
        Schema::dropIfExists('products');
    }
}
