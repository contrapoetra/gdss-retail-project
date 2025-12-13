<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Candidate;
use App\Models\Criteria;
use App\Models\Period;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // 1. Dashboard Utama Admin
    public function index(Request $request)
    {
        $users = User::all();
        
        // Handle Period Filter
        $activePeriod = Period::where('is_active', true)->first();
        $selectedPeriodId = $request->query('period_id', $activePeriod ? $activePeriod->id : null);
        
        // Fetch candidates for the selected period
        $candidates = Candidate::where('period_id', $selectedPeriodId)->get();
        
        $criterias = Criteria::all();
        $periods = Period::orderBy('created_at', 'desc')->get();

        return view('dashboard.admin', compact('users', 'candidates', 'criterias', 'periods', 'activePeriod', 'selectedPeriodId'));
    }

    // --- MANAJEMEN PERIODE ---
    public function storePeriod(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        // If no periods exist, make this one active by default? 
        // Or simply just create it. User can set active later.
        // Let's check if there is an active period.
        $hasActive = Period::where('is_active', true)->exists();

        Period::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => !$hasActive, // Auto activate if it's the first one
        ]);

        return back()->with('success', 'Periode berhasil dibuat.');
    }

    public function setActivePeriod($id)
    {
        // Use transaction to ensure atomicity
        DB::transaction(function () use ($id) {
            // Deactivate all
            Period::query()->update(['is_active' => false]);
            // Activate target
            Period::where('id', $id)->update(['is_active' => true]);
        });

        return back()->with('success', 'Periode aktif berhasil diubah.');
    }

    // --- MANAJEMEN USER (CHANGE PASSWORD) ---
    public function changePassword(Request $request, $id)
    {
        // Validasi input password baru
        $request->validate([
            'password' => 'required|string|min:8', // Minimal 8 karakter (bisa disesuaikan)
        ]);

        $user = User::findOrFail($id);
        // Mengubah password sesuai inputan admin
        $user->password = Hash::make($request->password); 
        $user->save();

        return back()->with('success', 'Password user ' . $user->name . ' berhasil diubah.');
    }
    
    // --- MANAJEMEN KANDIDAT ---
    public function storeCandidate(Request $request)
    {
        // Validasi semua field wajib
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:18',
            'experience_year' => 'required|integer|min:0',
            'full_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'domicile_city' => 'nullable|string|max:255',
            'portfolio_link' => 'nullable|url|max:255',
        ]);

        $activePeriod = Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return back()->with('error', 'Gagal: Tidak ada periode aktif. Buat/Aktifkan periode dulu.');
        }

        Candidate::create([
            'period_id' => $activePeriod->id,
            'name' => strtoupper($request->name),
            'age' => $request->age,
            'experience_year' => $request->experience_year,
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'domicile_city' => $request->domicile_city,
            'portfolio_link' => $request->portfolio_link,
        ]);
        
        return back()->with('success', 'Kandidat berhasil ditambahkan ke periode ' . $activePeriod->name . '.');
    }

    public function updateCandidate(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $candidate->update($request->all());
        return back()->with('success', 'Data kandidat berhasil diupdate.');
    }

    public function deleteCandidate($id)
    {
        // Cek apakah kandidat sudah pernah dinilai
        if (\App\Models\Evaluation::where('candidate_id', $id)->exists()) {
            return back()->with('error', 'Gagal hapus! Kandidat ini sudah memiliki data penilaian. Hapus penilaian dulu.');
        }
        
        Candidate::destroy($id);
        return back()->with('success', 'Kandidat berhasil dihapus.');
    }

    // --- MANAJEMEN KRITERIA ---
    public function storeCriteria(Request $request)
    {
        // Validasi sederhana
        $request->validate(['code' => 'required', 'name' => 'required', 'weight' => 'required']);
        $criteria = Criteria::create($request->all());
        return back()->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function updateCriteria(Request $request, $id)
    {
        $criteria = Criteria::findOrFail($id);
        $criteria->update($request->all());
        return back()->with('success', 'Kriteria berhasil diupdate.');
    }
    
    public function deleteCriteria($id)
    {
        // Cek apakah kriteria sudah digunakan
        if (\App\Models\Evaluation::where('criteria_id', $id)->exists()) {
            return back()->with('error', 'Gagal hapus! Kriteria ini sedang digunakan dalam penilaian.');
        }

        Criteria::destroy($id);
        return back()->with('success', 'Kriteria berhasil dihapus.');
    }
}