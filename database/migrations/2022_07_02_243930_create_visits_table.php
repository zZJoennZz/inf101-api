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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->date('visit_date');
            $table->time('time_in');
            $table->time('time_out');
            $table->longText('service_id');
            $table->unsignedBigInteger('visit_type');
            $table->decimal('visit_type_fee');
            $table->decimal('subtotal');
            $table->unsignedBigInteger('discount_type')->nullable()->default(0);
            $table->decimal('discount_amount');
            $table->decimal('discount_type_others');
            $table->text('discount_others');
            $table->decimal('total_amount');
            $table->decimal('points');
            $table->unsignedBigInteger('hd_representative');
            $table->unsignedBigInteger('wc_representative');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients');
            // $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('visit_type')->references('id')->on('visit_types');
            $table->foreign('discount_type')->references('id')->on('discounts');
            $table->foreign('hd_representative')->references('id')->on('users');
            $table->foreign('wc_representative')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visits');
    }
};
