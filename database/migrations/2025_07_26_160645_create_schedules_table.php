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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Guru
   			$table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade'); // Mapel
    		$table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade'); // Kelas
    		$table->tinyInteger('day_of_week'); // 1 untuk Senin, 2 untuk Selasa, dst.
    		$table->time('start_time');
    		$table->time('end_time');
    		$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
