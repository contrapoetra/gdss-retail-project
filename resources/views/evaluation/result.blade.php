@extends('layouts.app')

@section('title', 'Hasil Konsensus')
@section('header', 'SYSTEM_CONSENSUS_RESULT')

@section('content')

    {{-- 1. LOAD LIBRARY EKSTERNAL --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    {{-- [ADD] LIBRARY HTML2PDF --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    {{-- 2. STYLE --}}
    <style>
        /* A. ANIMASI & EFEK VISUAL TAMBAHAN (SCREEN ONLY) */
        @media screen {
            /* 1. Slide Up Animation */
            @keyframes slideUpFade {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-slide-up {
                animation: slideUpFade 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            
            /* 2. Staggered Delay (untuk elemen yang muncul berurutan) */
            .delay-100 { animation-delay: 0.1s; opacity: 0; animation-fill-mode: forwards; }
            .delay-200 { animation-delay: 0.2s; opacity: 0; animation-fill-mode: forwards; }
            .delay-300 { animation-delay: 0.3s; opacity: 0; animation-fill-mode: forwards; }

            /* 3. Floating Animation (untuk Piala) */
            @keyframes floating {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
                100% { transform: translateY(0px); }
            }
            .animate-float {
                animation: floating 4s ease-in-out infinite;
            }

            /* 4. Cyberpunk Corner Brackets */
            .cyber-corners {
                position: relative;
                clip-path: polygon(
                    0 0, 100% 0, 100% 100%, 0 100%
                );
            }
            .cyber-corners::before, .cyber-corners::after {
                content: ''; position: absolute; width: 20px; height: 20px;
                border: 2px solid rgba(234, 179, 8, 0.6); transition: all 0.3s ease;
            }
            .cyber-corners::before { top: 0; left: 0; border-right: 0; border-bottom: 0; }
            .cyber-corners::after { bottom: 0; right: 0; border-left: 0; border-top: 0; }
            .cyber-corners:hover::before, .cyber-corners:hover::after {
                width: 100%; height: 100%; border-color: rgba(234, 179, 8, 1);
                box-shadow: 0 0 15px rgba(234, 179, 8, 0.3) inset;
            }
        }

        /* B. STYLE KHUSUS CETAK (PRINT ONLY) - ORIGINAL PRESERVED */
        @media print {
            @page { size: auto; margin: 15mm; }
            body { background-color: white !important; color: black !important; -webkit-print-color-adjust: exact; }
            .no-print, nav, header, aside, .sidebar, button, .shadow, .bg-gray-900, .fixed, .z-0 { display: none !important; }
            .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; background: white !important; }
            .grid-print { display: block !important; width: 100%; }
            .col-print { width: 100% !important; page-break-inside: avoid; border: none !important; box-shadow: none !important; margin-bottom: 2rem; background: white !important; }
            h1, h2, h3, h4 { color: black !important; }
            table { width: 100% !important; border-collapse: collapse; font-size: 12px; color: black !important; }
            th, td { border: 1px solid #000 !important; padding: 6px; color: black !important; }
            thead th { background-color: #e5e7eb !important; color: #000 !important; font-weight: bold; }
            .print-header { display: block !important; text-align: center; border-bottom: 3px double #000; margin-bottom: 20px; padding-bottom: 10px; }
            .signature-section { display: flex !important; justify-content: space-between; margin-top: 50px; page-break-inside: avoid; }
            canvas { max-height: 350px !important; width: 100% !important; }
            .badge-print { border: 1px solid #000; color: #000 !important; background: none !important; padding: 2px 8px; border-radius: 10px; }
            /* Reset warna teks cyberpunk saat print */
            .text-yellow-400, .text-yellow-500, .text-gray-400 { color: black !important; }
        }
        .print-header, .signature-section { display: none; }

        /* [ADD] Sembunyikan template PDF saat dilihat di browser biasa */
        #pdf-template { display: none; }
    </style>

    {{-- GLOBAL BACKGROUND GRID EFFECT (YELLOW THEME) --}}
    <div class="fixed inset-0 pointer-events-none z-0 no-print" 
         style="background-image: linear-gradient(rgba(234, 179, 8, 0.03) 1px, transparent 1px), 
         linear-gradient(90deg, rgba(234, 179, 8, 0.03) 1px, transparent 1px); 
         background-size: 40px 40px; mask-image: radial-gradient(circle at center, black 40%, transparent 90%); background-color: #0B1120;">
    </div>

    {{-- 3. KOP SURAT (HANYA MUNCUL SAAT PRINT CTRL+P) --}}
    <div class="print-header">
        <h1 class="text-2xl font-bold uppercase tracking-wider">BERITA ACARA KEPUTUSAN</h1>
        <h2 class="text-xl font-bold">PEMILIHAN SUPERVISOR TOKO RETAIL</h2>
        <p class="text-sm mt-2">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    {{-- 4. NOTIFIKASI (CYBERPUNK STYLE) --}}
    <div class="no-print animate-slide-up">
        @if(session('success'))
            <div class="relative mb-6 group overflow-hidden rounded border border-yellow-500/50 bg-yellow-900/20 backdrop-blur-md p-4 text-yellow-400 shadow-[0_0_25px_rgba(234,179,8,0.2)]">
                <div class="absolute inset-0 bg-linear-to-r from-transparent via-yellow-500/10 to-transparent -translate-x-full group-hover:animate-[shimmer_2s_infinite]"></div>
                <div class="relative flex items-center gap-3">
                    <i class="fas fa-check-circle text-xl animate-pulse"></i>
                    <div>
                        <p class="font-mono font-bold text-xs tracking-widest uppercase text-yellow-300">SYSTEM SUCCESS</p>
                        <p class="text-sm text-yellow-100">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="relative mb-6 group overflow-hidden rounded border border-red-500/50 bg-red-900/20 backdrop-blur-md p-4 text-red-400 shadow-[0_0_25px_rgba(239,68,68,0.2)]">
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
    </div>

    {{-- [ADD] PERIOD SWITCHER --}}
    @if(isset($periods) && $periods->count() > 0)
        <div class="mb-4 flex justify-end no-print animate-slide-up delay-100">
            <form action="{{ route('consensus.index') }}" method="GET" class="flex items-center gap-2 bg-[#0B1120] px-3 py-2 rounded border border-yellow-500/20 shadow-[0_0_10px_rgba(234,179,8,0.1)]">
                <label class="text-[10px] font-mono text-yellow-500 uppercase tracking-wider">
                    <i class="fas fa-calendar-alt mr-1"></i> Filter Periode:
                </label>
                <select name="period_id" onchange="this.form.submit()" class="bg-gray-900 text-white text-xs py-1 px-2 rounded border border-gray-700 focus:border-yellow-500 outline-none font-mono">
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ isset($selectedPeriodId) && $selectedPeriodId == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} @if($p->is_active) [AKTIF] @endif
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    @endif

    {{-- 5. TOMBOL AKSI (CYBERPUNK STYLE) --}}
    <div class="mb-8 flex flex-col md:flex-row justify-between items-center no-print gap-4 relative z-10 animate-slide-up delay-100">
        {{-- [MODIFIED] Ganti window.print() dengan generatePDF() --}}
        <button onclick="generatePDF()" class="group relative inline-flex items-center gap-3 px-6 py-3 bg-[#0B1120] border border-gray-600 rounded overflow-hidden hover:border-yellow-400 hover:shadow-[0_0_15px_rgba(234,179,8,0.4)] transition-all duration-300">
            <div class="absolute inset-0 bg-white/5 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
            <i class="fas fa-file-pdf text-red-500 group-hover:text-yellow-400 transition-colors"></i> 
            <span class="font-mono text-gray-300 font-bold text-sm tracking-wider group-hover:text-white">DOWNLOAD LAPORAN HASIL</span>
        </button>

        @if(Auth::user()->role == 'area_manager')
            <div class="flex items-center gap-4">
                <div class="hidden md:block px-3 py-1 border border-yellow-500/20 bg-yellow-900/10 rounded text-xs font-mono text-yellow-500/70 animate-pulse">
                    <i class="fas fa-info-circle mr-1"></i> KALKULASI ULANG BILA DATA BARU DIGANTI                </div>
                <form id="recalc-form" action="{{ route('consensus.generate') }}" method="POST" class="inline-block" onsubmit="return runCalculationAnimation(event)">
                    @csrf
                    <button type="submit" class="group relative inline-flex items-center gap-3 px-6 py-3 bg-linear-to-r from-yellow-700 to-yellow-600 border border-yellow-400 rounded overflow-hidden hover:from-yellow-600 hover:to-yellow-500 transition-all duration-300 shadow-[0_0_20px_rgba(234,179,8,0.4)] hover:shadow-[0_0_30px_rgba(234,179,8,0.6)]">
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 skew-y-12"></div>
                        <i class="fas fa-sync-alt text-white relative z-10 group-hover:rotate-180 transition-transform duration-500"></i>
                        <span class="font-bold text-white tracking-wider font-mono relative z-10">HITUNG ULANG</span>
                    </button>
                </form>
            </div>
        @endif
    </div>

    {{-- 6. KONTEN UTAMA WRAPPER --}}
    <div class="rounded-xl overflow-hidden relative border border-yellow-500/20 bg-[#0B1120]/90 backdrop-blur-md shadow-[0_0_30px_rgba(0,0,0,0.5)] z-10 col-print animate-slide-up delay-200">
        
        {{-- Header Terminal --}}
        <div class="bg-black/40 border-b border-yellow-500/10 p-3 px-6 flex items-center justify-between no-print">
            <div class="flex items-center gap-3">
                <div class="flex gap-1.5">
                    <div class="w-2.5 h-2.5 bg-red-500/80 rounded-full shadow-[0_0_5px_rgba(239,68,68,0.5)]"></div>
                    <div class="w-2.5 h-2.5 bg-yellow-500/80 rounded-full shadow-[0_0_5px_rgba(234,179,8,0.5)]"></div>
                    <div class="w-2.5 h-2.5 bg-green-500/80 rounded-full shadow-[0_0_5px_rgba(16,185,129,0.5)]"></div>
                </div>
                <h3 class="text-yellow-400 font-mono font-bold text-xs tracking-[0.2em] ml-4 opacity-80">
                    PROTOKOL KONSENSUS
                </h3>
            </div>
            <div class="text-[10px] font-mono text-yellow-500/50 animate-pulse">
                STATUS: CALCULATED
            </div>
        </div>

        <div class="p-6 border-b border-yellow-500/10 relative overflow-hidden no-print">
             {{-- Header Decorative Scanline --}}
             <div class="absolute inset-0 bg-linear-to-r from-transparent via-yellow-500/10 to-transparent -translate-x-full animate-[shimmer_3s_infinite]"></div>
             <h3 class="text-xl md:text-2xl font-black text-white tracking-tighter flex items-center gap-3 relative z-10">
                <i class="fas fa-chart-pie text-yellow-500 drop-shadow-[0_0_10px_rgba(234,179,8,0.8)]"></i> 
                HASIL <span class="text-yellow-400">KEPUTUSAN</span> KONSENSUS
            </h3>
        </div>

        <div class="p-6 relative">
            @if(!$hasResult)
                {{-- KONDISI KOSONG --}}
                <div class="text-center py-20 border border-dashed border-gray-700 rounded-lg bg-[#050b14] opacity-80">
                    <i class="fas fa-folder-open text-6xl text-gray-700 mb-4"></i>
                    <h3 class="text-xl font-mono font-bold text-yellow-600">DATA NOT FOUND</h3>
                    <p class="text-gray-500 font-mono text-xs mt-2">>> WAITING FOR MANAGER EXECUTION...</p>
                </div>
            @else
                {{-- KONDISI ADA DATA --}}
                <div class="mb-4 text-xs font-mono text-yellow-500/60 text-right no-print border-b border-yellow-500/10 pb-2">
                    Dilakukan Pada: <span class="font-bold text-yellow-400">{{ $lastRun->format('d M Y, H:i') }}</span>
                </div>

                {{-- A. VOTING STATUS (REAL-TIME PROGRESS) --}}
                @if(isset($votingProgress))
                <div class="mb-6 bg-[#050b14] p-4 rounded-lg border border-blue-500/20 shadow-[0_0_20px_rgba(59,130,246,0.1)] relative overflow-hidden group no-print">
                    <div class="absolute inset-0 bg-blue-900/5 group-hover:bg-blue-900/10 transition-colors"></div>
                    <div class="relative z-10 flex justify-between items-end mb-2">
                        <div>
                            <h3 class="font-mono font-bold text-blue-400 text-xs uppercase tracking-widest"><i class="fas fa-satellite-dish mr-2 animate-pulse"></i> Status Penilaian (Real-Time)</h3>
                            <p class="text-[10px] text-gray-500 font-mono mt-1">Partisipasi Decision Maker dalam Periode Ini</p>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-black text-white">{{ $votingProgress['percent'] }}%</span>
                            <span class="text-[10px] text-gray-400 font-mono block">{{ $votingProgress['voted'] }} / {{ $votingProgress['total'] }} Voted</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-800 h-2 rounded-full overflow-hidden relative">
                        <div class="bg-blue-500 h-full shadow-[0_0_10px_#3b82f6] relative" style="width: {{ $votingProgress['percent'] }}%">
                            <div class="absolute inset-0 bg-white/20 animate-[shimmer_1s_infinite]"></div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- CHART SECTION WITH TABS --}}
                <div x-data="{ activeChart: 'borda' }" class="mb-8 col-print">
                    
                    {{-- TABS SWITCHER --}}
                    <div class="flex gap-4 mb-4 no-print border-b border-gray-800 pb-1">
                        <button @click="activeChart = 'borda'" 
                            :class="activeChart === 'borda' ? 'text-yellow-400 border-yellow-400 bg-yellow-400/10' : 'text-gray-500 border-transparent hover:text-gray-300'"
                            class="px-4 py-2 text-xs font-bold font-mono uppercase tracking-widest border-b-2 transition-all duration-300 flex items-center gap-2">
                            <i class="fas fa-chart-bar"></i> Statistik Poin
                        </button>
                        <button @click="activeChart = 'radar'" 
                            :class="activeChart === 'radar' ? 'text-cyan-400 border-cyan-400 bg-cyan-400/10' : 'text-gray-500 border-transparent hover:text-gray-300'"
                            class="px-4 py-2 text-xs font-bold font-mono uppercase tracking-widest border-b-2 transition-all duration-300 flex items-center gap-2">
                            <i class="fas fa-spider"></i> Boundary Analysis
                        </button>
                    </div>

                    {{-- GRID LAYOUT --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 grid-print">
                        
                        {{-- B. CHART CONTAINER (SWITCHABLE) --}}
                        <div class="relative">
                            {{-- BORDA CHART --}}
                            <div x-show="activeChart === 'borda'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 class="bg-[#050b14] p-6 rounded-lg border border-yellow-500/20 shadow-inner h-full relative cyber-corners group">
                                <h3 class="font-mono font-bold text-gray-400 text-xs mb-4 border-b border-gray-800 pb-2 uppercase tracking-wider group-hover:text-yellow-400 transition-colors">
                                    <i class="fas fa-chart-bar mr-2 text-yellow-500"></i> Statistik (Poin Borda)
                                </h3>
                                <div class="h-64 w-full relative">
                                    <canvas id="bordaChart"></canvas>
                                </div>
                            </div>

                            {{-- RADAR CHART --}}
                            <div x-show="activeChart === 'radar'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 class="bg-[#050b14] p-6 rounded-lg border border-cyan-500/20 shadow-inner h-full relative cyber-corners group" style="display: none;">
                                <h3 class="font-mono font-bold text-gray-400 text-xs mb-4 border-b border-gray-800 pb-2 uppercase tracking-wider group-hover:text-cyan-400 transition-colors">
                                    <i class="fas fa-spider mr-2 text-cyan-500"></i> Boundary Chart (Top 3 Performance)
                                </h3>
                                <div class="h-64 w-full relative">
                                    <canvas id="radarChart"></canvas>
                                </div>
                            </div>
                        </div>

                        {{-- D. JUARA UTAMA (WINNER CARD) --}}
                        <div class="bg-linear-to-br from-yellow-900/20 to-[#050b14] p-6 rounded-lg border border-yellow-500/30 text-center flex flex-col justify-center items-center relative overflow-hidden hover:shadow-[0_0_30px_rgba(234,179,8,0.15)] transition-all duration-500">
                            {{-- Decorative Background --}}
                            <div class="absolute inset-0" style="background-image: radial-gradient(circle at center, rgba(234,179,8,0.1) 0%, transparent 70%);"></div>
                            <div class="absolute top-0 right-0 p-2 opacity-30"><i class="fas fa-quote-right text-4xl text-yellow-900"></i></div>
                            
                            <div class="relative z-10 inline-block p-6 rounded-full bg-yellow-500/10 border border-yellow-400 text-yellow-400 mb-4 shadow-[0_0_20px_rgba(234,179,8,0.3)] no-print animate-float">
                                <i class="fas fa-trophy fa-4x drop-shadow-[0_0_15px_rgba(234,179,8,0.9)]"></i>
                            </div>
                            
                            <h2 class="text-yellow-500/70 font-mono font-bold uppercase tracking-[0.3em] text-xs mb-2 relative z-10">
                                KANDIDAT TERBAIK
                            </h2>
                            
                            @if(isset($results[0]))
                                <h1 class="text-3xl md:text-4xl font-black text-white mt-2 mb-2 uppercase tracking-tight relative z-10 drop-shadow-md">
                                    {{ $results[0]->candidate->name }}
                                </h1>
                                <div class="mt-4 flex gap-3 justify-center relative z-10">
                                    <div class="bg-yellow-500 text-black px-4 py-1 rounded text-xs font-bold shadow-[0_0_15px_rgba(234,179,8,0.5)] font-mono badge-print">
                                        SKOR: {{ $results[0]->total_points }}
                                    </div>
                                    <div class="bg-[#0B1120] border border-yellow-400 text-yellow-400 px-4 py-1 rounded text-xs font-bold shadow font-mono badge-print">
                                        RANK #1
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- C. TABEL PERINGKAT --}}
                <div class="rounded-lg overflow-hidden mb-8 border border-yellow-500/20 col-print bg-[#050b14] shadow-lg">
                    <div class="bg-yellow-900/20 text-yellow-400 p-4 no-print border-b border-yellow-500/20 flex justify-between items-center">
                        <h3 class="font-bold font-mono uppercase text-sm tracking-wider"><i class="fas fa-list-ol mr-2"></i> Data Perhitungan Akhir</h3>
                        <div class="flex gap-1">
                            <div class="h-1.5 w-1.5 bg-yellow-500 rounded-full animate-pulse"></div>
                            <div class="h-1.5 w-1.5 bg-yellow-500 rounded-full animate-pulse delay-100"></div>
                            <div class="h-1.5 w-1.5 bg-yellow-500 rounded-full animate-pulse delay-200"></div>
                        </div>
                    </div>
                    
                    <div class="print-header hidden text-left font-bold px-4 pt-4" style="border:none; margin-bottom:0;">
                        DETAIL PERINGKAT FINAL:
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-[#0B1120] text-gray-400 uppercase text-xs font-mono leading-normal border-b border-gray-700">
                                    <th class="py-3 px-6 text-center w-20 font-bold text-yellow-500">Rank</th>
                                    <th class="py-3 px-6 font-bold text-yellow-500">Nama Kandidat</th>
                                    <th class="py-3 px-6 text-center font-bold text-yellow-500">Total Poin Borda</th>
                                    <th class="py-3 px-6 text-center font-bold text-yellow-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-300 text-sm font-mono">
                                @foreach($results as $res)
                                    <tr class="border-b border-gray-800 hover:bg-yellow-500/10 transition-all duration-200 {{ $res->final_rank == 1 ? 'bg-yellow-900/10 shadow-[inset_0_0_10px_rgba(234,179,8,0.1)]' : '' }}">
                                        <td class="py-3 px-6 text-center">
                                            @if($res->final_rank == 1) 
                                                <i class="fas fa-crown text-yellow-400 no-print animate-bounce drop-shadow-[0_0_5px_rgba(234,179,8,0.8)]"></i> <span class="font-bold text-yellow-400">1</span>
                                            @else 
                                                {{ $res->final_rank }} 
                                            @endif
                                        </td>
                                        <td class="py-3 px-6 font-bold {{ $res->final_rank == 1 ? 'text-white tracking-wide' : 'text-gray-400' }}">
                                            {{ $res->candidate->name }}
                                        </td>
                                        <td class="py-3 px-6 text-center text-yellow-500/80">{{ $res->total_points }}</td>
                                        <td class="py-3 px-6 text-center">
                                            @if($res->final_rank <= 3) 
                                                <span class="text-green-400 font-bold badge-print text-xs tracking-wider border border-green-500/30 px-2 py-0.5 rounded bg-green-900/20 shadow-[0_0_10px_rgba(74,222,128,0.2)]">[ DIREKOMENDASIKAN ]</span> 
                                            @else 
                                                <span class="text-gray-600 font-bold">-</span> 
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- D. TANDA TANGAN (PRINT ONLY - DEFAULT) --}}
                <div class="signature-section w-full flex justify-between text-center px-10 text-sm mt-12">
                    <div class="w-1/3">
                        <p class="mb-1">Mengetahui,</p>
                        <p class="font-bold mb-24">HR Department</p>
                        <p class="border-t border-black inline-block pt-1 w-48">( ................................. )</p>
                    </div>
                    <div class="w-1/3">
                        <p class="mb-1">Menyetujui,</p>
                        <p class="font-bold mb-24">Area Manager</p>
                        <p class="font-bold underline uppercase">
                            {{ Auth::user()->role == 'area_manager' ? Auth::user()->name : '( ................................. )' }}
                        </p>
                    </div>
                </div>

                {{-- E. TRANSPARANSI PERHITUNGAN (SCREEN ONLY) --}}
                <div x-data="{ open: false }" class="mt-8 border border-yellow-500/20 rounded-lg bg-[#050b14] overflow-hidden no-print animate-slide-up delay-300">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-4 bg-gray-800/50 hover:bg-gray-800 focus:outline-none transition text-left group border-l-4 border-transparent hover:border-yellow-500">
                        <h3 class="font-bold text-gray-300 font-mono text-xs flex items-center uppercase tracking-wider group-hover:text-yellow-400 transition-colors">
                            <i class="fas fa-calculator mr-3 text-yellow-600 group-hover:text-yellow-400 group-hover:animate-spin"></i>
                            >>> Lihat Detail Kalkulasi                        </h3>
                        <i :class="open ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas text-gray-500 group-hover:text-yellow-400"></i>
                    </button>

                    <div x-show="open" x-transition class="p-6 border-t border-yellow-500/10 space-y-8 bg-[#0B1120]" style="display: none;">
                        
                        {{-- Optimasi: Ambil user decision maker sekali saja --}}
                        @php
                            $decisionMakers = \App\Models\User::where('role','!=','admin')->get();
                            $candidateCount = count($candidates);
                        @endphp

                        <div>
                            <h4 class="font-bold text-yellow-500 mb-4 text-xs font-mono uppercase border-b border-gray-700 pb-1 w-max">
                                <i class="fas fa-caret-right"></i> A. TITIK ASAL BORDA (AGGREGRASI)
                            </h4>
                            <div id="borda-breakdown-container" class="w-full overflow-x-auto rounded border border-gray-700 scrollbar-thin scrollbar-thumb-yellow-900 scrollbar-track-gray-900">
                                <table class="w-full table-auto text-xs border-collapse text-center font-mono">
                                    <thead class="bg-gray-800 text-gray-400">
                                        <tr>
                                            <th class="p-3 border-b border-gray-700 border-r text-left">Kandidat</th>
                                            @foreach($decisionMakers as $u)
                                                <th class="p-3 border-b border-gray-700 border-r hover:text-yellow-200 transition-colors">
                                                    {{ $u->name }} <br>
                                                    <span class="text-gray-600 text-[10px]">({{ $u->role }})</span>
                                                </th>
                                            @endforeach
                                            <th class="p-3 border-b border-gray-700 bg-yellow-900/20 text-yellow-400 font-bold">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-300">
                                        @foreach($candidates as $can)
                                            <tr class="hover:bg-white/5 transition-colors">
                                                <td class="p-2 border-b border-r border-gray-700 text-left font-bold text-yellow-100">{{ $can->name }}</td>
                                                @foreach($decisionMakers as $u)
                                                    @php
                                                        $rankData = $topsisBreakdown[$can->id]->where('user_id', $u->id)->first();
                                                        $rank = $rankData ? $rankData->rank : '-';
                                                        $poin = $rank != '-' ? ($candidateCount - $rank + 1) : 0;
                                                    @endphp
                                                    <td class="p-2 border-b border-r border-gray-700">
                                                        Rank: <b class="text-white">{{ $rank }}</b> <br>
                                                        <span class="text-yellow-500 font-bold">({{ $poin }} Pts)</span>
                                                    </td>
                                                @endforeach
                                                <td class="p-2 border-b border-gray-700 font-bold bg-yellow-900/10 text-yellow-400 text-sm">
                                                    {{ $results->where('candidate_id', $can->id)->first()->total_points ?? 0 }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-bold text-yellow-500 mb-2 text-xs font-mono uppercase border-b border-gray-700 pb-1 w-max">
                                <i class="fas fa-caret-right"></i> B. Sampel Perhitungan TOPSIS (User: {{ Auth::user()->name }})
                            </h4>
                            <p class="text-[10px] font-mono text-gray-500 mb-4">>> MENUNJUKKAN HASIL LOGIKA KALKULASI DI SESI SEKARANG</p>

                            <div x-data="{ tab: 'x' }">
                                <div class="flex gap-2 mb-3">
                                    <button @click="tab = 'x'" :class="tab === 'x' ? 'bg-yellow-600 text-white shadow-[0_0_10px_rgba(234,179,8,0.4)]' : 'bg-gray-800 text-gray-500 hover:bg-gray-700 hover:text-gray-300'" class="px-4 py-2 text-xs font-bold font-mono rounded transition border border-transparent">1. MATRIKS (X)</button>
                                    <button @click="tab = 'r'" :class="tab === 'r' ? 'bg-yellow-600 text-white shadow-[0_0_10px_rgba(234,179,8,0.4)]' : 'bg-gray-800 text-gray-500 hover:bg-gray-700 hover:text-gray-300'" class="px-4 py-2 text-xs font-bold font-mono rounded transition border border-transparent">2. TERNORMALISASI (R)</button>
                                    <button @click="tab = 'y'" :class="tab === 'y' ? 'bg-yellow-600 text-white shadow-[0_0_10px_rgba(234,179,8,0.4)]' : 'bg-gray-800 text-gray-500 hover:bg-gray-700 hover:text-gray-300'" class="px-4 py-2 text-xs font-bold font-mono rounded transition border border-transparent">3. TERBOBOT (Y)</button>
                                </div>

                                {{-- MATRIX X --}}
                                <div id="matrix-x-container" x-show="tab === 'x'" class="w-full overflow-x-auto border border-gray-700 rounded bg-[#0a101e]">
                                    <table class="w-full table-auto text-xs text-center font-mono text-gray-300">
                                        <thead class="bg-gray-800 text-yellow-500">
                                            <tr>
                                                <th class="p-2 border-b border-r border-gray-700 text-left">Alt</th>
                                                @foreach($criterias as $c)<th class="p-2 border-b border-r border-gray-700" title="{{ $c->name }}">{{ $c->code }}</th>@endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($candidates as $can)
                                                <tr class="hover:bg-white/5 transition-colors">
                                                    <td class="p-2 border-b border-r border-gray-700 font-bold text-left text-white">{{ $can->name }}</td>
                                                    @foreach($criterias as $c)<td class="p-2 border-b border-r border-gray-700">{{ $matrixX[$can->id][$c->id] ?? '-' }}</td>@endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- MATRIX R --}}
                                <div id="matrix-r-container" x-show="tab === 'r'" class="w-full overflow-x-auto border border-gray-700 rounded bg-[#0a101e]" style="display: none;">
                                    <table class="w-full table-auto text-xs text-center font-mono text-gray-300">
                                        <thead class="bg-gray-800 text-blue-400">
                                            <tr>
                                                <th class="p-2 border-b border-r border-gray-700 text-left">Alt</th>
                                                @foreach($criterias as $c)<th class="p-2 border-b border-r border-gray-700">{{ $c->code }}</th>@endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($candidates as $can)
                                                <tr class="hover:bg-white/5 transition-colors">
                                                    <td class="p-2 border-b border-r border-gray-700 font-bold text-left text-white">{{ $can->name }}</td>
                                                    @foreach($criterias as $c)<td class="p-2 border-b border-r border-gray-700">{{ number_format($matrixR[$can->id][$c->id] ?? 0, 4) }}</td>@endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- MATRIX Y --}}
                                <div id="matrix-y-container" x-show="tab === 'y'" class="w-full overflow-x-auto border border-gray-700 rounded bg-[#0a101e]" style="display: none;">
                                    <table class="w-full table-auto text-xs text-center font-mono text-gray-300">
                                        <thead class="bg-gray-800 text-green-400">
                                            <tr>
                                                <th class="p-2 border-b border-r border-gray-700 text-left">Alt</th>
                                                @foreach($criterias as $c)<th class="p-2 border-b border-r border-gray-700">{{ $c->code }}</th>@endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($candidates as $can)
                                                <tr class="hover:bg-white/5 transition-colors">
                                                    <td class="p-2 border-b border-r border-gray-700 font-bold text-left text-white">{{ $can->name }}</td>
                                                    @foreach($criterias as $c)<td class="p-2 border-b border-r border-gray-700">{{ number_format($matrixY[$can->id][$c->id] ?? 0, 4) }}</td>@endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 7. [ADD] TEMPLATE KHUSUS UNTUK PDF (HIDDEN ON SCREEN) --}}
    {{-- ============================================================ --}}
    @if($hasResult)
    <div id="pdf-template">
        <div style="width: 210mm; min-height: 297mm; padding: 20mm; background: white; color: black; font-family: 'Times New Roman', serif; margin: 0 auto; position: relative;">
            
            {{-- CONFIDENTIAL WATERMARK --}}
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 80px; color: rgba(0,0,0,0.05); font-weight: bold; pointer-events: none; white-space: nowrap;">
                CONFIDENTIAL DOCUMENT
            </div>

            {{-- A. Header Kop --}}
            <div style="text-align: center; border-bottom: 3px double black; padding-bottom: 10px; margin-bottom: 20px;">
                <h1 style="font-size: 20px; font-weight: 900; text-transform: uppercase; margin: 0; letter-spacing: 1px;">BERITA ACARA KEPUTUSAN</h1>
                <h2 style="font-size: 16px; font-weight: bold; margin: 5px 0;">SISTEM PENDUKUNG KEPUTUSAN (GDSS)</h2>
                <p style="font-size: 11px; color: #555; margin-top: 5px;">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>

            {{-- B. Bagian Grafik --}}
            <div style="display: flex; gap: 20px; margin-bottom: 20px; align-items: flex-start;">
                <div style="flex: 1;">
                    <h3 style="font-size: 12px; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #000; padding-bottom: 3px; margin-bottom: 10px;">
                        I. STATISTIK POIN (BORDA)
                    </h3>
                    <img id="chartImageTarget" src="" style="width: 100%; border: 1px solid #eee; height: auto;">
                </div>
                <div style="flex: 1;">
                    <h3 style="font-size: 12px; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #000; padding-bottom: 3px; margin-bottom: 10px;">
                        II. ANALISIS BOUNDARY (RADAR)
                    </h3>
                    <img id="radarImageTarget" src="" style="width: 100%; border: 1px solid #eee; height: auto;">
                </div>
            </div>

            {{-- C. Bagian Pemenang --}}
            <div style="margin-bottom: 20px; text-align: center; padding: 15px; border: 2px solid #000; background-color: #f3f4f6;">
                <h4 style="font-size: 10px; color: #555; uppercase; letter-spacing: 2px; margin-bottom: 5px;">KANDIDAT TERPILIH (RANK 1)</h4>
                @if(isset($results[0]))
                    <h1 style="font-size: 32px; font-weight: 900; margin: 5px 0; text-transform: uppercase;">{{ $results[0]->candidate->name }}</h1>
                    <div style="font-size: 12px;">Total Poin: <strong>{{ $results[0]->total_points }}</strong></div>
                @endif
            </div>

            {{-- D. Tabel Detail --}}
            <div style="margin-bottom: 30px;">
                <h3 style="font-size: 12px; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #000; padding-bottom: 3px; margin-bottom: 10px;">
                    III. RINCIAN PERINGKAT
                </h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                    <thead>
                        <tr style="background-color: #e5e7eb;">
                            <th style="border: 1px solid black; padding: 6px; text-align: center; width: 40px;">NO</th>
                            <th style="border: 1px solid black; padding: 6px; text-align: left;">NAMA KANDIDAT</th>
                            <th style="border: 1px solid black; padding: 6px; text-align: center;">POIN BORDA</th>
                            <th style="border: 1px solid black; padding: 6px; text-align: center;">KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results->take(5) as $res)
                            <tr style="{{ $res->final_rank == 1 ? 'background-color: #f9fafb; font-weight: bold;' : '' }}">
                                <td style="border: 1px solid black; padding: 6px; text-align: center;">{{ $res->final_rank }}</td>
                                <td style="border: 1px solid black; padding: 6px;">{{ $res->candidate->name }}</td>
                                <td style="border: 1px solid black; padding: 6px; text-align: center;">{{ $res->total_points }}</td>
                                <td style="border: 1px solid black; padding: 6px; text-align: center;">
                                    @if($res->final_rank <= 3) RECOMMENDED @else - @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- E. Tanda Tangan --}}
            <div style="display: flex; justify-content: space-between; margin-top: 40px; page-break-inside: avoid;">
                <div style="width: 40%; text-align: center;">
                    <p style="margin-bottom: 50px; font-size: 12px;">Dibuat Oleh,<br><strong>HR Department</strong></p>
                    <p style="border-top: 1px solid black; width: 80%; margin: 0 auto; padding-top: 5px; font-size: 12px;">( ................................. )</p>
                </div>
                <div style="width: 40%; text-align: center;">
                    <p style="margin-bottom: 50px; font-size: 12px;">Disetujui Oleh,<br><strong>Area Manager</strong></p>
                    <p style="border-top: 1px solid black; width: 80%; margin: 0 auto; padding-top: 5px; font-weight: bold; text-transform: uppercase; font-size: 12px;">
                        {{ Auth::user()->role == 'area_manager' ? Auth::user()->name : '( ................................. )' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 8. SCRIPT CHART JS & [ADD] PDF GENERATOR FUNCTION --}}
    @if($hasResult)
        <script>
            // A. Render Chart untuk Layar (Kode Asli Dipertahankan)
            document.addEventListener("DOMContentLoaded", function() {
                // 1. BORDA CHART
                const ctx = document.getElementById('bordaChart');
                const rawLabels = {!! json_encode($results->pluck('candidate.name') ?? []) !!};
                const rawData = {!! json_encode($results->pluck('total_points') ?? []) !!};

                if (ctx) {
                    const chartCanvas = ctx.getContext('2d');
                    const gradient = chartCanvas.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(234, 179, 8, 0.9)'); 
                    gradient.addColorStop(1, 'rgba(234, 179, 8, 0.1)');

                    window.myChartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: rawLabels,
                            datasets: [{
                                label: 'Total Poin',
                                data: rawData,
                                backgroundColor: gradient, 
                                borderColor: 'rgba(234, 179, 8, 1)',
                                borderWidth: 1,
                                borderRadius: 4,
                                barThickness: 'flex',
                                maxBarThickness: 50,
                                hoverBackgroundColor: 'rgba(255, 255, 255, 0.9)',
                                hoverBorderColor: '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 2000, easing: 'easeOutQuart' },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                    titleColor: '#fbbf24',
                                    bodyColor: '#fff',
                                    borderColor: '#fbbf24',
                                    borderWidth: 1,
                                    titleFont: { family: 'monospace' },
                                    bodyFont: { family: 'monospace' },
                                    padding: 10,
                                    displayColors: false,
                                    callbacks: { label: function(context) { return ' >> SCORE: ' + context.raw; } }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { stepSize: 1, color: '#6b7280', font: {family: 'monospace'} },
                                    grid: { color: 'rgba(75, 85, 99, 0.2)', borderDash: [5, 5] }
                                },
                                x: {
                                    ticks: { color: '#d1d5db', font: {family: 'monospace', weight: 'bold'} },
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                }

                // 2. RADAR CHART (BOUNDARY)
                const ctxRadar = document.getElementById('radarChart');
                if (ctxRadar) {
                    const criteriaCodes = {!! json_encode($criterias->pluck('code')) !!};
                    const criteriaIds = {!! json_encode($criterias->pluck('id')) !!};
                    
                    // Ambil Top 3 Kandidat
                    const topCandidates = {!! json_encode($results->take(3)->pluck('candidate')) !!};
                    const matrixR = {!! json_encode($matrixR) !!};
                    
                    const radarDatasets = topCandidates.map((cand, index) => {
                        // Map data sesuai urutan criteriaIds
                        const data = criteriaIds.map(id => matrixR[cand.id] ? matrixR[cand.id][id] : 0);
                        
                        const colors = [
                            { border: 'rgba(234, 179, 8, 1)', bg: 'rgba(234, 179, 8, 0.2)' }, // Yellow (1st)
                            { border: 'rgba(59, 130, 246, 1)', bg: 'rgba(59, 130, 246, 0.2)' }, // Blue (2nd)
                            { border: 'rgba(16, 185, 129, 1)', bg: 'rgba(16, 185, 129, 0.2)' }, // Green (3rd)
                        ];
                        const c = colors[index] || { border: '#999', bg: '#ddd' };

                        return {
                            label: cand.name,
                            data: data,
                            borderColor: c.border,
                            backgroundColor: c.bg,
                            borderWidth: 2,
                            pointBackgroundColor: c.border,
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: c.border
                        };
                    });

                    new Chart(ctxRadar, {
                        type: 'radar',
                        data: {
                            labels: criteriaCodes,
                            datasets: radarDatasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            elements: { line: { tension: 0.3 } },
                            scales: {
                                r: {
                                    angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                                    grid: { color: 'rgba(255, 255, 255, 0.1)' },
                                    pointLabels: { color: '#9ca3af', font: { family: 'monospace', size: 10 } },
                                    ticks: { display: false, backdropColor: 'transparent' },
                                    suggestedMin: 0,
                                    suggestedMax: 1
                                }
                            },
                            plugins: {
                                legend: { 
                                    labels: { color: '#d1d5db', font: { family: 'monospace' } },
                                    position: 'bottom'
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                    titleFont: { family: 'monospace' },
                                    bodyFont: { family: 'monospace' }
                                }
                            }
                        }
                    });
                }
            });

            // [ADD] B. Fungsi Generate PDF menggunakan html2pdf
            function generatePDF() {
                const element = document.getElementById('pdf-template');
                
                // FORCE VISIBLE ALL CHARTS FOR CAPTURE
                // Karena x-show menyembunyikan elemen, html2canvas mungkin gagal capture (width 0).
                // Kita akan memaksa display:block sementara.
                const radarContainer = document.getElementById('radarChart')?.parentElement?.parentElement; // Container div
                const bordaContainer = document.getElementById('bordaChart')?.parentElement?.parentElement;
                
                const originalRadarDisplay = radarContainer ? radarContainer.style.display : '';
                const originalBordaDisplay = bordaContainer ? bordaContainer.style.display : '';

                if(radarContainer) radarContainer.style.display = 'block';
                if(bordaContainer) bordaContainer.style.display = 'block';

                // Canvas Helpers
                const captureCanvas = (id) => {
                    const c = document.getElementById(id);
                    if (!c) return null;
                    const temp = document.createElement('canvas');
                    // Pakai width/height eksplisit dari canvas asli
                    temp.width = c.width; temp.height = c.height;
                    const ctx = temp.getContext('2d');
                    ctx.fillStyle = '#FFFFFF';
                    ctx.fillRect(0, 0, temp.width, temp.height);
                    ctx.drawImage(c, 0, 0);
                    return temp.toDataURL('image/jpeg', 1.0);
                };

                // Inject Borda Chart
                const bordaImg = captureCanvas('bordaChart');
                if(bordaImg) document.getElementById('chartImageTarget').src = bordaImg;

                // Inject Radar Chart
                const radarImg = captureCanvas('radarChart');
                if(radarImg) document.getElementById('radarImageTarget').src = radarImg;

                // RESTORE VISIBILITY
                // Kita kembalikan ke kosong agar Alpine.js mengambil alih lagi, 
                // atau set ke none jika memang hidden.
                // Masalah: Alpine mungkin bingung jika kita mainkan style inline.
                // Solusi aman: Hapus style inline display, biarkan Alpine re-eval x-show.
                if(radarContainer) radarContainer.style.display = '';
                if(bordaContainer) bordaContainer.style.display = '';

                const opt = {
                    margin:       0, // Reset margin
                    filename:     'Laporan_Keputusan_Final.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2, useCORS: true },
                    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };

                element.style.display = 'block'; 
                html2pdf().set(opt).from(element).save().then(function(){
                    element.style.display = 'none'; 
                });
            }

            // [ADD] C. Calculation Animation (VISUAL WIZARD)
            function runCalculationAnimation(e) {
                e.preventDefault();
                const overlay = document.getElementById('calculation-overlay');
                const logContainer = document.getElementById('calc-logs');
                const progressBar = document.getElementById('calc-progress');
                const percentText = document.getElementById('calc-percent');
                const stepTitle = document.getElementById('step-title');
                const stepContent = document.getElementById('step-content');
                const form = document.getElementById('recalc-form');

                overlay.classList.remove('hidden');
                overlay.classList.add('flex');

                // Helper to get cloned content or fallback
                const getContent = (id) => {
                    const el = document.getElementById(id);
                    if (el) {
                        const clone = el.cloneNode(true);
                        
                        // 1. Force Visibility & Width
                        clone.style.display = 'block';
                        clone.style.visibility = 'visible';
                        clone.style.width = '100%';
                        clone.classList.add('w-full'); // Tailwind force width

                        // 2. Remove ID to prevent conflicts
                        clone.removeAttribute('id');

                        // 3. Clean Alpine.js attributes that might hide element
                        const cleanAlpine = (node) => {
                            if (node.nodeType === 1) { // Element
                                node.removeAttribute('x-show');
                                node.removeAttribute('x-data');
                                node.removeAttribute('x-transition');
                                node.removeAttribute('x-transition:enter');
                                node.removeAttribute('x-transition:leave');
                                node.style.display = ''; // Reset inline display (except root)
                            }
                            if (node.hasChildNodes()) {
                                node.childNodes.forEach(cleanAlpine);
                            }
                        };
                        cleanAlpine(clone);

                        // Re-apply display block to root after cleaning
                        clone.style.display = 'block';

                        return clone;
                    }
                    return null;
                };

                // Steps Definition
                const sequence = [
                    { 
                        title: "1. LOADING RAW DATA (MATRIX X)",
                        log: "> Fetching user evaluations...",
                        contentId: "matrix-x-container",
                        fallbackHTML: "<div class='text-yellow-500'>[DATA STREAM INCOMING...]</div>"
                    },
                    { 
                        title: "2. NORMALIZING DATA (MATRIX R)",
                        log: "> Applying Euclidean Normalization...",
                        contentId: "matrix-r-container",
                        fallbackHTML: "<div class='text-blue-400'>[CALCULATING R = X / SQRT(SUM(X^2))...]</div>"
                    },
                    { 
                        title: "3. APPLYING WEIGHTS (MATRIX Y)",
                        log: "> Multiplying by Criteria Weights...",
                        contentId: "matrix-y-container",
                        fallbackHTML: "<div class='text-green-400'>[CALCULATING Y = R * W...]</div>"
                    },
                    { 
                        title: "4. BORDA AGGREGATION",
                        log: "> Summing points from all Decision Makers...",
                        contentId: "borda-breakdown-container",
                        fallbackHTML: "<div class='text-purple-400'>[AGGREGATING VOTES...]</div>"
                    },
                    { 
                        title: "5. FINALIZING CONSENSUS",
                        log: "> Sorting Candidates & Saving to Database...",
                        contentId: null,
                        fallbackHTML: "<div class='text-center'><i class='fas fa-trophy text-6xl text-yellow-500 mb-4 animate-bounce'></i><br>GENERATING FINAL REPORT</div>"
                    }
                ];

                let currentStep = 0;
                const stepDuration = 1200; // ms per step
                const totalDuration = sequence.length * stepDuration;

                const interval = setInterval(() => {
                    if (currentStep >= sequence.length) {
                        clearInterval(interval);
                        // Submit Form
                        stepTitle.innerText = "DONE. RELOADING...";
                        logContainer.innerText = "> REFRESHING SYSTEM...";
                        form.submit();
                        return;
                    }

                    const step = sequence[currentStep];
                    
                    // Update UI
                    stepTitle.innerText = step.title;
                    logContainer.innerText = step.log;
                    
                    // Update Content
                    stepContent.innerHTML = ''; // clear previous
                    stepContent.style.opacity = '0';
                    
                    setTimeout(() => {
                        let content = null;
                        if(step.contentId) content = getContent(step.contentId);
                        
                        if(content) {
                            stepContent.appendChild(content);
                        } else {
                            stepContent.innerHTML = step.fallbackHTML;
                        }
                        stepContent.style.opacity = '1';
                    }, 100);

                    // Update Progress
                    const percent = Math.round(((currentStep + 1) / sequence.length) * 100);
                    progressBar.style.width = percent + "%";
                    percentText.innerText = percent + "%";

                    currentStep++;

                }, stepDuration);
            }
        </script>
    @endif

    {{-- ============================================================ --}}
    {{-- 9. [ADD] CALCULATION OVERLAY (ANIMATION) --}}
    {{-- ============================================================ --}}
    <div id="calculation-overlay" class="fixed inset-0 z-[9999] hidden bg-[#05000a] flex-col items-center justify-center font-mono">
        <div class="w-full max-w-5xl p-8 relative flex flex-col items-center h-full justify-center">
            
            {{-- Header Overlay --}}
            <div class="text-center mb-8 relative z-10">
                <div class="inline-block border border-yellow-500/50 bg-yellow-900/10 px-6 py-2 rounded mb-4 shadow-[0_0_20px_rgba(234,179,8,0.2)]">
                    <h2 class="text-yellow-400 text-xl font-bold tracking-[0.3em] animate-pulse flex items-center gap-3">
                        <i class="fas fa-network-wired"></i> GDSS PROTOCOL ENGAGED
                    </h2>
                </div>
                <p id="step-title" class="text-white text-lg font-mono tracking-widest uppercase opacity-80 h-8">INITIALIZING...</p>
            </div>

            {{-- VISUAL STEP CONTAINER --}}
            <div id="visual-step-container" class="w-full relative flex-1 border border-gray-800 bg-[#0a050f] rounded-lg overflow-hidden relative shadow-2xl mb-8">
                
                {{-- Scanlines --}}
                <div class="absolute inset-0 pointer-events-none bg-[url('https://media.giphy.com/media/oEI9uBYSzLpBK/giphy.gif')] opacity-5 mix-blend-overlay"></div>
                <div class="absolute inset-0 bg-linear-to-b from-transparent via-yellow-500/5 to-transparent h-full w-full animate-[shimmer_3s_infinite] pointer-events-none"></div>

                {{-- DYNAMIC CONTENT WILL BE INJECTED HERE --}}
                <div id="step-content" class="p-8 w-full h-full flex flex-col justify-center items-center overflow-auto text-xs text-gray-300">
                    {{-- Default content --}}
                    <div class="flex flex-col items-center gap-4 opacity-50">
                        <i class="fas fa-spinner fa-spin text-4xl text-yellow-500"></i>
                        <span>ESTABLISHING SECURE CONNECTION...</span>
                    </div>
                </div>

            </div>

            {{-- Progress Bar & Logs --}}
            <div class="w-full max-w-3xl">
                <div class="w-full h-1 bg-gray-900 relative overflow-hidden rounded mb-2">
                    <div id="calc-progress" class="absolute top-0 left-0 h-full bg-yellow-500 w-0 transition-all duration-300 ease-out shadow-[0_0_15px_#eab308]"></div>
                </div>
                <div class="flex justify-between items-end">
                    <div id="calc-logs" class="text-[10px] text-gray-500 font-mono h-6 overflow-hidden">
                        > WAITING FOR COMMAND...
                    </div>
                    <span id="calc-percent" class="text-yellow-500 font-bold text-xs">0%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER LEGEND --}}
    <div class="mt-6 holo-card rounded-lg border border-yellow-500/20 bg-[#0B1120]/80 backdrop-blur p-4 relative no-print animate-slide-up delay-300">
         <div class="absolute top-0 left-0 w-2 h-2 border-t border-l border-yellow-500"></div>
         <div class="absolute bottom-0 right-0 w-2 h-2 border-b border-r border-yellow-500"></div>
         
        <h4 class="font-mono font-bold text-xs text-yellow-500 mb-3 flex items-center gap-2 uppercase tracking-widest border-b border-yellow-500/10 pb-2">
            <i class="fas fa-info-circle"></i> INFORMASI
        </h4>
        <p class="text-xs text-gray-400 font-mono">
            Metode yang digunakan adalah <span class="text-yellow-400 font-bold">Borda Count</span> untuk agregasi grup dan <span class="text-blue-400 font-bold">TOPSIS</span> untuk preferensi individu. Data dienkripsi dan disimpan dalam protokol aman.
        </p>
    </div>

@endsection