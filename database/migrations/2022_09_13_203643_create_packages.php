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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('price')->nullable();
            $table->text('description')->nullable();
            $table->string('is_active')->default(1)->nullable();
            $table->tinyInteger('manually_added')->nullable();
            $table->timestamps();
        });
        DB::table('packages')->insert(
            array(
                [
                'name' => 'Pacakge 1',
                'price' => '200',
                'description' => 'EVENT PHOTOGRAPHY+ BRIDAL / COUPLE SHOOT /1 Digital Album 10 Pages - 80-90 Pics / Soft Pictures  -  200-300 Pics/ DSLR / HD Camera /1 Still Cam /1 Video Cam / Complete Event Edited video with Highlight',
                ],
                [
                'name' => 'Pacakge 2',
                'price' => '200',
                'description' => 'EVENT PHOTOGRAPHY+ BRIDAL / COUPLE SHOOT /1 Digital Album 10 Pages - 80-90 Pics / Soft Pictures  -  200-300 Pics/ DSLR / HD Camera /1 Still Cam /1 Video Cam / Complete Event Edited video with Highlight',
                ],
                [
                'name' => 'Pacakge 3',
                'price' => '200',
                'description' => 'EVENT PHOTOGRAPHY+ BRIDAL / COUPLE SHOOT /1 Digital Album 10 Pages - 80-90 Pics / Soft Pictures  -  200-300 Pics/ DSLR / HD Camera /1 Still Cam /1 Video Cam / Complete Event Edited video with Highlight',
                ],
                [
                'name' => 'Pacakge 4',
                'price' => '200',
                'description' => 'EVENT PHOTOGRAPHY+ BRIDAL / COUPLE SHOOT /1 Digital Album 10 Pages - 80-90 Pics / Soft Pictures  -  200-300 Pics/ DSLR / HD Camera /1 Still Cam /1 Video Cam / Complete Event Edited video with Highlight',
                ],
       
                [
                'name' => 'Pacakge 5',
                'price' => '200',
                'description' => 'EVENT PHOTOGRAPHY+ BRIDAL / COUPLE SHOOT /1 Digital Album 10 Pages - 80-90 Pics / Soft Pictures  -  200-300 Pics/ DSLR / HD Camera /1 Still Cam /1 Video Cam / Complete Event Edited video with Highlight',
                ],
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
        Schema::dropIfExists('packages');
    }
};
