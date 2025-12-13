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
    public function index()
    {
        $latestLog = ConsensusLog::latest()->first();

        if (!$latestLog) {
            // Jika belum ada log, return view kosong
            return view('evaluation.result', ['hasResult' => false]);
        }

        // 1. Hasil Final Borda
        $results = BordaResult::where('consensus_log_id', $latestLog->id)
                    ->with('candidate')
                    ->orderBy('final_rank', 'asc')
                    ->get();

        // 2. Data Transparansi: Rincian Poin Borda
        $topsisBreakdown = TopsisResult::with(['user', 'candidate'])
                            ->get()
                            ->groupBy('candidate_id');

        // 3. Data Transparansi: Matriks TOPSIS 
        // FIX: Jangan pakai Auth::id() saja, karena Area Manager mungkin tidak input data.
        // Kita ambil user pertama yang ada di tabel evaluasi sebagai sampel transparansi.
        $sampleUser = Evaluation::first()->user_id ?? Auth::id();
        
        $criterias = Criteria::all();
        $candidates = Candidate::all();
        $evaluations = Evaluation::where('user_id', $sampleUser)->get();

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
            'matrixY' => $matrixY
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