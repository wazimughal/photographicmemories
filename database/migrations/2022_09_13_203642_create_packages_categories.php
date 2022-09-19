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
        Schema::create('packages_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('is_active')->default(1);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
        // Insert some stuff
        DB::table('packages_categories')->insert(
            array(
                [
                'name' => 'Category 1',
                'slug' => phpslug('Category 1'),
                'is_active' => 1,
                'user_id' => 1,
                ],
                [
                'name' => 'Category 2',
                'slug' => phpslug('Category 2'),
                'is_active' => 1,
                'user_id' => 1,
                ],
                [
                'name' => 'Category 3',
                'slug' => phpslug('Category 3'),
                'is_active' => 1,
                'user_id' => 1,
                ]
                
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages_categories');
    }
};
