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
        Schema::table('users', function (Blueprint $table) {
            // Mengubah kolom role untuk memastikan enum mendukung 'piket'
            $table->enum('role', ['admin', 'guru', 'piket'])->default('guru')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Mengembalikan ke enum lama jika perlu rollback
            $table->enum('role', ['admin', 'guru'])->default('guru')->change();
        });
    }
};
