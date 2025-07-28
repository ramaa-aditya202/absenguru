<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::latest()->paginate(10);
        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.subjects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Subject::create($request->all());
        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $subject->update($request->all());
        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
