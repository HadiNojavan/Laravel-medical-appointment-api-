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
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->string('rrule');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('slot_duration_minutes');
            $table->date('valid_from');
            $table->date('valid_until')->nullable();
            $table->string('timezone', 64)->default('Asia/Tehran');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            /*
             * //here when we want to get Availability::where('doctor_id', 5)->where('is_active', true)->get();
             * it will be slow but by using index it will faster to find them
             */
            $table->index(['doctor_id', 'is_active']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
