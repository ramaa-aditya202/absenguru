{{-- resources/views/piket/teachers/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Guru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Info untuk role piket --}}
                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <strong>Mode Piket:</strong> Anda dapat melihat data guru namun tidak dapat menambah, mengedit, atau menghapus data.
                                </p>
                            </div>
                        </div>
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
                                    <th class="py-2 px-4 border-b">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($teachers as $index => $teacher)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b text-center">{{ $loop->iteration + $teachers->firstItem() - 1 }}</td>
                                        <td class="py-2 px-4 border-b">{{ $teacher->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $teacher->email }}</td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($teacher->role == 'admin') bg-red-100 text-red-800
                                                @elseif($teacher->role == 'piket') bg-yellow-100 text-yellow-800
                                                @else bg-blue-100 text-blue-800 @endif">
                                                {{ ucfirst($teacher->role) }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Aktif
                                            </span>
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
