@extends('layouts.app')

@section('title', $ui['header_title'])
@section('header', $ui['header_title'])

@section('content')

    {{-- GLOBAL BACKGROUND GRID EFFECT --}}
    {{-- Dynamic Color for Gradient: We use style attribute for dynamic colors to be safe/easy --}}
    @php
        // Color Mapping for Tailwind classes (Safelist/Logic helper)
        $t = $ui['theme']; // e.g., 'cyan', 'red', 'orange'
        $st = $ui['secondary_theme']; // e.g., 'blue', 'orange', 'amber'
        
        // Define specific classes to ensure they are picked up or just valid
        // We will use string interpolation but beware of JIT. 
        // Assuming classes exist in project.
    @endphp

    <div class="fixed inset-0 pointer-events-none -z-10" 
         style="background-image: linear-gradient(rgba(var(--color-{{$t}}-500), 0.05) 1px, transparent 1px), 
         linear-gradient(90deg, rgba(var(--color-{{$t}}-500), 0.05) 1px, transparent 1px); 
         background-size: 30px 30px; mask-image: radial-gradient(circle, black 60%, transparent 100%);">
         {{-- Note: The above style might not work if CSS vars aren't set up. Fallback to inline SVG or class if needed. 
              Actually, the original files used fixed colors. Let's try to mimic that. 
         --}}
    </div>
    
    {{-- Background fallback using classes --}}
    <div class="fixed inset-0 pointer-events-none -z-10 opacity-30">
        <div class="absolute inset-0 bg-{{ $t }}-500/5" style="mask-image: radial-gradient(circle, black 40%, transparent 80%);"></div>
    </div>


    {{-- 1. SECTION ATAS: WELCOME PANEL --}}
    <div class="holo-card rounded-xl overflow-hidden relative border border-{{ $t }}-500/20 bg-[#0B1120]/90 backdrop-blur-md shadow-[0_0_30px_rgba(0,0,0,0.5)] mb-8 z-10">
        
        {{-- Header Terminal --}}
        <div class="bg-black/40 border-b border-{{ $t }}-500/10 p-3 px-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex gap-1.5">
                    <div class="w-2.5 h-2.5 bg-red-500/80 rounded-full shadow-[0_0_5px_rgba(239,68,68,0.5)]"></div>
                    <div class="w-2.5 h-2.5 bg-yellow-500/80 rounded-full shadow-[0_0_5px_rgba(234,179,8,0.5)]"></div>
                    <div class="w-2.5 h-2.5 bg-green-500/80 rounded-full shadow-[0_0_5px_rgba(34,197,94,0.5)]"></div>
                </div>
                <h3 class="text-{{ $t }}-400 font-mono font-bold text-xs tracking-[0.2em] ml-4 opacity-80">
                    DASHBOARD
                </h3>
            </div>
            <div class="text-[10px] font-mono text-{{ $t }}-500/50 animate-pulse">
                ONLINE
            </div>
        </div>

        <div class="p-6 md:p-10 relative">
            {{-- Decorative Scanline --}}
            <div class="absolute inset-0 bg-linear-to-b from-transparent via-{{ $t }}-500/5 to-transparent opacity-30 pointer-events-none animate-[scan_4s_ease-in-out_infinite]"></div>
            
            <div class="relative z-10">
                <h2 class="text-4xl md:text-5xl font-black text-white mb-4 font-sans tracking-tighter">
                    HALO, <span class="text-transparent bg-clip-text bg-linear-to-r from-{{ $t }}-300 via-{{ $st }}-500 to-purple-500 drop-shadow-[0_0_10px_rgba(var(--color-{{ $t }}-500),0.5)]">{{ strtoupper($user->name) }}</span>
                </h2>
                
                <div class="w-full h-px bg-linear-to-r from-{{ $t }}-500/50 via-{{ $st }}-500/20 to-transparent my-6"></div>

                <div class="grid md:grid-cols-3 gap-8">
                    {{-- Kolom Kiri: Identitas --}}
                    <div class="md:col-span-2">
                        <p class="text-gray-400 mb-6 font-mono text-xs md:text-sm leading-relaxed">
                            {!! $ui['description_html'] !!}
                        </p>
                    </div>

                    {{-- Kolom Kanan: Dynamic Content (Objectives OR Icon) --}}
                    @if(isset($ui['objectives']) && is_array($ui['objectives']))
                        {{-- Objectives Box (Area Manager Style) --}}
                        <div class="bg-black/40 border-l-2 border-{{ $t }}-500 p-4 relative overflow-hidden group">
                             <div class="absolute top-0 right-0 p-2 opacity-20 group-hover:opacity-50 transition-opacity">
                                <i class="fas {{ $ui['icon_main'] }} text-4xl text-{{ $t }}-500"></i>
                            </div>
                            <h4 class="text-[10px] font-mono text-{{ $t }}-400 uppercase tracking-widest mb-3 w-fit border-b border-{{ $t }}-500/30 pb-1">
                                OBJEKTIF
                            </h4>
                            <ul class="font-mono text-[11px] text-gray-300 space-y-3">
                                @foreach($ui['objectives'] as $idx => $obj)
                                    <li class="flex items-start gap-3">
                                        <span class="text-{{ $t }}-500">[{{ sprintf('%02d', $idx+1) }}]</span>
                                        <span>{{ $obj }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        {{-- Icon Box (Store/HR Style) --}}
                         <div class="hidden md:flex justify-end items-center opacity-80">
                            <div class="relative">
                                <div class="absolute inset-0 bg-{{ $t }}-500 blur-3xl opacity-20 animate-pulse"></div>
                                <i class="fas {{ $ui['icon_main'] }} text-8xl text-gradient bg-clip-text text-transparent bg-linear-to-b from-{{ $t }}-500 to-transparent opacity-50"></i>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 2. SECTION BAWAH: STATS GRID --}}
    {{-- Always 3 columns now because we brought back Keputusan Final --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 relative z-10">
        
        {{-- CARD 1: ROLE / CANDIDATE COUNT --}}
        <div class="holo-card rounded-lg p-6 border border-{{ $t }}-500/30 bg-[#0f172a]/80 backdrop-blur-sm relative group overflow-hidden hover:-translate-y-1 hover:shadow-[0_0_20px_rgba(var(--color-{{ $t }}-500),0.2)] transition-all duration-300">
            {{-- HUD Corners --}}
            <div class="absolute top-0 left-0 w-3 h-3 border-t-2 border-l-2 border-{{ $t }}-400"></div>
            <div class="absolute bottom-0 right-0 w-3 h-3 border-b-2 border-r-2 border-{{ $t }}-400"></div>
            
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-{{ $t }}-500/10 rounded-full blur-2xl group-hover:bg-{{ $t }}-500/20 transition-all duration-500"></div>
            
            <div class="flex flex-col h-full justify-between relative z-10">
                <div class="flex items-start justify-between mb-4">
                     <div class="h-10 w-10 rounded border border-{{ $t }}-500/30 flex items-center justify-center text-{{ $t }}-400 bg-black/30">
                        <i class="fas fa-users text-lg"></i>
                    </div>
                    <span class="text-[10px] font-mono text-{{ $t }}-500/50">DATA</span>
                </div>
                
                <div>
                    {{-- If Area Manager, show Count. Else show Role? 
                         Original Area showed Candidates Count. Store/HR showed Role.
                         Let's unify: Show Candidates Count for everyone is useful? 
                         Or show Role? Role is already in the header. 
                         Let's show "Total Candidates" for everyone, as it's useful context.
                    --}}
                    <p class="font-mono text-[10px] text-{{ $t }}-400 tracking-[0.2em] uppercase mb-1">Total Kandidat</p>
                    <p class="text-3xl font-bold font-mono text-white tracking-tighter">
                        {{ $candidateCount }} 
                        <span class="text-[10px] text-gray-500 font-normal tracking-normal">ORANG</span>
                    </p>
                </div>
            </div>
        </div>

        @php
            // Logic for Status Card (User's personal evaluation)
            $statusColor = $hasEvaluated ? 'text-emerald-400' : 'text-'.$t.'-400';
            $glowColor   = $hasEvaluated ? 'shadow-emerald-500/20' : 'shadow-'.$t.'-500/20';
            $borderColor = $hasEvaluated ? 'border-emerald-500/30' : 'border-'.$t.'-500/30';
            $cornerColor = $hasEvaluated ? 'border-emerald-500' : 'border-'.$t.'-500';
            $bgGlow      = $hasEvaluated ? 'bg-emerald-500/10' : 'bg-'.$t.'-500/10';
            $icon        = $hasEvaluated ? 'fa-clipboard-check' : 'fa-exclamation-triangle';
            
            if ($hasEvaluated) {
                // If has evaluated, show PERSONAL Winner or just "Complete"
                // User asked for Personal Winner in "Sudah Menilai" card.
                $statusText = $myWinnerName ? strtoupper($myWinnerName) : 'COMPLETE';
                $statusLabel = 'PILIHAN ANDA';
            } else {
                $statusText = 'PENDING';
                $statusLabel = 'STATUS PENILAIAN';
            }
        @endphp

        {{-- CARD 2: STATUS PENILAIAN (PERSONAL) --}}
        <div class="holo-card rounded-lg p-6 border {{ $borderColor }} bg-[#0f172a]/80 backdrop-blur-sm relative group overflow-hidden hover:-translate-y-1 hover:{{ $glowColor }} transition-all duration-300">
            {{-- HUD Corners --}}
            <div class="absolute top-0 right-0 w-3 h-3 border-t-2 border-r-2 {{ $cornerColor }}"></div>
            <div class="absolute bottom-0 left-0 w-3 h-3 border-b-2 border-l-2 {{ $cornerColor }}"></div>

            <div class="absolute -left-8 -bottom-8 w-24 h-24 {{ $bgGlow }} rounded-full blur-2xl transition-all duration-500"></div>
            
             <div class="flex flex-col h-full justify-between relative z-10">
                <div class="flex items-start justify-between mb-4">
                     <div class="h-10 w-10 rounded border {{ $borderColor }} flex items-center justify-center {{ $statusColor }} bg-black/30 relative">
                        <i class="fas {{ $icon }} text-lg"></i>
                        @if(!$hasEvaluated)
                            <span class="absolute -top-1 -right-1 flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-{{ $t }}-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-{{ $t }}-500"></span>
                            </span>
                        @endif
                    </div>
                    <span class="text-[10px] font-mono {{ $hasEvaluated ? 'text-emerald-500/50' : 'text-'.$t.'-500/50' }}">STATUSES</span>
                </div>
                
                <div>
                    <p class="font-mono text-[10px] {{ $statusColor }} tracking-[0.2em] uppercase mb-1">{{ $statusLabel }}</p>
                    <p class="text-2xl font-bold font-mono text-white tracking-tighter truncate" title="{{ $statusText }}">
                        {{ $statusText }}
                    </p>
                </div>
            </div>
        </div>

        {{-- CARD 3: KEPUTUSAN FINAL (GLOBAL) --}}
        {{-- User requested to bring this back for everyone --}}
        <div class="holo-card rounded-lg p-6 border border-amber-500/30 bg-[#0f172a]/80 backdrop-blur-sm relative group overflow-hidden hover:-translate-y-1 hover:shadow-[0_0_20px_rgba(245,158,11,0.2)] transition-all duration-300">
            {{-- Gold Gradient Overlay --}}
            <div class="absolute inset-0 bg-linear-to-tr from-amber-500/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            
            <div class="flex flex-col h-full justify-between relative z-10">
                <div class="flex items-start justify-between mb-4">
                     <div class="h-10 w-10 rounded border border-amber-500/30 flex items-center justify-center text-amber-400 bg-black/30">
                        <i class="fas fa-crown text-lg group-hover:animate-bounce"></i>
                    </div>
                    <span class="text-[10px] font-mono text-amber-500/50">CONSENSUS</span>
                </div>
                
                <div>
                    <p class="font-mono text-[10px] text-amber-500 tracking-[0.2em] uppercase mb-1">Keputusan Final</p>
                    <p class="text-xs font-mono text-gray-300 mt-1 leading-tight">
                        @if($globalWinnerName)
                             <span class="text-white font-bold tracking-wider text-lg">{{ strtoupper($globalWinnerName) }}</span><br>GLOBAL WINNER
                        @else
                             <span class="text-white font-bold tracking-wider">MENUNGGU</span><br>KONSENSUS BORDA
                        @endif
                    </p>
                </div>
            </div>
        </div>

    </div>

@endsection