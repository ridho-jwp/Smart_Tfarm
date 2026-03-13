@extends('layouts.app')
@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Kontrol Pompa</h1>
        <p class="text-sm text-gray-500 mt-1">Kontrol saluran air, nutrisi, dan pembasmi hama (Admin Only)</p>
    </div>

    <!-- Pump Controls -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($devices as $device)
        <div class="card-glass rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $device->name }}</h3>
                    <p class="text-xs text-gray-400">{{ $device->device_id }}</p>
                </div>
                <span class="ml-auto text-xs px-2 py-1 rounded-full {{ $device->is_online ? 'badge-online' : 'badge-offline' }}">
                    {{ $device->is_online ? 'Online' : 'Offline' }}
                </span>
            </div>

            @php
                $isSpray = str_contains(strtolower($device->name), 'hama') || str_contains(strtolower($device->name), 'pembasmi');
                $onAction = $isSpray ? 'spray_on' : 'pump_on';
                $offAction = $isSpray ? 'spray_off' : 'pump_off';
            @endphp

            <div class="flex gap-3">
                <form method="POST" action="{{ route('control.toggle') }}" class="flex-1">
                    @csrf
                    <input type="hidden" name="device_id" value="{{ $device->id }}">
                    <input type="hidden" name="action" value="{{ $onAction }}">
                    <button type="submit" class="w-full btn-primary py-3 rounded-xl text-white font-semibold text-sm flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Nyalakan
                    </button>
                </form>
                <form method="POST" action="{{ route('control.toggle') }}" class="flex-1">
                    @csrf
                    <input type="hidden" name="device_id" value="{{ $device->id }}">
                    <input type="hidden" name="action" value="{{ $offAction }}">
                    <button type="submit" class="w-full btn-danger py-3 rounded-xl text-white font-semibold text-sm flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        Matikan
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-2 card-glass rounded-2xl p-12 text-center">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
            <p class="text-gray-400">Tidak ada perangkat actuator terdaftar.</p>
        </div>
        @endforelse
    </div>

    <!-- Recent Logs -->
    <div class="card-glass rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Log Kontrol Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Waktu</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Perangkat</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Aksi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentLogs as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-mono text-gray-600">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td class="px-5 py-3 text-gray-800">{{ $log->device?->name ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs px-2 py-1 rounded-full {{ in_array($log->action, ['pump_on', 'spray_on']) ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500' }}">
                                {{ in_array($log->action, ['pump_on', 'spray_on']) ? '⚡ Nyalakan' : '⛔ Matikan' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-500">{{ $log->user?->name ?? 'Sistem' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-gray-400">Belum ada log kontrol.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
