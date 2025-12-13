<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BordaService;
use App\Models\ConsensusLog;
use App\Models\BordaResult;
use App\Models\Criteria;
use App\Models\Candidate;
use App\Models\Evaluation;
use App\Models\TopsisResult;
use Illuminate\Support\Facades\Auth;

class ConsensusController extends Controller
{
    public function index(Request $request)
    {
        // 1. Determine Period
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        $selectedPeriodId = $request->query('period_id', $activePeriod ? $activePeriod->id : null);
        $periods = \App\Models\Period::orderBy('created_at', 'desc')->get();

        // 2. Fetch Latest Log for Selected Period
        $latestLog = ConsensusLog::where('period_id', $selectedPeriodId)->latest()->first();

        if (!$latestLog) {
            // Jika belum ada log, return view kosong
            return view('evaluation.result', [
                'hasResult' => false,
                'periods' => $periods,
                'selectedPeriodId' => $selectedPeriodId
            ]);
        }

        // 1. Hasil Final Borda
        $results = BordaResult::where('consensus_log_id', $latestLog->id)
                    ->with('candidate')
                    ->orderBy('final_rank', 'asc')
                    ->get();

        // 2. Data Transparansi: Rincian Poin Borda
        $topsisBreakdown = TopsisResult::with(['user', 'candidate'])
                            ->whereHas('candidate', function($q) use ($selectedPeriodId) {
                                $q->where('period_id', $selectedPeriodId);
                            })
                            ->get()
                            ->groupBy('candidate_id');

        // 3. Data Transparansi: Matriks TOPSIS 
        // FIX: Jangan pakai Auth::id() saja, karena Area Manager mungkin tidak input data.
        // Kita ambil user pertama yang ada di tabel evaluasi (untuk kandidat periode ini) sebagai sampel transparansi.
        
        $sampleEvaluation = Evaluation::whereHas('candidate', function($q) use ($selectedPeriodId) {
            $q->where('period_id', $selectedPeriodId);
        })->first();

        $sampleUser = $sampleEvaluation ? $sampleEvaluation->user_id : Auth::id();
        
        $criterias = Criteria::all();
        $candidates = Candidate::where('period_id', $selectedPeriodId)->get();
        $evaluations = Evaluation::where('user_id', $sampleUser)
                        ->whereHas('candidate', function($q) use ($selectedPeriodId) {
                            $q->where('period_id', $selectedPeriodId);
                        })
                        ->get();

        // A. Matriks Keputusan (X)
        $matrixX = [];
        foreach($candidates as $can) {
            foreach($criterias as $crit) {
                $val = $evaluations->where('candidate_id', $can->id)
                                   ->where('criteria_id', $crit->id)
                                   ->first()->score ?? 0;
                $matrixX[$can->id][$crit->id] = $val;
            }
        }

        // B. Matriks Normalisasi (R)
        $matrixR = [];
        foreach($criterias as $crit) {
            $sumSq = 0;
            foreach($candidates as $can) {
                $sumSq += pow($matrixX[$can->id][$crit->id], 2);
            }
            $divisor = sqrt($sumSq);
            foreach($candidates as $can) {
                // FIX: Cegah division by zero
                $matrixR[$can->id][$crit->id] = $divisor > 0 ? $matrixX[$can->id][$crit->id] / $divisor : 0;
            }
        }

        // C. Matriks Terbobot (Y)
        $matrixY = [];
        foreach($candidates as $can) {
            foreach($criterias as $crit) {
                $matrixY[$can->id][$crit->id] = $matrixR[$can->id][$crit->id] * $crit->weight;
            }
        }

        return view('evaluation.result', [
            'hasResult' => true,
            'results' => $results,
            'lastRun' => $latestLog->created_at,
            'topsisBreakdown' => $topsisBreakdown,
            'criterias' => $criterias,
            'candidates' => $candidates,
            'matrixX' => $matrixX,
            'matrixR' => $matrixR,
            'matrixY' => $matrixY,
            'periods' => $periods,
            'selectedPeriodId' => $selectedPeriodId
        ]);
    }

    public function generate(BordaService $bordaService)
    {
        try {
            // Logika kalkulasi sudah dipindah ke Service
            $bordaService->calculateConsensus(Auth::id());
            return redirect()->route('consensus.index')->with('success', 'Konsensus Borda berhasil dihitung ulang!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghitung: ' . $e->getMessage());
        }
    }
}