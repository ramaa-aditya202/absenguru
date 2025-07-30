{{-- Partial untuk tabel detail absensi --}}
<table class="min-w-full bg-white border">
    <thead class="bg-gray-200">
        <tr>
            <th class="py-2 px-4 border-b">Tanggal</th>
            <th class="py-2 px-4 border-b">Guru</th>
            <th class="py-2 px-4 border-b">Status</th>
            <th class="py-2 px-4 border-b">Keterangan</th>
            <th class="py-2 px-4 border-b">Diabsen oleh</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($attendances as $attendance)
            <tr class="hover:bg-gray-50">
                <td class="py-2 px-4 border-b">{{ \Carbon\Carbon::parse($attendance->attendance_date)->translatedFormat('d F Y') }}</td>
                <td class="py-2 px-4 border-b">{{ $attendance->user->name }}</td>
                <td class="py-2 px-4 border-b text-center">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @switch($attendance->status)
                            @case('hadir') bg-green-100 text-green-800 @break
                            @case('sakit') bg-yellow-100 text-yellow-800 @break
                            @case('izin') bg-blue-100 text-blue-800 @break
                            @case('alpa') bg-red-100 text-red-800 @break
                        @endswitch">
                        {{ ucfirst($attendance->status) }}
                    </span>
                </td>
                <td class="py-2 px-4 border-b">{{ $attendance->remarks }}</td>
                <td class="py-2 px-4 border-b">{{ $attendance->recorder->name }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="py-4 px-4 border-b text-center text-gray-500">
                    Tidak ada data absensi yang cocok dengan filter yang diterapkan.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Pagination --}}
<div class="mt-4" id="pagination-container">
    {{ $attendances->withQueryString()->links() }}
</div>
