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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->text('client_id');
            $table->text('first_name');
            $table->text('middle_name');
            $table->text('last_name');
            $table->text('suffix')->nullable();
            $table->tinyInteger('gender');
            $table->date('birthday');
            $table->text('address');
            $table->text('barangay');
            $table->text('city');
            $table->text('province');
            $table->text('region');
            $table->text('zip_code');
            $table->text('contact_number');
            $table->text('email_address');
            $table->text('facebook');
            $table->text('instagram');
            $table->longText('maintenance');
            $table->text('signature');
            $table->text('image');
            $table->unsignedBigInteger('added_by');
            $table->timestamps();

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
        Schema::dropIfExists('clients');
    }
};
