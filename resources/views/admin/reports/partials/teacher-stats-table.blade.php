{{-- Partial untuk tabel persentase kehadiran per guru --}}
@if(request('sort_stats'))
    <p class="text-sm text-gray-600 mb-4">
        Diurutkan berdasarkan: <strong>{{ ucfirst(str_replace('_', ' ', request('sort_stats'))) }}</strong> 
        ({{ request('sort_direction') == 'asc' ? 'Ascending' : 'Descending' }})
        <a href="javascript:void(0)" onclick="resetSorting()" class="ml-2 text-blue-600 hover:text-blue-800">Reset Sorting</a>
    </p>
@endif
<div class="overflow-x-auto mb-6">
    <table class="min-w-full bg-white border">
        <thead class="bg-gray-200">
            <tr>
                <th class="py-2 px-4 border-b text-left">
                    <a href="javascript:void(0)" onclick="sortTable('name', '{{ ($sortBy == 'name' && $sortDirection == 'asc') ? 'desc' : 'asc' }}')" class="flex items-center hover:text-blue-600">
                        Nama Guru
                        @if($sortBy == 'name')
                            <span class="ml-1">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </a>
                </th>
                <th class="py-2 px-4 border-b text-center">
                    <a href="javascript:void(0)" onclick="sortTable('hadir', '{{ ($sortBy == 'hadir' && $sortDirection == 'asc') ? 'desc' : 'asc' }}')" class="flex items-center justify-center hover:text-blue-600">
                        Hadir
                        @if($sortBy == 'hadir')
                            <span class="ml-1">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </a>
                </th>
                <th class="py-2 px-4 border-b text-center">
                    <a href="javascript:void(0)" onclick="sortTable('sakit', '{{ ($sortBy == 'sakit' && $sortDirection == 'asc') ? 'desc' : 'asc' }}')" class="flex items-center justify-center hover:text-blue-600">
                        Sakit
                        @if($sortBy == 'sakit')
                            <span class="ml-1">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </a>
                </th>
                <th class="py-2 px-4 border-b text-center">
                    <a href="javascript:void(0)" onclick="sortTable('izin', '{{ ($sortBy == 'izin' && $sortDirection == 'asc') ? 'desc' : 'asc' }}')" class="flex items-center justify-center hover:text-blue-600">
                        Izin
                        @if($sortBy == 'izin')
                            <span class="ml-1">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </a>
                </th>
                <th class="py-2 px-4 border-b text-center">
                    <a href="javascript:void(0)" onclick="sortTable('alpa', '{{ ($sortBy == 'alpa' && $sortDirection == 'asc') ? 'desc' : 'asc' }}')" class="flex items-center justify-center hover:text-blue-600">
                        Alpa
                        @if($sortBy == 'alpa')
                            <span class="ml-1">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </a>
                </th>
                <th class="py-2 px-4 border-b text-center">
                    <a href="javascript:void(0)" onclick="sortTable('total', '{{ ($sortBy == 'total' && $sortDirection == 'asc') ? 'desc' : 'asc' }}')" class="flex items-center justify-center hover:text-blue-600">
                        Total Absensi
                        @if($sortBy == 'total')
                            <span class="ml-1">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </a>
                </th>
                <th class="py-2 px-4 border-b text-center">
                    <a href="javascript:void(0)" onclick="sortTable('percentage', '{{ ($sortBy == 'percentage' && $sortDirection == 'asc') ? 'desc' : 'asc' }}')" class="flex items-center justify-center hover:text-blue-600">
                        Persentase Kehadiran
                        @if($sortBy == 'percentage')
                            <span class="ml-1">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($teacherAttendanceStats as $stat)
                <tr class="hover:bg-gray-50">
                    <td class="py-2 px-4 border-b font-medium">{{ $stat['name'] }}</td>
                    <td class="py-2 px-4 border-b text-center">
                        <span class="px-2 py-1 rounded-full text-sm bg-green-100 text-green-800">
                            {{ $stat['hadir'] }}
                        </span>
                    </td>
                    <td class="py-2 px-4 border-b text-center">
                        <span class="px-2 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">
                            {{ $stat['sakit'] }}
                        </span>
                    </td>
                    <td class="py-2 px-4 border-b text-center">
                        <span class="px-2 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                            {{ $stat['izin'] }}
                        </span>
                    </td>
                    <td class="py-2 px-4 border-b text-center">
                        <span class="px-2 py-1 rounded-full text-sm bg-red-100 text-red-800">
                            {{ $stat['alpa'] }}
                        </span>
                    </td>
                    <td class="py-2 px-4 border-b text-center font-medium">{{ $stat['total'] }}</td>
                    <td class="py-2 px-4 border-b text-center">
                        <span class="px-2 py-1 rounded-full text-sm font-medium
                            @if($stat['percentage'] >= 90) bg-green-100 text-green-800
                            @elseif($stat['percentage'] >= 80) bg-yellow-100 text-yellow-800
                            @elseif($stat['percentage'] >= 70) bg-orange-100 text-orange-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $stat['percentage'] }}%
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
