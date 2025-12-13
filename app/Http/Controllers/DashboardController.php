<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Period;
use App\Models\Evaluation;
use App\Models\TopsisResult;
use App\Models\ConsensusLog;
use App\Models\BordaResult;
use App\Models\Candidate;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        // 1. Data Logic
        $activePeriod = Period::active()->first();
        $hasEvaluated = false;
        $myWinnerName = null;
        $globalWinnerName = null;
        
        if ($activePeriod) {
            // Check Evaluation Status
            $hasEvaluated = Evaluation::where('user_id', $user->id)
                ->whereHas('candidate', function($q) use ($activePeriod) {
                    $q->where('period_id', $activePeriod->id);
                })
                ->exists();

            // Check Personal Winner (TOPSIS)
            if ($hasEvaluated) {
                $myResult = TopsisResult::where('user_id', $user->id)
                    ->where('rank', 1)
                    ->whereHas('candidate', function($q) use ($activePeriod) {
                        $q->where('period_id', $activePeriod->id);
                    })
                    ->with('candidate')
                    ->first();
                
                if ($myResult && $myResult->candidate) {
                    $myWinnerName = $myResult->candidate->name;
                }
            }

            // Check Global Winner (BORDA)
            $consensusLog = ConsensusLog::where('period_id', $activePeriod->id)->latest()->first();
            if ($consensusLog) {
                $bordaResult = BordaResult::where('consensus_log_id', $consensusLog->id)
                    ->where('final_rank', 1)
                    ->with('candidate')
                    ->first();
                if ($bordaResult && $bordaResult->candidate) {
                    $globalWinnerName = $bordaResult->candidate->name;
                }
            }
        }

        // 2. UI Configuration based on Role
        $ui = $this->getUiConfig($role);

        // 3. Stats
        $candidateCount = Candidate::where('period_id', $activePeriod?->id)->count();

        return view('dashboard.decision_maker', compact(
            'user', 
            'activePeriod', 
            'hasEvaluated', 
            'myWinnerName', 
            'globalWinnerName', 
            'ui', 
            'candidateCount'
        ));
    }

    private function getUiConfig($role)
    {
        switch ($role) {
            case 'area_manager':
                return [
                    'theme' => 'cyan', // cyan-500
                    'secondary_theme' => 'blue',
                    'header_title' => 'AREA COMMAND CENTER',
                    'role_label' => 'AREA MANAGER',
                    'description_html' => '
                        <span class="text-cyan-400 mr-2">>></span> ID: <strong class="text-white">AREA MANAGER</strong><br>
                        <span class="text-cyan-400 mr-2">>></span> LEVEL AKSES: <strong class="text-emerald-400">TERTINGGI</strong><br>
                        <span class="text-cyan-400 mr-2">>></span> TUGAS: EKSEKUSI PENILAIAN & ANALISA ALGORITMA BORDA.
                    ',
                    'icon_main' => 'fa-crosshairs',
                    'objectives' => [
                        'Scanning & Penilaian Kandidat.',
                        'Stream Data HR & Store Manager.',
                        'Inisialisasi Algoritma Borda.'
                    ]
                ];
            case 'store_manager':
                return [
                    'theme' => 'red', // red-500
                    'secondary_theme' => 'orange',
                    'header_title' => 'PANEL KEPALA TOKO',
                    'role_label' => 'KEPALA TOKO',
                    'description_html' => '
                        <span class="text-red-500 mr-2">>></span> OTORITAS: <strong class="text-white">KEPALA TOKO</strong><br>
                        <span class="text-red-500 mr-2">>></span> FOKUS PENILAIAN: <strong class="text-rose-400">PERFORMA & KONDISI LAPANGAN</strong><br>
                        <span class="text-red-500 mr-2">>></span> STATUS: MENUNGGU OBSERVASI OBJEKTIF.
                    ',
                    'icon_main' => 'fa-store',
                    'objectives' => null // Store layout didn't have objectives box in previous files
                ];
            case 'hr':
                return [
                    'theme' => 'orange', // orange-500
                    'secondary_theme' => 'amber',
                    'header_title' => 'PANEL HUMAN RESOURCES',
                    'role_label' => 'HR DEPARTMENT',
                    'description_html' => '
                        <span class="text-orange-500 mr-2">>></span> OTORITAS: <strong class="text-white">HR DEPARTMENT</strong><br>
                        <span class="text-orange-500 mr-2">>></span> FOKUS PENILAIAN: <strong class="text-amber-400">INTEGRITAS & SOFT SKILL</strong><br>
                        <span class="text-orange-500 mr-2">>></span> STATUS: MENUNGGU INPUT DATA KANDIDAT.
                    ',
                    'icon_main' => 'fa-user-tie',
                    'objectives' => null
                ];
            default:
                // Fallback for unexpected roles
                return [
                    'theme' => 'gray',
                    'secondary_theme' => 'slate',
                    'header_title' => 'DASHBOARD',
                    'role_label' => 'USER',
                    'description_html' => 'Welcome.',
                    'icon_main' => 'fa-user',
                    'objectives' => null
                ];
        }
    }
}
