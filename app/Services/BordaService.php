<?php

namespace App\Services;

use App\Models\TopsisResult;
use App\Models\BordaResult;
use App\Models\ConsensusLog;
use App\Models\User;
use App\Models\Candidate;
use App\Models\Evaluation; // Tambahkan ini
use Illuminate\Support\Facades\DB;

class BordaService
{
    protected $topsisService;

    // Inject TopsisService agar bisa dipanggil otomatis
    public function __construct(TopsisService $topsisService)
    {
        $this->topsisService = $topsisService;
    }

    public function calculateConsensus($userId)
    {
        // 1. Validasi: Hanya Area Manager
        $user = User::find($userId);
        if ($user->role !== 'area_manager') {
            throw new \Exception("Akses Ditolak. Hanya Area Manager yang bisa memicu konsensus.");
        }

        // Tentukan Active Period
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        if (!$activePeriod) {
            throw new \Exception("Tidak ada periode aktif.");
        }
        $periodId = $activePeriod->id;

        // === [FIX UTAMA: AUTO-CALCULATE TOPSIS] ===
        // Sebelum Borda jalan, kita wajib hitung TOPSIS untuk setiap user yang sudah input nilai.
        
        // Ambil ID semua user yang sudah melakukan evaluasi DI PERIODE INI
        $evaluatorIds = Evaluation::whereHas('candidate', function($q) use ($periodId) {
            $q->where('period_id', $periodId);
        })->select('user_id')->distinct()->pluck('user_id');

        if ($evaluatorIds->isEmpty()) {
            throw new \Exception("Data Kosong: Belum ada Decision Maker yang melakukan penilaian di periode aktif.");
        }

        // Hitung ulang TOPSIS untuk masing-masing user tersebut pada periode ini
        foreach ($evaluatorIds as $eid) {
            $this->topsisService->calculateByUser($eid, $periodId);
        }
        // ==========================================

        // 2. Ambil Hasil TOPSIS yang BARU SAJA dihitung (Hanya periode ini)
        $topsisResults = TopsisResult::whereHas('candidate', function($q) use ($periodId) {
            $q->where('period_id', $periodId);
        })->get();

        if ($topsisResults->isEmpty()) {
            throw new \Exception("Gagal menghitung TOPSIS. Pastikan data kriteria dan bobot lengkap.");
        }

        // 3. Algoritma Borda Count
        $totalCandidates = Candidate::where('period_id', $periodId)->count();
        $bordaScores = [];

        foreach ($topsisResults as $result) {
            $candidateId = $result->candidate_id;
            $rank = $result->rank;

            // Rumus: Poin = (Total Kandidat - Ranking + 1)
            // Ranking 1 dapat poin maksimal, Ranking terakhir dapat 1 poin
            $points = $totalCandidates - $rank + 1;

            if (!isset($bordaScores[$candidateId])) {
                $bordaScores[$candidateId] = 0;
            }
            $bordaScores[$candidateId] += $points;
        }

        // 4. Simpan Hasil ke Database
        DB::beginTransaction();
        try {
            // Buat Log Baru dengan Period ID
            $log = ConsensusLog::create([
                'triggered_by' => $userId,
                'period_id' => $periodId
            ]);

            // Sort skor dari tertinggi ke terendah
            arsort($bordaScores);

            $finalRank = 1;
            foreach ($bordaScores as $candId => $totalPoints) {
                BordaResult::create([
                    'consensus_log_id' => $log->id,
                    'candidate_id' => $candId,
                    'total_points' => $totalPoints,
                    'final_rank' => $finalRank++
                ]);
            }

            DB::commit();
            return $log->id;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // Lempar error asli agar terbaca di Controller
        }
    }
}