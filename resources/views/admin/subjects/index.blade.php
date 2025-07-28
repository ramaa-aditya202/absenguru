{{-- resources/views/admin/subjects/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Mata Pelajaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Tombol untuk menambah data baru --}}
                    <div class="mb-4">
                        <a href="{{ route('admin.subjects.create') }}">
                            <x-primary-button>
                                {{ __('Tambah Mata Pelajaran') }}
                            </x-primary-button>
                        </a>
                    </div>
                    
                    {{-- Pesan Sukses --}}
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Tabel untuk menampilkan data --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4 border-b">No</th>
                                    <th class="py-2 px-4 border-b">Nama Mata Pelajaran</th>
                                    <th class="py-2 px-4 border-b">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($subjects as $index => $subject)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b text-center">{{ $loop->iteration + $subjects->firstItem() - 1 }}</td>
                                        <td class="py-2 px-4 border-b">{{ $subject->name }}</td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <div class="flex justify-center space-x-2">
                                                {{-- Tombol Edit --}}
                                                <a href="{{ route('admin.subjects.edit', $subject) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm">Edit</a>
                                                
                                                {{-- Tombol Hapus --}}
                                                <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 px-4 border-b text-center text-gray-500">
                                            Tidak ada data mata pelajaran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Link Paginasi --}}
                    <div class="mt-4">
                        {{ $subjects->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>