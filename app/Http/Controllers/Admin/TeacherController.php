<?php
// app/Http/Controllers/Admin/TeacherController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua guru.
     */
    public function index()
    {
        // Ambil semua user dengan role 'guru' atau 'admin'
        $teachers = User::whereIn('role', ['guru', 'admin'])->latest()->paginate(10);
        return view('admin.teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan form untuk membuat guru baru.
     */
    public function create()
    {
        return view('admin.teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan data guru baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,guru'],
        ]);

        // Buat user baru
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
            'role' => $request->role,
        ]);

        return redirect()->route('admin.teachers.index')->with('success', 'Pengguna berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan form untuk mengedit guru.
     */
    public function edit(User $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     * Memperbarui data guru di database.
     */
    public function update(Request $request, User $teacher)
    {
        // Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($teacher->id)],
            'password' => ['nullable', 'string', 'min:8'], // Password opsional
            'role' => ['required', 'in:admin,guru'],
        ]);

        // Siapkan data untuk diupdate
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Jika password diisi, update passwordnya
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Update data pengguna
        $teacher->update($data);

        return redirect()->route('admin.teachers.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus data guru dari database.
     */
    public function destroy(User $teacher)
    {
        // Tambahkan proteksi agar tidak bisa menghapus diri sendiri
        if ($teacher->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }
        
        $teacher->delete();
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}