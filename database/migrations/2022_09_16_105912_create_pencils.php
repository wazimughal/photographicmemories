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
        Schema::create('pencils', function (Blueprint $table) {
            $table->id();
            $table->id();
            $table->string('name');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('cnic')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('mobileno')->nullable()->unique();
            $table->string('homeaddress')->nullable();
            $table->string('unitnumber')->unique()->nullable();
            $table->string('country')->default('USA');
            $table->unsignedBigInteger('state_id')->default(1);
            $table->foreign('state_id')->references('id')->on('states');
            $table->unsignedBigInteger('city_id')->default(1);
            $table->foreign('city_id')->references('id')->on('cities');
            $table->unsignedBigInteger('zipcode_id')->default(1);
            $table->foreign('zipcode_id')->references('id')->on('zipcode');
            $table->string('password')->nullable();
            $table->string('profile_pic')->nullable();
            $table->tinyInteger('is_active')->default(0 );
            $table->tinyInteger('status')->default(0 );
            $table->tinyInteger('lead_type')->default(0 );
            $table->unsignedInteger('lead_added_by_uid')->nullable();
            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->unsignedInteger('venue_users_id')->nullable();
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
        Schema::dropIfExists('pencils');
    }
};
