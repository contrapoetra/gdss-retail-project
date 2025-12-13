<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ADMIN')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400&family=Outfit:wght@200;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        mono: ['Space Mono', 'monospace'],
                    },
                    colors: {
                        'void': '#05000a', 
                        'purple-neon': '#d946ef', 
                        'cyan-neon': '#00e5ff', 
                        'glass': 'rgba(255, 255, 255, 0.03)',
                    },
                    animation: {
                        'glitch': 'glitch 1s linear infinite',
                    },
                    keyframes: {
                        glitch: {
                            '2%, 64%': { transform: 'translate(2px,0) skew(0deg)' },
                            '4%, 60%': { transform: 'translate(-2px,0) skew(0deg)' },
                            '62%': { transform: 'translate(0,0) skew(5deg)' },
                        }
                    }
                },
            },
        }
    </script>

    <style>
        :root {
            --primary: #d946ef; /* NEON PURPLE */
            --secondary: #00e5ff; /* CYAN */
        }

        body {
            background-color: #05000a;
            margin: 0;
            color: white;
            cursor: none;
            overflow-x: hidden;
        }

        /* --- SCROLLBAR GLOBAL --- */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0a050f; }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 4px; }

        /* --- CRT EFFECTS --- */
        .scanlines {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,0) 50%, rgba(0,0,0,0.2) 50%, rgba(0,0,0,0.2));
            background-size: 100% 4px; z-index: 50; pointer-events: none; opacity: 0.3;
        }
        .vignette {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: radial-gradient(circle, rgba(0,0,0,0) 60%, rgba(5,0,10,1) 100%);
            z-index: 51; pointer-events: none;
        }

        /* --- BOOT SCREEN --- */
        #boot-screen {
            position: fixed; inset: 0; background: #05000a; z-index: 9999;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            font-family: 'Space Mono', monospace;
        }
        .boot-title-glitch {
            font-size: 4rem; font-weight: 800; position: relative; color: white; letter-spacing: -2px;
        }
        .boot-title-glitch::before, .boot-title-glitch::after {
            content: attr(data-text); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #05000a;
        }
        .boot-title-glitch::before { left: 2px; text-shadow: -1px 0 var(--secondary); animation: glitch 2s infinite; }
        .boot-title-glitch::after { left: -2px; text-shadow: -1px 0 var(--primary); animation: glitch 3s infinite; }

        /* --- HOLO CARDS --- */
        .holo-card {
            background: rgba(20, 5, 30, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(217, 70, 239, 0.2);
            box-shadow: 0 0 20px rgba(217, 70, 239, 0.05);
            position: relative; overflow: hidden;
            transition: all 0.3s ease;
        }
        .holo-card:hover {
            border-color: var(--primary);
            box-shadow: 0 0 30px rgba(217, 70, 239, 0.15);
        }

        /* --- INPUT & BUTTON --- */
        .tech-input {
            background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease; color: #fff; font-family: 'Space Mono', monospace;
        }
        .tech-input:focus {
            border-color: var(--primary); box-shadow: 0 0 15px rgba(217, 70, 239, 0.3);
            background: rgba(217, 70, 239, 0.05); outline: none;
        }
        
        .tech-btn {
            background: transparent; border: 1px solid var(--primary); color: var(--primary);
            font-family: 'Space Mono', monospace; letter-spacing: 2px; text-transform: uppercase;
            position: relative; overflow: hidden; transition: all 0.3s;
        }
        .tech-btn:hover {
            background: var(--primary); color: black; box-shadow: 0 0 20px var(--primary);
        }
        .tech-btn-danger {
            border-color: #ef4444; color: #ef4444;
        }
        .tech-btn-danger:hover {
            background: #ef4444; color: white; box-shadow: 0 0 20px #ef4444;
        }

        /* --- TABLES --- */
        .tech-table th {
            text-align: left; padding: 1rem;
            border-bottom: 1px solid rgba(217, 70, 239, 0.3);
            color: var(--secondary); font-family: 'Space Mono', monospace; font-size: 0.8rem;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .tech-table td {
            padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05);
            font-family: 'Outfit', sans-serif; color: rgba(255,255,255,0.8);
        }
        .tech-table tr:hover td {
            background: rgba(217, 70, 239, 0.05); color: white;
        }

        /* --- CUSTOM NEON SELECT --- */
        .neon-select {
            position: relative;
        }

        .neon-select select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background: radial-gradient(circle at top left, rgba(217,70,239,0.2), transparent 55%) rgba(0,0,0,0.6);
            border: 1px solid rgba(255,255,255,0.12);
            color: #e5e7eb;
            padding-right: 2.2rem;
            cursor: pointer;
        }

        .neon-select::after {
            content: "\f078";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.6rem;
            color: var(--secondary);
            pointer-events: none;
            text-shadow: 0 0 8px rgba(0,229,255,0.8);
        }

        .neon-select select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 16px rgba(217,70,239,0.55);
        }

        .neon-select select option {
            background-color: #05000f;
            color: #e5e7eb;
            font-family: 'Space Mono', monospace;
            padding: 0.4rem 0.75rem;
        }

        .neon-select select option:hover,
        .neon-select select option:checked {
            background-color: rgba(217,70,239,0.35);
            color: #ffffff;
        }

        /* --- HERO TITLE: lebih terang + glow --- */
        .hero-title {
            color: #f9fafb;
            text-shadow:
                0 0 12px rgba(0, 229, 255, 0.9),
                0 0 26px rgba(217, 70, 239, 0.75),
                0 0 40px rgba(217, 70, 239, 0.6);
        }

        /* --- CURSOR (SELALU DI ATAS MODAL) --- */
        #cursor {
            position: fixed; top: 0; left: 0; width: 20px; height: 20px;
            border: 1px solid var(--primary); border-radius: 50%;
            transform: translate(-50%, -50%); pointer-events: none; 
            z-index: 200000; /* > modal */
            transition: width 0.2s, height 0.2s;
            mix-blend-mode: difference;
        }
        #cursor-dot {
            position: fixed; top: 0; left: 0; width: 4px; height: 4px;
            background: var(--primary); border-radius: 50%; transform: translate(-50%, -50%);
            pointer-events: none; 
            z-index: 200001; /* sedikit di atas ring */
        }
        .cursor-hover {
            width: 50px !important; height: 50px !important; background-color: rgba(217, 70, 239, 0.2);
        }

        /* --- MODAL LOGOUT: di atas konten, di bawah cursor --- */
        #shutdown-modal {
            z-index: 150000;
        }
    </style>
