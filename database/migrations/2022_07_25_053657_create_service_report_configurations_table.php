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
        Schema::create('service_report_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("service_id");
            $table->unsignedBigInteger("service_report_id");
            $table->integer("is_active")->default(1);
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('service_report_id')->references('id')->on('service_reports');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_report_configurations');
    }
};
