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
        Schema::create('service_report_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("visit_id");
            $table->unsignedBigInteger("report_id");
            $table->longText("record");
            $table->unsignedBigInteger("added_by");
            $table->timestamps();

            $table->foreign('visit_id')->references('id')->on('visits');
            $table->foreign('report_id')->references('id')->on('service_reports');
            $table->foreign('added_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_report_records');
    }
};