</head>
<body class="font-sans selection:bg-purple-neon selection:text-white">

    <div class="scanlines"></div>
    <div class="vignette"></div>
    <canvas id="warp-canvas" class="fixed inset-0 z-0"></canvas>
    
    <div id="cursor"></div>
    <div id="cursor-dot"></div>

    <div id="boot-screen">
        <div class="boot-title-glitch" data-text="PORTAL ADMIN">PORTAL ADMIN</div>
        <div class="text-purple-neon font-mono text-xs mt-4 tracking-[0.5em] animate-pulse">MEMULAI SESI...</div>
        <div class="w-64 h-1 bg-gray-900 mt-6 relative overflow-hidden rounded">
            <div id="boot-bar" class="absolute top-0 left-0 h-full bg-purple-neon shadow-[0_0_10px_#d946ef] w-0 transition-all duration-2000 ease-out"></div>
        </div>
    </div>

    <div id="main-interface" class="relative z-10 min-h-screen p-6 md:p-12 opacity-0 transition-opacity duration-1000">
        
        <header class="mb-12 flex justify-between items-end border-b border-white/10 pb-6">
            <div>
                <h1 class="hero-title text-4xl md:text-5xl font-black tracking-tighter font-mono">
                    SELAMAT DATANG, ADMIN!
                </h1>
                <p class="text-xs font-mono text-cyan-neon mt-2 tracking-widest">ADMIN DASHBOARD</p>
            </div>

            <div class="flex items-end gap-6">
                <div class="text-right hidden md:block">
                    <div class="text-[10px] text-gray-500 font-mono">SYSTEM TIME</div>
                    <div id="clock" class="text-xl font-mono text-white">00:00:00</div>
                </div>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>

                <button onclick="toggleShutdown()" type="button" class="group relative px-4 py-2 border border-red-500/50 text-red-500 hover:bg-red-500/10 hover:shadow-[0_0_15px_rgba(239,68,68,0.5)] transition-all rounded overflow-hidden">
                    <span class="relative z-10 font-mono text-xs font-bold tracking-widest flex items-center gap-2">
                        <i class="fas fa-power-off"></i> LOGOUT
                    </span>
                    <div class="absolute inset-0 bg-red-500/20 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-300"></div>
                </button>
            </div>
        </header>

        @if(session('success'))
        <div class="mb-8 holo-card border-l-4 border-l-green-500! p-4 flex items-center gap-4 animate-pulse">
            <i class="fas fa-check-circle text-green-500 text-xl"></i>
            <div>
                <h4 class="font-mono text-green-500 text-xs uppercase tracking-widest">Success</h4>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <div class="holo-card rounded-xl mb-12">
            <div class="p-4 border-b border-white/5 bg-purple-900/20 flex justify-between items-center">
                <h3 class="font-bold font-mono text-purple-neon flex items-center gap-2">
                    <i class="fas fa-users-cog"></i> MANAJEMEN USER
                </h3>
                <div class="h-1 w-24 bg-purple-neon shadow-[0_0_10px_#d946ef]"></div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full tech-table text-sm">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Level</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        @php
                            $levelText = '-';
                            $levelColor = 'text-gray-500';
                            switch($user->role) {
                                case 'area_manager': $levelText = 'TERTINGGI'; $levelColor = 'text-cyan-400'; break;
                                case 'store_manager': $levelText = 'KEPALA TOKO'; $levelColor = 'text-red-400'; break;
                                case 'hr': $levelText = 'HR DEPARTMENT'; $levelColor = 'text-orange-400'; break;
                                case 'admin': $levelText = 'ADMIN'; $levelColor = 'text-purple-400'; break;
                            }
                        @endphp
                        <tr>
                            <td class="font-bold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="border border-white/20 px-2 py-1 rounded text-[10px] font-mono uppercase text-cyan-neon">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td>
                                <span class="text-[10px] font-mono uppercase {{ $levelColor }} tracking-wider">
                                    {{ $levelText }}
                                </span>
                            </td>
                            <td class="text-right">
                                <form action="{{ route('admin.changePassword', $user->id) }}" method="POST" class="flex items-center justify-end gap-2">
                                    @csrf
                                    @method('PUT')
                                    
                                    <input type="text" name="password" placeholder="New Pass..." 
                                        class="bg-transparent border-b border-white/10 w-24 text-xs focus:border-purple-neon focus:outline-none py-1 text-right text-gray-400 focus:text-white font-mono transition-colors"
                                        required minlength="8" maxlength="50">

                                    
                                    <button type="submit" class="text-xs hover:text-yellow-400 text-yellow-600 transition-colors font-mono" title="Simpan Password">
                                        [ SAVE ]
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PANEL MANAJEMEN PERIODE -->
        <div class="holo-card rounded-xl mb-12">
            <div class="p-4 border-b border-white/5 bg-pink-900/20 flex justify-between items-center">
                <h3 class="font-bold font-mono text-pink-500 flex items-center gap-2">
                    <i class="fas fa-calendar-alt"></i> MANAJEMEN PERIODE
                </h3>
                <div class="h-1 w-24 bg-pink-500 shadow-[0_0_10px_#ec4899]"></div>
            </div>
            <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- CREATE PERIOD -->
                <div class="lg:col-span-1">
                    <form action="{{ route('admin.period.store') }}" method="POST" class="flex flex-col gap-4">
                        @csrf
                        <div>
                            <label class="text-[10px] font-mono text-pink-500 block mb-2 tracking-widest">NAMA PERIODE</label>
                            <input type="text" name="name" placeholder="Ex: Selection 2024" class="tech-input w-full px-3 py-2 rounded text-sm" required>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-[10px] font-mono text-pink-500 block mb-2 tracking-widest">MULAI</label>
                                <input type="date" name="start_date" class="tech-input w-full px-3 py-2 rounded text-sm">
                            </div>
                            <div>
                                <label class="text-[10px] font-mono text-pink-500 block mb-2 tracking-widest">SELESAI</label>
                                <input type="date" name="end_date" class="tech-input w-full px-3 py-2 rounded text-sm">
                            </div>
                        </div>
                        <button type="submit" class="tech-btn px-4 py-2 rounded text-xs border-pink-500 text-pink-500 hover:bg-pink-500 hover:text-black">
                            BUAT PERIODE
                        </button>
                    </form>
                </div>

                <!-- LIST PERIODS -->
                <div class="lg:col-span-2 overflow-y-auto max-h-[200px]">
                    <table class="w-full tech-table text-sm">
                        <thead>
                            <tr>
                                <th class="text-pink-500!">Periode</th>
                                <th class="text-pink-500!">Status</th>
                                <th class="text-pink-500! text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($periods as $period)
                            <tr class="{{ $selectedPeriodId == $period->id ? 'bg-pink-500/10' : '' }}">
                                <td>
                                    <span class="font-bold block">{{ $period->name }}</span>
                                    <span class="text-[10px] text-gray-400">
                                        {{ $period->start_date ? $period->start_date->format('d M Y') : '-' }} s/d 
                                        {{ $period->end_date ? $period->end_date->format('d M Y') : '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if($period->is_active)
                                        <span class="border border-green-500 text-green-500 px-2 py-1 rounded text-[10px] font-mono animate-pulse">AKTIF (INPUT)</span>
                                    @else
                                        <span class="text-gray-500 text-[10px] font-mono">ARSIP</span>
                                    @endif
                                </td>
                                <td class="text-right flex justify-end gap-2 items-center pt-3">
                                    <!-- Button View Candidates (Dashboard Filter) -->
                                    <a href="{{ route('dashboard.admin', ['period_id' => $period->id]) }}" 
                                       class="text-[10px] px-2 py-1 border border-white/20 hover:bg-white/10 rounded font-mono text-cyan-neon" title="Lihat Kandidat">
                                        <i class="fas fa-users"></i> DATA
                                    </a>

                                    <!-- [ADD] Button View Results (Consensus Page) -->
                                    <a href="{{ route('consensus.index', ['period_id' => $period->id]) }}" 
                                       class="text-[10px] px-2 py-1 border border-yellow-500/50 text-yellow-500 hover:bg-yellow-500/10 rounded font-mono" title="Lihat Hasil Akhir/Ranking">
                                        <i class="fas fa-trophy"></i> HASIL
                                    </a>

                                    <!-- Button Activate -->
                                    @if(!$period->is_active)
                                    <form action="{{ route('admin.period.activate', $period->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-[10px] px-2 py-1 border border-green-500/50 text-green-500 hover:bg-green-500/10 rounded font-mono" title="Aktifkan untuk input data">
                                            <i class="fas fa-check"></i> SET AKTIF
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @php
            $criteriaCount = $criterias->count();
            $nextCodeNumber = $criteriaCount + 1;
            $nextCode = 'C' . $nextCodeNumber;
            $suggestNewWeight = $criteriaCount > 0 ? 1 / ($criteriaCount + 1) : 1;
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- PANEL KANDIDAT -->
            <div class="holo-card rounded-xl flex flex-col">
                <div class="p-4 border-b border-white/5 bg-blue-900/20 flex justify-between items-center">
                    <h3 class="font-bold font-mono text-cyan-neon flex items-center gap-2">
                        <i class="fas fa-user-tie"></i> DATA KANDIDAT
                        @if($selectedPeriodId)
                            <span class="text-[10px] bg-cyan-900/50 px-2 py-1 rounded ml-2 border border-cyan-500/30">
                                FILTER: {{ $periods->firstWhere('id', $selectedPeriodId)->name ?? 'Unknown' }}
                            </span>
                        @endif
                    </h3>
                    <div class="text-[10px] font-mono text-gray-500">CANDIDATES</div>
                </div>
                
                <div class="p-6 flex-1">
                    
                    <!-- FORM TAMBAH KANDIDAT -->
                    @if($activePeriod && $activePeriod->id == $selectedPeriodId)
                    <form action="{{ route('admin.candidate.store') }}" method="POST" class="mb-8 grid grid-cols-4 gap-4 items-end">
                        @csrf
                        
                        <div class="col-span-2">
                            <label class="text-[10px] font-mono text-cyan-neon block mb-2 tracking-widest">NAMA KANDIDAT</label>
                            <input 
                                type="text" 
                                name="name" 
                                placeholder="NAMA LENGKAP" 
                                class="tech-input w-full px-3 py-2 rounded text-sm uppercase"
                                required
                                maxlength="60"
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-Z ]/g, '');"
                            >
                        </div>
                        
                        <div>
                            <label class="text-[10px] font-mono text-cyan-neon block mb-2 tracking-widest">UMUR</label>
                            <div class="neon-select">
                                <select 
                                    name="age" 
                                    class="tech-input w-full px-3 py-2 rounded text-sm text-center"
                                    required
                                >
                                    @for($i = 18; $i <= 63; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="text-[10px] font-mono text-cyan-neon block mb-2 tracking-widest">EXP (THN)</label>
                                <div class="neon-select">
                                    <select 
                                        name="experience_year" 
                                        class="tech-input w-full px-3 py-2 rounded text-sm text-center"
                                        required
                                    >
                                        @for($i = 3; $i <= 20; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="tech-btn px-4 py-2 rounded text-xs h-[38px] self-end mb-px hover:bg-cyan-neon hover:text-black hover:shadow-[0_0_15px_#00e5ff] border-cyan-neon text-cyan-neon">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="mb-8 p-4 border border-yellow-500/30 bg-yellow-900/10 rounded text-center">
                        <p class="text-xs text-yellow-500 font-mono">
                            <i class="fas fa-lock"></i> 
                            MODE ARSIP: Tidak dapat menambah kandidat di periode ini.
                            <br>Aktifkan periode ini atau pilih periode aktif untuk mengedit.
                        </p>
                    </div>
                    @endif

                    <!-- TABEL EDIT KANDIDAT -->
                    <div class="overflow-y-auto max-h-[300px] pr-2">
                        <table class="w-full tech-table text-sm">
                            <thead>
                                <tr>
                                    <th class="text-cyan-neon!">Nama</th>
                                    <th class="text-cyan-neon! text-center">Umur</th>
                                    <th class="text-cyan-neon! text-center">Exp</th>
                                    <th class="text-right text-cyan-neon!">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($candidates as $cand)
                                <tr>
                                    <form action="{{ route('admin.candidate.update', $cand->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        
                                        <td class="p-2">
                                            <input 
                                                type="text" 
                                                name="name" 
                                                value="{{ $cand->name }}" 
                                                class="bg-transparent border-none text-white w-full focus:ring-0 focus:text-cyan-neon font-mono text-xs transition-colors uppercase"
                                                maxlength="60"
                                                oninput="this.value = this.value.toUpperCase().replace(/[^A-Z ]/g, '');"
                                            >
                                        </td>

                                        <td class="p-2">
                                            <div class="neon-select">
                                                <select 
                                                    name="age" 
                                                    class="bg-transparent tech-input border border-white/10 rounded text-gray-200 w-full focus:ring-0 focus:text-cyan-neon font-mono text-xs text-center transition-colors"
                                                    required
                                                >
                                                    @for($i = 18; $i <= 63; $i++)
                                                        <option value="{{ $i }}" {{ $cand->age == $i ? 'selected' : '' }}>
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </td>

                                        <td class="p-2">
                                            <div class="neon-select">
                                                <select 
                                                    name="experience_year" 
                                                    class="bg-transparent tech-input border border-white/10 rounded text-gray-200 w-full focus:ring-0 focus:text-cyan-neon font-mono text-xs text-center transition-colors"
                                                    required
                                                >
                                                    @for($i = 3; $i <= 20; $i++)
                                                        <option value="{{ $i }}" {{ $cand->experience_year == $i ? 'selected' : '' }}>
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </td>

                                        <td class="p-2 text-right flex justify-end gap-4 items-center">
                                            <button type="submit" class="text-cyan-500 hover:text-cyan-300 transition-colors" title="Simpan Perubahan">
                                                <i class="fas fa-save"></i>
                                            </button>
                                    </form> 
                                            <form action="{{ route('admin.candidate.delete', $cand->id) }}" method="POST" onsubmit="return confirm('Hapus kandidat ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-400 transition-colors" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PANEL KRITERIA -->
            <div class="holo-card rounded-xl flex flex-col">
                <div class="p-4 border-b border-white/5 bg-purple-900/20 flex justify-between items-center">
                    <h3 class="font-bold font-mono text-purple-neon flex items-center gap-2">
                        <i class="fas fa-list-check"></i> DATA KRITERIA
                    </h3>
                    <div class="text-[10px] font-mono text-gray-500">CRITERIAS</div>
                </div>
                
                <div class="p-6">
                    <!-- FORM TAMBAH KRITERIA -->
                    <form action="{{ route('admin.criteria.store') }}" method="POST" class="mb-8 grid grid-cols-4 gap-2">
                        @csrf
                        <input 
                            type="text" 
                            name="code" 
                            value="{{ $nextCode }}" 
                            class="tech-input px-3 py-2 rounded text-sm text-center font-mono tracking-widest"
                            readonly
                            pattern="^C[0-9]+$"
                            title="Kode otomatis C diikuti angka"
                        >
                        <input 
                            type="text" 
                            name="name" 
                            placeholder="NAMA KRITERIA" 
                            class="tech-input col-span-2 px-3 py-2 rounded text-sm"
                            required
                            maxlength="60"
                            oninput="this.value = this.value.replace(/[^A-Za-z ]/g,'');"
                        >
                        <div class="neon-select">
                            <select name="type" class="tech-input px-3 py-2 rounded text-sm bg-black">
                                <option value="benefit">BENEFIT</option>
                                <option value="cost">COST</option>
                            </select>
                        </div>
                        <input 
                            type="number" 
                            step="0.01" 
                            min="0" 
                            max="1" 
                            name="weight" 
                            id="new-criteria-weight"
                            value="{{ number_format($suggestNewWeight, 4, '.', '') }}"
                            placeholder="0.10" 
                            class="tech-input col-span-3 px-3 py-2 rounded text-sm"
                            required
                        >
                        <button type="submit" class="tech-btn px-4 py-2 rounded text-xs flex justify-center items-center">
                            <i class="fas fa-plus"></i>
                        </button>
                    </form>

                    <!-- TABEL EDIT KRITERIA -->
                    <div class="overflow-y-auto max-h-[300px] pr-2">
                        <table class="w-full tech-table text-sm">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Bobot</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criterias as $crit)
                                <tr>
                                    <form action="{{ route('admin.criteria.update', $crit->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <td class="font-mono font-bold text-purple-400">
                                            C{{ $loop->iteration }}
                                        </td>
                                        <td>
                                            <input 
                                                type="text" 
                                                name="name" 
                                                value="{{ $crit->name }}" 
                                                class="bg-transparent border-b border-white/10 w-full text-xs focus:border-purple-neon focus:outline-none py-1"
                                                maxlength="60"
                                                oninput="this.value = this.value.replace(/[^A-Za-z ]/g,'');"
                                            >
                                        </td>
                                        <td>
                                            <input 
                                                type="number" 
                                                step="0.01" 
                                                min="0" 
                                                max="1" 
                                                name="weight" 
                                                value="{{ number_format($crit->weight, 4, '.', '') }}" 
                                                class="bg-transparent border-b border-white/10 w-16 text-xs focus:border-purple-neon focus:outline-none py-1 text-center criteria-weight-input"
                                            >
                                        </td>
                                        <td class="text-right flex justify-end gap-3 items-center pt-3">
                                            <button type="submit" class="text-purple-400 hover:text-white transition-colors">
                                                <i class="fas fa-save"></i>
                                            </button>
                                    </form>
                                            <form action="{{ route('admin.criteria.delete', $crit->id) }}" method="POST" onsubmit="return confirm('Hapus kriteria?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-400 transition-colors">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex items-center justify-between text-[11px] font-mono">
                        <div class="flex flex-col gap-1">
                            <span class="text-gray-400 tracking-widest">TOTAL BOBOT KRITERIA</span>
                            <span id="total-weight-value" class="text-emerald-400 text-sm">0.0000</span>
                        </div>
                        <button 
                            type="button" 
                            onclick="autoNormalizeWeights()" 
                            class="px-3 py-1 rounded border border-cyan-neon/60 text-[10px] font-mono tracking-widest text-cyan-neon hover:bg-cyan-neon/10 hover:shadow-[0_0_15px_#00e5ff]"
                        >
                            AUTO NORMALISASI
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <footer class="mt-12 border-t border-white/5 pt-6 flex justify-between text-[10px] font-mono text-gray-600 tracking-widest uppercase">
            <div>GDSS PROJECT 2025</div>
            <div>ADMIN</div>
        </footer>

    </div>

    <!-- MODAL LOGOUT -->
    <div id="shutdown-modal" class="fixed inset-0 hidden items-center justify-center backdrop-blur-sm bg-black/80 transition-opacity duration-300 opacity-0">
        
        <div class="holo-card border border-red-500/50 shadow-[0_0_50px_rgba(239,68,68,0.2)] p-1 max-w-sm w-full transform scale-90 transition-transform duration-300" id="modal-box">
            <div class="bg-[#0a0505]/90 p-6 relative overflow-hidden">
                
                <div class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-transparent via-red-500 to-transparent"></div>
                <div class="absolute top-0 left-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-10"></div>

                <div class="text-center mb-6 relative z-10">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-500 animate-pulse mb-4"></i>
                    <h3 class="font-mono text-2xl font-bold text-white tracking-tighter">PEMBERITAHUAN</h3>
                    <p class="font-mono text-[10px] text-red-400 tracking-[0.3em] mt-1">Tetap ingin logout?</p>
                </div>

                <div class="flex gap-3 relative z-10">
                    <button onclick="toggleShutdown()" class="flex-1 py-3 border border-gray-600 text-gray-400 font-mono text-xs hover:bg-gray-800 hover:text-white transition-colors">
                        BATAL
                    </button>
                    
                    <button onclick="confirmLogout()" class="flex-1 py-3 bg-red-600 text-black font-bold font-mono text-xs hover:bg-red-500 hover:shadow-[0_0_20px_rgba(220,38,38,0.6)] transition-all tracking-wider">
                        LOGOUT
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
        // --- 1. BOOT SEQUENCE ---
        window.addEventListener('load', () => {
            const bootBar = document.getElementById('boot-bar');
            const bootScreen = document.getElementById('boot-screen');
            const mainInterface = document.getElementById('main-interface');

            setTimeout(() => { bootBar.style.width = "100%"; }, 100);

            setTimeout(() => {
                bootScreen.style.opacity = "0";
                setTimeout(() => { bootScreen.style.display = "none"; }, 500);
                mainInterface.style.opacity = "1";
            }, 2200);
        });

        // --- 2. CLOCK ---
        setInterval(() => {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('en-GB');
        }, 1000);

        // --- 3. WARP DRIVE PARTICLES ---
        const canvas = document.getElementById('warp-canvas');
        const ctx = canvas.getContext('2d');
        let width, height, stars = [], warpSpeed = 0.5;

        function resize() {
            width = window.innerWidth; height = window.innerHeight;
            canvas.width = width; canvas.height = height;
        }
        window.addEventListener('resize', resize);
        resize();

        for(let i = 0; i < 600; i++) {
            stars.push({ x: Math.random() * width - width/2, y: Math.random() * height - height/2, z: Math.random() * width });
        }

        function drawStars() {
            ctx.fillStyle = "rgba(5, 0, 10, 0.5)"; 
            ctx.fillRect(0, 0, width, height);
            
            const cx = width / 2; const cy = height / 2;

            stars.forEach(star => {
                star.z -= warpSpeed;
                if(star.z <= 0) { star.z = width; star.x = Math.random() * width - width/2; star.y = Math.random() * height - height/2; }
                
                const x = cx + (star.x / star.z) * width;
                const y = cy + (star.y / star.z) * width;
                const size = (1 - star.z / width) * 2;
                const alpha = (1 - star.z / width);
                
                ctx.fillStyle = `rgba(217, 70, 239, ${alpha})`;
                
                ctx.beginPath();
                ctx.arc(x, y, size, 0, Math.PI * 2);
                ctx.fill();
            });
            requestAnimationFrame(drawStars);
        }
        drawStars();

        // --- 4. MAGNETIC CURSOR ---
        const cursor = document.getElementById('cursor');
        const cursorDot = document.getElementById('cursor-dot');
        let mouseX = 0, mouseY = 0, cursorX = 0, cursorY = 0;

        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX; mouseY = e.clientY;
            cursorDot.style.left = mouseX + 'px'; cursorDot.style.top = mouseY + 'px';
        });

        function animateCursor() {
            cursorX += (mouseX - cursorX) * 0.15; cursorY += (mouseY - cursorY) * 0.15;
            cursor.style.left = cursorX + 'px'; cursor.style.top = cursorY + 'px';
            requestAnimationFrame(animateCursor);
        }
        animateCursor();

        const targets = document.querySelectorAll('button, input, a, select');
        targets.forEach(target => {
            target.addEventListener('mouseenter', () => { cursor.classList.add('cursor-hover'); });
            target.addEventListener('mouseleave', () => { cursor.classList.remove('cursor-hover'); });
        });

        // --- 5. FUNGSI MODAL SHUTDOWN ---
        const modal = document.getElementById('shutdown-modal');
        const modalBox = document.getElementById('modal-box');

        function toggleShutdown() {
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modal.classList.add('flex');
                    modalBox.classList.remove('scale-90');
                    modalBox.classList.add('scale-100');
                }, 10);
            } else {
                modal.classList.add('opacity-0');
                modalBox.classList.remove('scale-100');
                modalBox.classList.add('scale-90');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 300);
            }
        }

        function confirmLogout() {
            const btn = event.currentTarget;
            btn.innerHTML = "DISCONNECTING...";
            
            document.body.style.filter = "brightness(0) blur(10px)";
            document.body.style.transition = "all 0.5s";

            setTimeout(() => {
                document.getElementById('logout-form').submit();
            }, 800);
        }

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                toggleShutdown();
            }
        });

        // --- 6. LOGIKA BOBOT KRITERIA & NORMALISASI ---
        function handleWeightInput(el) {
            let v = el.value.replace(',', '.');
            v = v.replace(/[^0-9.]/g, '');
            const parts = v.split('.');
            if (parts.length > 2) {
                v = parts[0] + '.' + parts.slice(1).join('');
            }
            let num = parseFloat(v);
            if (isNaN(num)) {
                el.value = '';
            } else {
                if (num < 0) num = 0;
                if (num > 1) num = 1;
                el.value = num.toString();
            }
        }

        function updateTotalWeight() {
            const inputs = document.querySelectorAll('.criteria-weight-input');
            let total = 0;
            inputs.forEach(i => {
                const val = parseFloat(i.value);
                if (!isNaN(val)) total += val;
            });
            const display = document.getElementById('total-weight-value');
            if (display) {
                display.textContent = total.toFixed(4);
                display.classList.remove('text-red-400', 'text-yellow-300', 'text-emerald-400');
                if (Math.abs(total - 1) < 0.0001) {
                    display.classList.add('text-emerald-400');
                } else if (total > 1) {
                    display.classList.add('text-red-400');
                } else {
                    display.classList.add('text-yellow-300');
                }
            }
        }

        function autoNormalizeWeights() {
            const inputs = document.querySelectorAll('.criteria-weight-input');
            const arr = [];
            inputs.forEach(i => {
                let v = parseFloat(i.value);
                if (isNaN(v) || v < 0) v = 0;
                arr.push({el: i, val: v});
            });
            const n = arr.length;
            if (!n) return;
            let total = arr.reduce((s,w) => s + w.val, 0);

            if (total <= 0) {
                const equal = 1 / n;
                arr.forEach(w => w.el.value = equal.toFixed(4));
            } else {
                arr.forEach(w => {
                    const norm = w.val / total;
                    w.el.value = norm.toFixed(4);
                });
            }
            updateTotalWeight();
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.criteria-weight-input').forEach(input => {
                input.addEventListener('input', function() {
                    handleWeightInput(this);
                    updateTotalWeight();
                });
            });

            const newWeight = document.getElementById('new-criteria-weight');
            if (newWeight) {
                newWeight.addEventListener('input', function() {
                    handleWeightInput(this);
                });
            }

            autoNormalizeWeights();
        });
    </script>
</body>
</html>
