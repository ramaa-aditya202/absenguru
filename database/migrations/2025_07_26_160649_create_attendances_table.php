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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
   		 	$table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Guru yang diabsen
    		$table->date('attendance_date');
    		$table->enum('status', ['hadir', 'sakit', 'izin', 'alpa']);
    		$table->text('remarks')->nullable(); // Catatan, misal "Sakit, ada surat dokter"
    		$table->foreignId('recorded_by')->constrained('users'); // Siapa yang mengabsen (Admin)
    		$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
