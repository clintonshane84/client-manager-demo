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
        Schema::create('identities', function (Blueprint $table) {
            // Create Table Columns
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedSmallInteger('identity_type_id');
            $table->string('value_encrypted');
            $table->char('value_blind_index', 64)->nullable()->index();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('identity_type_id')->references('id')->on('identity_types')->cascadeOnDelete();

            // Unique Keys
            $table->unique(['user_id', 'identity_type_id']);

            return $table;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('identities');
    }
};
