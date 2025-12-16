<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nilai;
use App\Models\Patrol;
use App\Models\PatrolMember;
use App\Models\Member;
use Illuminate\Support\Facades\Session;

class UserNilaiController extends Controller
{
    // Menampilkan form penilaian
    public function index($id = null)
    {
        $patrolId = $id ?? Session::get('current_patrol_id', Patrol::latest()->first()->Id_Patrol);
        $patrol = Patrol::findOrFail($patrolId);

        $userId = Session::get('login_id');
        $memberId = Session::get('member_id'); // sekarang pakai session member langsung

        // Pastikan member diambil dari tabel yang baru (misal Employee)
        $member = Member::find($memberId);

        // Ambil nilai yang sudah ada
        $nilaiRecord = Nilai::where('Id_Patrol', $patrolId)
            ->where('Id_User', $userId)
            ->first();

        $existingNilai = $nilaiRecord ? json_decode($nilaiRecord->Value_Nilai, true) : [];

        return view('users.nilais.index', compact('patrol', 'existingNilai', 'member'));
    }

    // Menyimpan semua input nilai

    public function store(Request $request, $patrolId)
    {
        $userId = Session::get('login_id');
        $rawValues = $request->input('nilai', []);

        // Validasi nilai seperti biasa...

        $normalizedValues = [];
        for ($i = 1; $i <= 25; $i++) {
            $val = trim($rawValues[$i] ?? '');
            if ($val === '' || $val === null) {
                return back()->with('error', "Nilai nomor {$i} harus diisi.");
            }
            if ($val === '-') {
                $normalizedValues[$i] = '-';
            } elseif (is_numeric($val) && $val >= 0 && $val <= 4) {
                $normalizedValues[$i] = (int) $val;
            } else {
                return back()->with('error', "Nilai nomor {$i} tidak valid.");
            }
        }

        // Ambil member dari login
        $member = Member::where('nik', Session::get('login_nik'))->first();

        $dataUpdate = [
            'Value_Nilai' => json_encode($normalizedValues),
            'Id_User' => $userId,
            'Id_Patrol' => $patrolId,
            'Id_Member' => $member ? $member->id : null, // 💡 sama seperti di temuan
        ];

        Nilai::updateOrCreate(
            ['Id_Patrol' => $patrolId, 'Id_User' => $userId],
            $dataUpdate
        );

        return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
    }


    public function totalPerStep($patrolId, $step, $userId = null)
    {
        $userId = $userId ?? Session::get('login_id');

        $record = Nilai::where('Id_Patrol', $patrolId)
            ->where('Id_User', $userId)
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

        $total = 0;
        foreach ($stepMapping[$step] as $i) {
            $value = $nilai[$i] ?? 0;
            if ($value === '-' || !is_numeric($value)) {
                $value = 0;
            }
            $total += $value;
        }

        return $total;
    }

    public function totalNilai($patrolId)
    {
        $record = Nilai::where('Id_Patrol', $patrolId)
            ->where('Id_User', Session::get('login_id'))
            ->first();

        if (!$record) return 0;

        $nilai = json_decode($record->Value_Nilai, true) ?? [];

        $total = 0;
        for ($i = 1; $i <= 25; $i++) {
            $value = $nilai[$i] ?? 0;

            // Jika "-" atau bukan angka, anggap 0
            if ($value === '-' || !is_numeric($value)) {
                $value = 0;
            }

            $total += $value;
        }

        return $total;
    }
}
