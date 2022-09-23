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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('groom_name')->nullable();
            $table->string('groom_home_phone')->nullable();
            $table->string('groom_mobile')->nullable();
            $table->string('groom_email')->unique()->nullable();
            $table->text('groom_billing_address')->nullable();
            $table->string('bride_name')->nullable();
            $table->string('bride_home_phone')->nullable();
            $table->string('bride_mobile')->nullable();
            $table->string('bride_email')->unique()->nullable();
            $table->text('bride_billing_address')->nullable();
            $table->string('date_of_event')->nullable();
            $table->string('time_of_event')->nullable();
            $table->tinyInteger('is_active')->default(0 );
            $table->tinyInteger('status')->default(0); // hold, awaiting, pending,or any status
            $table->tinyInteger('preferred_photographer_id')->default(0); // hold, awaiting, pending,or any status
            $table->tinyInteger('photographer_status')->default(0)->nullable(); // if Photographer accepted : 1 reject 2 : 0 waiting for photographer response. (This can be ignored and managed through status field)
            $table->tinyInteger('deposit_needed')->default(0); // 0: No 1: YES
            $table->tinyInteger('collected_by_photographere')->default(0); // 0: No 1: YES
            $table->tinyInteger('paying_via')->default(0)->nullable(); // chequ:0, CreditCard:1, Zelle:2 photographer:3
            $table->tinyInteger('pencile_by')->default(0)->nullable(); // Offic:0, Venue Group:1,Website:2 
            $table->tinyInteger('who_is_paying')->default(0)->nullable(); // 0:custoemr 1:venue 2: both
            $table->string('venue_group_to_pay')->nullable(); // 0:custoemr 1:venue 2: both
            $table->string('customer_to_pay')->nullable(); // 0:custoemr 1:venue 2: both

            $table->string('other_venue_group')->nullable();
            $table->text('notes_for_photographer')->nullable();
            $table->text('notes_for_customer')->nullable();
            $table->text('notes_for_venue_group')->nullable();
            $table->text('notes_by_pencil')->nullable();
            $table->string('over_time')->nullable(); // Number of hours over time by Photographer
            $table->string('over_time_requested_by')->nullable(); // Name who asked for over time to Photographer
            $table->text('over_time_description')->nullable(); // description about who requested and what he said and what is more information about him/her(phone , etc)
            $table->unsignedBigInteger('city_id')->default(1);
            $table->foreign('city_id')->references('id')->on('cities');
            $table->unsignedBigInteger('package_id')->default(1);
            $table->foreign('package_id')->references('id')->on('packages');
            $table->unsignedBigInteger('customer_id')->default(1);
            $table->foreign('customer_id')->references('id')->on('users');
            $table->string('overtime_hours')->nullable();
            $table->string('overtime_rate_per_hour')->nullable();
            //$table->string('title_for_extra_price')->nullable(); // description about packages, e.g over time will charged for 200 USD per hours
            $table->string('extra_price')->nullable(); // description about packages, e.g over time will charged for 200 USD per hours
            $table->text('extra_charge_desc')->nullable(); // description about packages, e.g over time will charged for 200 USD per hours
            $table->string('total_cost')->nullable();
            $table->tinyInteger('created_by_user')->nullable(); // chequ:0, CreditCard:1, Zelle:2 photographer:3
            
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
        Schema::dropIfExists('bookings');
    }
};
