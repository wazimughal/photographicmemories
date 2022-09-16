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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('groom_first_name')->nullable();
            $table->string('groom_last_name')->nullable();
            $table->string('groom_email')->nullable();
            $table->string('groom_contact_number')->nullable();
            $table->text('groom_billing_address')->nullable();
            $table->string('bride_first_name')->nullable();
            $table->string('bride_last_name')->nullable();
            $table->string('bride_email')->nullable();
            $table->string('bride_contact_number')->nullable();
            $table->text('bride_billing_address')->nullable();
            $table->string('event_date_time')->nullable();
            $table->text('booking_notes')->nullable();
            $table->string('payment_source')->nullable();
            $table->unsignedBigInteger('venue_group_id');
            $table->foreign('venue_group_id')->references('id')->on('venue_groups');
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->unsignedBigInteger('package_id');
            //$table->foreign('orders_package_id')->references('id')->on('packages');

            $table->string('who_is_paying')->nullable();
            $table->string('customer_to_pay')->nullable();
            $table->string('venue_group_to_pay')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->unsignedBigInteger('created_uid');
            $table->foreign('created_uid')->references('id')->on('users');
            
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
        Schema::dropIfExists('booking');
    }
};
