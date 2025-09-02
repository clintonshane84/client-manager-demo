<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('identity_types', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code', 20);
            $table->string('name', 30);
            $table->string('validation_pattern', 200);
            $table->boolean('is_sensitive')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identity_types');
    }
};
