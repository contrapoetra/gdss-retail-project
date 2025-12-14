@extends('layouts.app')

@section('title', 'Input Penilaian')
@section('header', 'INPUT DATA PROTOCOL')

@section('content')

    {{-- LOAD ALPINE JS --}}
    <script src="//unpkg.com/alpinejs" defer></script>

    {{-- GLOBAL BACKGROUND GRID EFFECT (GREEN THEME) --}}
    <div class="fixed inset-0 pointer-events-none z-0" 
         style="background-image: linear-gradient(rgba(16, 185, 129, 0.05) 1px, transparent 1px), 
         linear-gradient(90deg, rgba(16, 185, 129, 0.05) 1px, transparent 1px); 
         background-size: 30px 30px; mask-image: radial-gradient(circle, black 60%, transparent 100%);">
    </div>

    {{-- NOTIFIKASI ALERT (CYBERPUNK STYLE) --}}
    @if(session('success'))
        <div class="relative mb-6 group overflow-hidden rounded border border-emerald-500/50 bg-emerald-900/20 backdrop-blur-md p-4 text-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.2)]">
            <div class="absolute inset-0 bg-emerald-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative flex items-center gap-3">
                <i class="fas fa-check-circle text-xl animate-pulse"></i>
                <div>
                    <p class="font-mono font-bold text-xs tracking-widest uppercase">SYSTEM SUCCESS</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="relative mb-6 group overflow-hidden rounded border border-red-500/50 bg-red-900/20 backdrop-blur-md p-4 text-red-400 shadow-[0_0_15px_rgba(239,68,68,0.2)]">
            <div class="absolute inset-0 bg-red-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-xl animate-pulse"></i>
                <div>
                    <p class="font-mono font-bold text-xs tracking-widest uppercase">SYSTEM ERROR</p>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- MAIN FORM CONTAINER --}}
    <div class="holo-card rounded-xl overflow-hidden relative border border-emerald-500/20 bg-[#0B1120]/90 backdrop-blur-md shadow-[0_0_30px_rgba(0,0,0,0.5)] z-10">
        
        {{-- Header Terminal --}}
        <div class="bg-black/40 border-b border-emerald-500/10 p-3 px-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex gap-1.5">
                    <div class="w-2.5 h-2.5 bg-red-500/80 rounded-full shadow-[0_0_5px_rgba(239,68,68,0.5)]"></div>
                    <div class="w-2.5 h-2.5 bg-yellow-500/80 rounded-full shadow-[0_0_5px_rgba(234,179,8,0.5)]"></div>
                    <div class="w-2.5 h-2.5 bg-emerald-500/80 rounded-full shadow-[0_0_5px_rgba(16,185,129,0.5)]"></div>
                </div>
                <h3 class="text-emerald-400 font-mono font-bold text-xs tracking-[0.2em] ml-4 opacity-80">
                    PENILAIAN
                </h3>
            </div>
            <div class="text-[10px] font-mono text-emerald-500/50 animate-pulse">
                EVALUASI
            </div>
        </div>

        <div class="p-6 border-b border-emerald-500/10 relative overflow-hidden">
            {{-- Header Decorative Scanline --}}
            <div class="absolute inset-0 bg-linear-to-r from-transparent via-emerald-500/5 to-transparent -translate-x-full animate-[shimmer_3s_infinite]"></div>
            
            <div class="relative z-10 flex justify-between items-end">
                <div>
                    <h3 class="text-xl md:text-2xl font-black text-white tracking-tighter flex items-center gap-3">
                        <i class="fas fa-pen-to-square text-emerald-500 drop-shadow-[0_0_5px_rgba(16,185,129,0.8)]"></i> 
                        LEMBAR PENILAIAN <span class="text-emerald-400">SUPERVISOR</span>
                    </h3>
                    <p class="text-xs font-mono text-gray-400 mt-2 max-w-2xl leading-relaxed">
                        <span class="text-emerald-500">>></span> INSTRUKSI: Input nilai matriks berdasarkan observasi lapangan.
                        <br><span class="text-emerald-500">>></span> RENTANG: <span class="text-white font-bold">1 (Sangat Kurang)</span> s/d <span class="text-white font-bold">5 (Sangat Baik)</span>.
                    </p>
                </div>
                <div class="hidden md:block">
                    <div class="border border-emerald-500/30 p-2 rounded bg-black/30">
                        <i class="fas fa-database text-emerald-500/50 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('evaluation.store') }}" method="POST" class="p-6 relative">
            @csrf
            
            {{-- Decorative Lines --}}
            <div class="absolute top-0 left-6 w-px h-full bg-linear-to-b from-emerald-500/20 to-transparent"></div>
            <div class="absolute top-0 right-6 w-px h-full bg-linear-to-b from-emerald-500/20 to-transparent"></div>

            <div class="overflow-x-auto relative z-10 rounded border border-emerald-500/20 shadow-inner bg-[#050b14]">
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-emerald-900/20 border-b border-emerald-500/30">
                            <th class="p-4 border-r border-emerald-500/20 text-left min-w-[200px] text-emerald-400 font-mono tracking-wider text-xs uppercase">
                                <i class="fas fa-id-badge mr-2"></i> Identitas Kandidat
                            </th>
                            @foreach($criterias as $criteria)
                                <th class="p-4 border-r border-emerald-500/20 text-center min-w-[120px]" title="{{ $criteria->name }}">
                                    <div class="flex flex-col items-center justify-center h-full">
                                        <span class="text-emerald-300 font-bold font-mono text-sm bg-emerald-500/10 px-2 py-1 rounded border border-emerald-500/30 mb-1 block w-full">
                                            {{ $criteria->code }}
                                        </span>
                                        <span class="text-[10px] font-normal text-gray-400 font-mono uppercase tracking-tighter">
                                            {{ substr($criteria->name, 0, 10) }}..
                                        </span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-500/10">
                        @foreach($candidates as $candidate)
                        <tr class="group hover:bg-emerald-500/5 transition-colors duration-200">
                            <td class="p-4 border-r border-emerald-500/20 bg-emerald-900/10 font-bold text-white font-mono relative">
                                {{-- Active Indicator Line --}}
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <div class="flex items-center gap-3 justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded bg-gray-800 border border-gray-600 flex items-center justify-center text-gray-400 text-xs">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        {{ $candidate->name }}
                                    </div>
                                    
                                    {{-- CV DETAIL BUTTON & MODAL (ALPINE JS) --}}
                                    <div x-data="{ showCV: false }">
                                        <button type="button" @click="showCV = true" class="text-[10px] bg-blue-600/20 hover:bg-blue-600/40 text-blue-400 border border-blue-500/30 px-2 py-1 rounded transition-all" title="Lihat Profil Lengkap">
                                            <i class="fas fa-id-card"></i> CV
                                        </button>

                                        {{-- MODAL OVERLAY --}}
                                        <div x-show="showCV" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-sm" style="display: none;" x-transition.opacity>
                                            {{-- MODAL CONTENT --}}
                                            <div @click.away="showCV = false" class="bg-[#0B1120] border border-blue-500/50 w-full max-w-lg rounded-xl shadow-[0_0_50px_rgba(59,130,246,0.3)] relative overflow-hidden" x-transition.scale>
                                                
                                                {{-- Header Modal --}}
                                                <div class="bg-blue-900/20 p-4 border-b border-blue-500/30 flex justify-between items-center">
                                                    <h3 class="font-mono font-bold text-blue-400 flex items-center gap-2">
                                                        <i class="fas fa-user-tie"></i> PROFILE KANDIDAT
                                                    </h3>
                                                    <button type="button" @click="showCV = false" class="text-gray-500 hover:text-red-400 transition-colors">
                                                        <i class="fas fa-times text-lg"></i>
                                                    </button>
                                                </div>

                                                {{-- Body Modal --}}
                                                <div class="p-6 space-y-4 font-mono text-sm text-gray-300">
                                                    
                                                    <div class="flex items-center gap-4 mb-6">
                                                        <div class="w-16 h-16 rounded-full bg-gray-700 border-2 border-blue-500 flex items-center justify-center text-3xl text-gray-400 shadow-[0_0_15px_rgba(59,130,246,0.5)]">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                        <div>
                                                            <h2 class="text-xl font-bold text-white uppercase">{{ $candidate->full_name ?? $candidate->name }}</h2>
                                                            <p class="text-blue-400 text-xs tracking-wider">CANDIDATE ID: #{{ $candidate->id }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div class="bg-gray-800/50 p-3 rounded border border-gray-700">
                                                            <label class="block text-[10px] text-gray-500 uppercase tracking-widest mb-1">UMUR</label>
                                                            <div class="text-white">{{ $candidate->age }} Tahun</div>
                                                        </div>
                                                        <div class="bg-gray-800/50 p-3 rounded border border-gray-700">
                                                            <label class="block text-[10px] text-gray-500 uppercase tracking-widest mb-1">PENGALAMAN</label>
                                                            <div class="text-white">{{ $candidate->experience_year }} Tahun</div>
                                                        </div>
                                                        <div class="bg-gray-800/50 p-3 rounded border border-gray-700">
                                                            <label class="block text-[10px] text-gray-500 uppercase tracking-widest mb-1">DOMISILI</label>
                                                            <div class="text-white">{{ $candidate->domicile_city ?? '-' }}</div>
                                                        </div>
                                                        <div class="bg-gray-800/50 p-3 rounded border border-gray-700">
                                                            <label class="block text-[10px] text-gray-500 uppercase tracking-widest mb-1">NO. HP</label>
                                                            <div class="text-white">{{ $candidate->phone_number ?? '-' }}</div>
                                                        </div>
                                                        <div class="col-span-2 bg-gray-800/50 p-3 rounded border border-gray-700">
                                                            <label class="block text-[10px] text-gray-500 uppercase tracking-widest mb-1">EMAIL</label>
                                                            <div class="text-white">{{ $candidate->email ?? '-' }}</div>
                                                        </div>
                                                        @if($candidate->portfolio_link)
                                                        <div class="col-span-2 bg-blue-900/10 p-3 rounded border border-blue-500/30 hover:bg-blue-900/20 transition-colors cursor-pointer" onclick="window.open('{{ $candidate->portfolio_link }}', '_blank')">
                                                            <label class="block text-[10px] text-blue-400 uppercase tracking-widest mb-1 pointer-events-none">PORTFOLIO</label>
                                                            <div class="text-blue-300 flex items-center gap-2">
                                                                <i class="fas fa-link"></i> {{ $candidate->portfolio_link }}
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>

                                                </div>
                                                
                                                {{-- Footer Modal --}}
                                                <div class="bg-black/40 p-3 text-center border-t border-gray-800">
                                                    <p class="text-[10px] text-gray-600 uppercase tracking-[0.2em]">CONFIDENTIAL DATA</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- END MODAL --}}

                                </div>
                            </td>
                            @foreach($criterias as $criteria)
                                @php
                                    $savedScore = $existingEvaluations[$candidate->id][$criteria->id] ?? null;
                                @endphp
                                <td class="p-3 border-r border-emerald-500/10 text-center relative">
                                    <div class="relative">
                                        <select name="scores[{{ $candidate->id }}][{{ $criteria->id }}]" 
                                                onchange="this.classList.remove('text-gray-500', 'font-normal'); this.classList.add('text-emerald-400', 'font-bold');"
                                                class="w-full p-2 pl-3 bg-[#0a101e] border border-emerald-500/30 {{ $savedScore ? 'text-gray-500 font-normal' : 'text-gray-200' }} rounded text-xs font-mono focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 focus:shadow-[0_0_10px_rgba(16,185,129,0.3)] focus:outline-none transition-all appearance-none hover:border-emerald-500/60 cursor-pointer" required>
                                            <option value="" disabled {{ $savedScore ? '' : 'selected' }} class="text-gray-600">- INPUT -</option>
                                            <option value="1" {{ $savedScore == 1 ? 'selected' : '' }} class="bg-gray-900 text-red-400 font-bold">1 - [SANGAT KURANG]</option>
                                            <option value="2" {{ $savedScore == 2 ? 'selected' : '' }} class="bg-gray-900 text-orange-400">2 - [KURANG]</option>
                                            <option value="3" {{ $savedScore == 3 ? 'selected' : '' }} class="bg-gray-900 text-yellow-400">3 - [CUKUP]</option>
                                            <option value="4" {{ $savedScore == 4 ? 'selected' : '' }} class="bg-gray-900 text-blue-400">4 - [BAIK]</option>
                                            <option value="5" {{ $savedScore == 5 ? 'selected' : '' }} class="bg-gray-900 text-emerald-400 font-bold">5 - [SANGAT BAIK]</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-emerald-500">
                                            <i class="fas fa-caret-down text-xs"></i>
                                        </div>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex flex-col md:flex-row items-center justify-end gap-4">
                @if($hasEvaluated)
                    <div class="px-4 py-2 bg-amber-500/10 border border-amber-500/50 rounded flex items-center gap-3 text-amber-400 text-xs font-mono">
                        <i class="fas fa-exclamation-triangle animate-pulse"></i> 
                        <span>PERINGATAN: TERDETEKSI DATA TIMPA. MENYIMPAN AKAN MENGGANTI REKAM SEBELUMNYA</span>
                    </div>
                @endif
                
                <button type="submit" class="group relative inline-flex items-center gap-3 px-8 py-3 bg-emerald-600 border border-emerald-400 rounded overflow-hidden hover:bg-emerald-500 transition-all duration-300 shadow-[0_0_20px_rgba(16,185,129,0.4)]">
                    <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 skew-y-12"></div>
                    <i class="fas fa-save text-white relative z-10 group-hover:animate-bounce"></i>
                    <span class="font-bold text-white tracking-wider font-mono relative z-10">SIMPAN</span>
                </button>
            </div>

        </form>
    </div>
    
    {{-- LEGEND KRITERIA (DATA PROTOCOL) --}}
    <div class="mt-6 holo-card rounded-lg border border-emerald-500/20 bg-[#0B1120]/80 backdrop-blur p-4 relative">
         <div class="absolute top-0 left-0 w-2 h-2 border-t border-l border-emerald-500"></div>
         <div class="absolute bottom-0 right-0 w-2 h-2 border-b border-r border-emerald-500"></div>
         
        <h4 class="font-mono font-bold text-xs text-emerald-500 mb-3 flex items-center gap-2 uppercase tracking-widest border-b border-emerald-500/10 pb-2">
            <i class="fas fa-info-circle"></i> KAMUS KRITERIA
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs text-gray-400 font-mono">
            @foreach($criterias as $c)
                <div class="flex items-start gap-2 group hover:text-emerald-300 transition-colors">
                    <span class="font-bold text-emerald-500 bg-emerald-500/10 px-1 rounded border border-emerald-500/20 group-hover:border-emerald-400">
                        {{ $c->code }}
                    </span> 
                    <span class="leading-relaxed">
                        {{ $c->name }} <span class="text-[10px] text-gray-600 group-hover:text-emerald-500/50">({{ strtoupper($c->type) }})</span>
                    </span>
                </div>
            @endforeach
        </div>
    </div>

@endsection