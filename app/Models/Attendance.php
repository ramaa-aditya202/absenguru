<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Atribut yang diizinkan untuk diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'schedule_id',
        'user_id',
        'attendance_date',
        'status',
        'remarks', // <-- Ini adalah kunci perbaikannya
        'recorded_by',
    ];

    /**
     * Mendapatkan data guru yang memiliki absensi ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mendapatkan data admin yang mencatat absensi ini.
     */
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Mendapatkan data jadwal yang terkait dengan absensi ini.
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
