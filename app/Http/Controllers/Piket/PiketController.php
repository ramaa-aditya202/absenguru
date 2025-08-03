<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\Schedule;
use Illuminate\Http\Request;

class PiketController extends Controller
{
    public function teachers()
    {
        $teachers = User::whereIn('role', ['admin', 'guru', 'piket'])
            ->orderBy('name')
            ->paginate(10);
            
        return view('piket.teachers.index', compact('teachers'));
    }

    public function subjects()
    {
        $subjects = Subject::orderBy('name')
            ->paginate(10);
            
        return view('piket.subjects.index', compact('subjects'));
    }

    public function classrooms()
    {
        $classrooms = Classroom::orderBy('name')
            ->paginate(10);
            
        return view('piket.classrooms.index', compact('classrooms'));
    }

    public function schedules()
    {
        $schedules = Schedule::with(['subject', 'teacher', 'classroom', 'timeSlot'])
            ->orderBy('day_of_week')
            ->orderBy('time_slot_id')
            ->paginate(15);
            
        return view('piket.schedules.index', compact('schedules'));
    }
}
