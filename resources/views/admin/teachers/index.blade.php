{{-- resources/views/admin/teachers/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Guru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-4">
                        <a href="{{ route('admin.teachers.create') }}">
                            <x-primary-button>{{ __('Tambah Guru') }}</x-primary-button>
                        </a>
                    </div>
                    
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded">{{ session('success') }}</div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4 border-b">No</th>
                                    <th class="py-2 px-4 border-b">Nama Guru</th>
                                    <th class="py-2 px-4 border-b">Email</th>
                                    <th class="py-2 px-4 border-b">Role</th>
                                    <th class="py-2 px-4 border-b">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($teachers as $index => $teacher)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b text-center">{{ $loop->iteration + $teachers->firstItem() - 1 }}</td>
                                        <td class="py-2 px-4 border-b">{{ $teacher->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $teacher->email }}</td>
                                        <td class="py-2 px-4 border-b text-center"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $teacher->role }}</span></td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('admin.teachers.edit', $teacher) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm">Edit</a>
                                                <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 px-4 border-b text-center text-gray-500">Tidak ada data guru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">{{ $teachers->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>