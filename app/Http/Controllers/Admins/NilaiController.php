<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Nilai;
use App\Models\Patrol;
use App\Models\User;
use App\Models\Member;

class NilaiController extends Controller
{
    // Tampilkan form penilaian
    public function index($id = null)
    {
        // Cegah akses tanpa login
        if (!Session::has('login_id')) {
            return redirect()->route('login')->withErrors([
                'unauthorized' => 'Silakan login terlebih dahulu.'
            ]);
        }

        // Ambil ID user & patrol
        $userId = Session::get('login_id');
        $patrolId = $id ?? Patrol::latest()->first()?->Id_Patrol;

        if (!$patrolId) {
            return back()->withErrors(['patrol' => 'Data patrol tidak ditemukan.']);
        }

        $patrol = Patrol::findOrFail($patrolId);
        $user = User::find($userId);

        // Ambil member yang login (misal kamu simpan Id_Employee di session)
        $member = Member::where('Id_Employee', Session::get('employee_id'))->first();

        // Ambil nilai yang sudah tersimpan (jika ada)
        $nilaiRecord = Nilai::where('Id_Patrol', $patrolId)
            ->where('Id_User', $userId)
            ->first();

        $existingNilai = $nilaiRecord ? json_decode($nilaiRecord->Value_Nilai, true) : [];

        return view('admins.nilais.index', compact('patrol', 'existingNilai', 'member', 'user'));
    }

    // Simpan atau update nilai
    public function store(Request $request, $patrolId)
    {
        $userId = Session::get('login_id');
        $employeeId = Session::get('employee_id');

        $values = $request->input('nilai'); // array: [index => value]

        if (!$values) {
            return back()->withErrors(['nilai' => 'Data nilai tidak boleh kosong.']);
        }

        // Buat atau update record
        Nilai::updateOrCreate(
            [
                'Id_Patrol' => $patrolId,
                'Id_User'   => $userId,
            ],
            [
                'Id_Patrol' => $patrolId,
                'Id_User'   => $userId,
                'Id_Member' => Member::where('Id_Employee', $employeeId)->value('Id_Member'),
                'Value_Nilai' => json_encode($values),
            ]
        );

        return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
    }

    // Total nilai per step
    public function totalPerStep($patrolId, $step)
    {
        $record = Nilai::where('Id_Patrol', $patrolId)
            ->where('Id_User', Session::get('login_id'))
            ->first();

        if (!$record) return 0;

        $nilai = json_decode($record->Value_Nilai, true) ?? [];

        $stepMapping = [
            1 => [1, 2, 3, 4, 5, 6],
            2 => [7, 8, 9, 10, 11, 12, 13, 14],
            3 => [15, 16, 17, 18, 19],
            4 => [20, 21, 22],
            5 => [23, 24, 25],
        ];

        return collect($stepMapping[$step] ?? [])->sum(fn($i) => $nilai[$i] ?? 0);
    }

    // Total semua nilai
    public function totalNilai($patrolId)
    {
        $record = Nilai::where('Id_Patrol', $patrolId)
            ->where('Id_User', Session::get('login_id'))
            ->first();

        if (!$record) return 0;

        $nilai = json_decode($record->Value_Nilai, true) ?? [];
        return array_sum(array_slice($nilai, 1, 25));
    }
}
