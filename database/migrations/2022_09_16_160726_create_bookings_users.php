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
        Schema::create('bookings_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings');
            $table->unsignedBigInteger('group_id'); // used for User type (admin,customer,photographer etc)
            $table->string('slug'); // any shortcode to detect the action e.g admin_added_new_booking
            $table->string('description')->nullable(); // any shortcode to detect the action e.g admin_added_new_booking
            $table->tinyInteger('status')->nullable()->default(0); // 0: new Booking and waiting for Photographer Accept or Declined , 1:accepted, 2: rejeecteed by Photographer
            $table->string('photographer_expense')->nullable();
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
        Schema::dropIfExists('venue_users');
    }
};
