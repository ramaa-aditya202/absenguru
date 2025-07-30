{{-- Partial untuk rekapitulasi summary --}}
<div class="p-4 bg-green-100 rounded-lg">
    <div class="text-3xl font-bold text-green-800">{{ $summary['hadir'] ?? 0 }}</div>
    <div class="text-sm text-green-600">Hadir</div>
</div>
<div class="p-4 bg-yellow-100 rounded-lg">
    <div class="text-3xl font-bold text-yellow-800">{{ $summary['sakit'] ?? 0 }}</div>
    <div class="text-sm text-yellow-600">Sakit</div>
</div>
<div class="p-4 bg-blue-100 rounded-lg">
    <div class="text-3xl font-bold text-blue-800">{{ $summary['izin'] ?? 0 }}</div>
    <div class="text-sm text-blue-600">Izin</div>
</div>
<div class="p-4 bg-red-100 rounded-lg">
    <div class="text-3xl font-bold text-red-800">{{ $summary['alpa'] ?? 0 }}</div>
    <div class="text-sm text-red-600">Alpa</div>
</div>
