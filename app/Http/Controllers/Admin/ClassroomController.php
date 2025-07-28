<?php
// app/Http/Controllers/Admin/ClassroomController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua ruang kelas.
     */
    public function index()
    {
        $classrooms = Classroom::latest()->paginate(10);
        return view('admin.classrooms.index', compact('classrooms'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan form untuk membuat kelas baru.
     */
    public function create()
    {
        return view('admin.classrooms.create');
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan kelas baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255|unique:classrooms,name',
        ]);

        // Buat data baru
        Classroom::create($request->all());

        // Arahkan kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.classrooms.index')->with('success', 'Ruang kelas berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan form untuk mengedit kelas.
     */
    public function edit(Classroom $classroom)
    {
        return view('admin.classrooms.edit', compact('classroom'));
    }

    /**
     * Update the specified resource in storage.
     * Memperbarui data kelas di database.
     */
    public function update(Request $request, Classroom $classroom)
    {
        // Validasi input, pastikan nama unik kecuali untuk data itu sendiri
        $request->validate([
            'name' => 'required|string|max:255|unique:classrooms,name,' . $classroom->id,
        ]);

        // Update data
        $classroom->update($request->all());

        // Arahkan kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.classrooms.index')->with('success', 'Ruang kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus data kelas dari database.
     */
    public function destroy(Classroom $classroom)
    {
        $classroom->delete();
        return back()->with('success', 'Ruang kelas berhasil dihapus.');
    }
}