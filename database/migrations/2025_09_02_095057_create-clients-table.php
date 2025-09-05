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
        Schema::create('clients', function (Blueprint $table) {
            // Create Table Columns
            $table->id();
            $table->string('name');
            $table->string('surname')->default('');
            $table->string('email')->unique();
            $table->string('mobile')->nullable();
            $table->date('birth_date')->nullable();
            $table->unsignedSmallInteger('language_id');
            $table->timestamps();

            // Create Foreign Keys
            $table->foreign('language_id')->references('id')->on('languages');

            return $table;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
