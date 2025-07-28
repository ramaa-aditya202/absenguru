<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    // Di versi Laravel yang lebih baru, trait HasFactory seringkali sudah ada.
    // Jika tidak ada, Anda bisa menambahkannya seperti ini.
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'subject_id',
        'classroom_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    /**
     * Mendefinisikan relasi ke model User (Guru).
     */
	public function user() { 
        return $this->belongsTo(User::class); 
    }

    /**
     * Mendefinisikan relasi ke model Subject (Mata Pelajaran).
     */
	public function subject() { 
        return $this->belongsTo(Subject::class); 
    }

    /**
     * Mendefinisikan relasi ke model Classroom (Kelas).
     */
	public function classroom() { 
        return $this->belongsTo(Classroom::class); 
    }
}
