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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('title',255);
            $table->string('role',50);
            $table->string('slug',50);
            $table->text('description');
            $table->timestamps();
        });
                // Insert some stuff
            DB::table('groups')->insert(
                array(
                    [
                    'title' => 'Super Admin',
                    'role' => 'admin',
                    'slug' => phpslug('admin'),
                    'description' => 'This is the super admin role',
                    ],
                    [
                        'title' => 'Venue Group HOD',
                        'role' => 'venue_group_hod',
                        'slug' => phpslug('venue_group'),
                        'description' => 'Venue Group Head',
                    ],
                    [
                        'title' => 'Customer',
                        'role' => 'customer',
                        'slug' => phpslug('customer'),
                        'description' => 'This is the customer role',
                    ],
                    [
                        'title' => 'photographer',
                        'role' => 'photographer',
                        'slug' => phpslug('photographer'),
                        'description' => 'This is the photographer role',
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
        Schema::dropIfExists('groups');
    }
};
