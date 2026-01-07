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
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->string('seito_id');
            $table->string('parent_name');
            $table->string('parent_relationship');
            $table->string('parent_tel')->nullable();
            $table->string('parent_initial_email')->nullable();
            $table->string('parent_initial_password')->nullable();
            $table->string('parent_email')->unique();
            $table->string('parent_password');
            $table->timestamps();
            
            $table->foreign('seito_id')->references('seito_id')->on('students')->onDelete('cascade');
            $table->index('seito_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
