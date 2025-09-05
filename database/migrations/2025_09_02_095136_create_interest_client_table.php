<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('client_interest', function (Blueprint $table) {
            // Create Table Columns
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedSmallInteger('interest_id');

            // Create Foreign Keys
            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
            $table->foreign('interest_id')->references('id')->on('interests')->cascadeOnDelete();

            return $table;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('client_interest');
    }
};
