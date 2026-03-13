@extends('layouts.app')
@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-white">Perangkat IoT</h1>
        <p class="text-sm text-slate-400 mt-1">Daftar perangkat ESP32 yang terhubung ke sistem</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($devices as $device)
        <div class="card-glass rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl {{ $device->type === 'sensor' ? 'bg-cyan-500/20' : 'bg-amber-500/20' }} flex items-center justify-center">
                    @if($device->type === 'sensor')
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    @else
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    @endif
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full {{ $device->is_online ? 'badge-online' : 'badge-offline' }} flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full {{ $device->is_online ? 'bg-emerald-400 pulse-dot' : 'bg-red-400' }}"></span>
                    {{ $device->is_online ? 'Online' : 'Offline' }}
                </span>
            </div>

            <h3 class="text-lg font-semibold text-white mb-1">{{ $device->name }}</h3>
            <p class="text-xs text-slate-500 font-mono mb-4">{{ $device->device_id }}</p>

            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Tipe</span>
                    <span class="text-white capitalize">{{ $device->type === 'sensor' ? 'Sensor' : 'Aktuator' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Heartbeat Terakhir</span>
                    <span class="text-white text-xs">{{ $device->last_heartbeat?->diffForHumans() ?? 'Belum pernah' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Total Data Sensor</span>
                    <span class="text-white">{{ number_format($device->sensor_data_count) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Anomali Terdeteksi</span>
                    <span class="text-white">{{ $device->anomalies_count }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Total Log</span>
                    <span class="text-white">{{ number_format($device->logs_count) }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 card-glass rounded-2xl p-12 text-center">
            <svg class="w-12 h-12 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
            <p class="text-slate-500">Belum ada perangkat IoT yang terdaftar.</p>
            <p class="text-xs text-slate-600 mt-1">Perangkat akan muncul otomatis saat ESP32 mengirim data pertama.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
