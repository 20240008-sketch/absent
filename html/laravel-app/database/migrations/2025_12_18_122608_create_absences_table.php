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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->string('seito_id');
            $table->string('division');
            $table->text('reason');
            $table->time('scheduled_time')->nullable();
            $table->date('absence_date');
            $table->timestamps();
            
            $table->foreign('seito_id')->references('seito_id')->on('students')->onDelete('cascade');
            $table->index('seito_id');
            $table->index('absence_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
