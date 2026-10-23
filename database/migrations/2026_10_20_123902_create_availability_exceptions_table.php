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
        Schema::create('availability_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->timestamp('start_at');   // UTC
            $table->timestamp('end_at');     // UTC
            $table->string('reason')->nullable();
            $table->timestamps();

            // the slot builder looks up leaves by doctor and time range
            $table->index(['doctor_id', 'start_at']);

        });
        DB::statement('ALTER TABLE availability_exceptions ADD CONSTRAINT leave_range_check CHECK (end_at > start_at)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability_exceptions');
    }
};
