<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Criteria;
use App\Models\Evaluation;
use App\Models\Period;
use App\Services\TopsisService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    protected $topsisService;

    public function __construct(TopsisService $topsisService)
    {
        $this->topsisService = $topsisService;
    }

    // 1. Tampilkan Form Input
    public function index()
    {
        $activePeriod = Period::where('is_active', true)->first();

        if (!$activePeriod) {
             return view('evaluation.input', [
                 'candidates' => collect([]), 
                 'criterias' => Criteria::all(), 
                 'hasEvaluated' => false,
                 'error' => 'Tidak ada periode seleksi yang aktif saat ini.'
             ]);
        }

        // Ambil data penilaian sebelumnya (jika ada)
        // Format: [candidate_id => [criteria_id => score]]
        $existingEvaluations = Evaluation::where('user_id', Auth::id())
            ->whereHas('candidate', function($q) use ($activePeriod) {
                $q->where('period_id', $activePeriod->id);
            })
            ->get()
            ->groupBy('candidate_id')
            ->map(function ($items) {
                return $items->pluck('score', 'criteria_id');
            });
            
        $hasEvaluated = $existingEvaluations->isNotEmpty();

        // Ambil data master active
        $candidates = Candidate::where('period_id', $activePeriod->id)->get();
        $criterias = Criteria::all();

        return view('evaluation.input', compact('candidates', 'criterias', 'hasEvaluated', 'activePeriod', 'existingEvaluations'));
    }

    // 2. Simpan Nilai ke Database
    public function store(Request $request)
    {
        // Validasi dasar
        $request->validate([
            'scores' => 'required|array',
        ]);

        $activePeriod = Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return back()->with('error', 'Tidak ada periode aktif.');
        }

        DB::beginTransaction();
        try {
            $userId = Auth::id();

            // Hapus penilaian lama JIKA ada, tapi HANYA untuk kandidat di periode ini
            // Agar history tahun lalu tidak hilang
            Evaluation::where('user_id', $userId)
                ->whereHas('candidate', function($q) use ($activePeriod) {
                    $q->where('period_id', $activePeriod->id);
                })
                ->delete();

            // Loop input dari form
            // Struktur name di HTML nanti: scores[candidate_id][criteria_id]
            foreach ($request->scores as $candidateId => $criteriaScores) {
                // Optional: Verify candidate belongs to active period?
                // For now assuming UI handles it, but safety check:
                // $cand = Candidate::find($candidateId);
                // if($cand->period_id != $activePeriod->id) continue;

                foreach ($criteriaScores as $criteriaId => $score) {
                    Evaluation::create([
                        'user_id' => $userId,
                        'candidate_id' => $candidateId,
                        'criteria_id' => $criteriaId,
                        'score' => $score
                    ]);
                }
            }

            // Otomatis Hitung TOPSIS Individu setelah save
            // Hitung untuk periode aktif saja
            $this->topsisService->calculateByUser($userId, $activePeriod->id);

            DB::commit();
            return redirect()->route('evaluation.index')->with('success', 'Penilaian berhasil disimpan & dihitung!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}