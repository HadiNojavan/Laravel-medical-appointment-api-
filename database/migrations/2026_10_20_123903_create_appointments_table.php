<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->string('status')->default('pending');
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamp('reminded_at')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'start_at']);
            $table->index(['patient_id', 'start_at']);
            $table->index(['status', 'start_at']);
        });

        /*
         * now if our appointment is canceled
         *  other patient have to be able to reserve that time
         * our doctor_id and start_at will be unique on db
         */
        DB::statement("
        CREATE UNIQUE INDEX appointments_doctor_start_active_unique
        ON appointments (doctor_id, start_at)
        WHERE status <> 'cancelled'
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
