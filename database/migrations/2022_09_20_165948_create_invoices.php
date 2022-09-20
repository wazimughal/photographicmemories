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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('payee_name')->nullable();
            $table->string('description')->nullable();
            $table->string('paid_amount');
            $table->string('other')->nullable();
            $table->string('slug')->nullable();
            $table->tinyInteger('created_by')->nullable(0);
            $table->unsignedBigInteger('payee_uid');
            $table->foreign('payee_uid')->references('id')->on('users');
            $table->unsignedBigInteger('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings');
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
        Schema::dropIfExists('invoices');
    }
};
