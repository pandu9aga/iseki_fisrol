<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\Patrol;
use App\Models\User;
use App\Models\Member;

class AverageController extends Controller
{
    public function index($patrolId)
    {
        $patrol = Patrol::findOrFail($patrolId);

        // Ambil semua data nilai beserta relasi user/member
        $nilaiRecords = Nilai::with(['user', 'member'])
            ->where('Id_Patrol', $patrolId)
            ->get();

        // 🔹 Ambil semua ID user & member yang terlibat
        $userIds = $nilaiRecords->pluck('Id_User')->filter()->unique();
        $memberIds = $nilaiRecords->pluck('Id_Member')->filter()->unique();

        // 🔹 Ambil nama dari tabel user dan member
        $userNames = User::whereIn('Id_User', $userIds)
            ->pluck('Name_User', 'Id_User')
            ->toArray();

        $memberNames = Member::whereIn('id', $memberIds)
            ->pluck('nama', 'id')
            ->toArray();

        // 🔹 Gabungkan ke koleksi auditor
        $auditors = collect();
        foreach ($nilaiRecords as $n) {
            $auditors->push((object)[
                'Id_User' => $n->Id_User,
                'Id_Member' => $n->Id_Member,
                'auditor_name' => $memberNames[$n->Id_Member] ?? $userNames[$n->Id_User] ?? 'Auditor',
            ]);
        }

        // 🔹 Hilangkan duplikat nama
        $auditors = $auditors->unique('auditor_name')->values();

        // Hitung rata-rata nilai per nomor dan total akhir
        $averagePerValue = $this->averagePerValue($patrolId);
        $finalAverage = $this->finalAverage($patrolId);

        return view('admins.reports.index', compact(
            'patrol',
            'auditors',
            'nilaiRecords',
            'averagePerValue',
            'finalAverage'
        ));
    }

    public function averagePerValue($patrolId)
    {
        $records = Nilai::where('Id_Patrol', $patrolId)->get();
        if ($records->isEmpty()) return [];

        $totalPerIndex = [];
        $countPerIndex = [];

        foreach ($records as $record) {
            $nilai = json_decode($record->Value_Nilai, true) ?: [];

            foreach ($nilai as $index => $value) {
                $index = (int) $index;
                $value = is_numeric($value) ? $value : 0;
                $totalPerIndex[$index] = ($totalPerIndex[$index] ?? 0) + $value;
                $countPerIndex[$index] = ($countPerIndex[$index] ?? 0) + 1;
            }
        }

        $averagePerIndex = [];
        foreach ($totalPerIndex as $index => $total) {
            $averagePerIndex[$index] = round($total / $countPerIndex[$index], 2);
        }

        return $averagePerIndex;
    }

    public function averagePerStep($patrolId, $step)
    {
        $records = Nilai::where('Id_Patrol', $patrolId)->get();
        if ($records->isEmpty()) return 0;

        $stepMapping = [
            1 => [1, 2, 3, 4, 5, 6],
            2 => [7, 8, 9, 10, 11, 12, 13, 14],
            3 => [15, 16, 17, 18, 19],
            4 => [20, 21, 22],
            5 => [23, 24, 25],
        ];

        $totalPerStep = 0;
        foreach ($records as $record) {
            $nilai = json_decode($record->Value_Nilai, true);
            if (!is_array($nilai)) $nilai = [];

            foreach ($stepMapping[$step] as $i) {
                $totalPerStep += isset($nilai[$i]) && is_numeric($nilai[$i]) ? $nilai[$i] : 0;
            }
        }

        return round($totalPerStep / $records->count(), 2);
    }

    public function averageTotal($patrolId)
    {
        $records = Nilai::where('Id_Patrol', $patrolId)->get();
        if ($records->isEmpty()) return 0;

        $totalAll = 0;
        foreach ($records as $record) {
            $nilai = json_decode($record->Value_Nilai, true);
            if (!is_array($nilai)) $nilai = [];

            for ($i = 1; $i <= 25; $i++) {
                $totalAll += isset($nilai[$i]) && is_numeric($nilai[$i]) ? $nilai[$i] : 0;
            }
        }

        return round($totalAll / $records->count(), 2);
    }

    public function finalAverage($patrolId)
    {
        $averagePerStep = [];
        for ($step = 1; $step <= 5; $step++) {
            $averagePerStep[$step] = $this->averagePerStep($patrolId, $step);
        }

        return round(array_sum($averagePerStep) / count($averagePerStep), 2);
    }

    public function getPatrolScores($patrolId)
    {
        $patrol = Patrol::findOrFail($patrolId);

        // ambil semua nilai untuk patrol ini beserta user/member
        $nilaiRecords = Nilai::with(['user', 'member'])->where('Id_Patrol', $patrolId)->get();

        // mapping per item, per penilai
        $scores = [];
        foreach ($nilaiRecords as $record) {
            $values = json_decode($record->Value_Nilai, true);
            if (!is_array($values)) $values = [];

            foreach ($values as $index => $value) {
                $scores[$index][] = [
                    'nilai' => $value,
                    'auditor' => $record->user?->Name_User ?? $record->member?->Name_Member
                ];
            }
        }

        return view('admins.reports.index', compact('patrol', 'scores'));
    }

    public function totalPerStepByAuditor($patrolId)
    {
        $records = Nilai::with(['user', 'member'])
            ->where('Id_Patrol', $patrolId)
            ->get();

        $stepMapping = [
            1 => [1, 2, 3, 4, 5, 6],
            2 => [7, 8, 9, 10, 11, 12, 13, 14],
            3 => [15, 16, 17, 18, 19],
            4 => [20, 21, 22],
            5 => [23, 24, 25],
        ];

        $result = [];

        foreach ($records as $record) {
            $auditorName = $record->user?->Name_User ?? $record->member?->nama ?? 'Auditor';
            $nilai = json_decode($record->Value_Nilai, true) ?: [];

            $totals = [];
            foreach ($stepMapping as $step => $indexes) {
                $sum = 0;
                foreach ($indexes as $i) {
                    $value = $nilai[$i] ?? 0;
                    if (!is_numeric($value)) $value = 0;
                    $sum += $value;
                }
                $totals[$step] = $sum;
            }

            $result[] = [
                'auditor' => $auditorName,
                'totals_per_step' => $totals,
            ];
        }

        return $result;
    }

    /**
     * Hitung total nilai per step untuk satu auditor (bisa user atau member)
     *
     * @param int $patrolId
     * @param int $step (1-5)
     * @param int|null $userId
     * @param int|null $memberId
     * @return int
     */
    public function calculateTotalForAuditor($patrolId, $step, $userId = null, $memberId = null)
    {
        // Validasi: harus salah satu diisi
        if (!$userId && !$memberId) {
            return 0;
        }

        $query = Nilai::where('Id_Patrol', $patrolId);

        if ($userId) {
            $query->where('Id_User', $userId);
        }

        if ($memberId) {
            $query->where('Id_Member', $memberId);
        }

        $record = $query->first();
        if (!$record) {
            return 0;
        }

        $nilai = json_decode($record->Value_Nilai, true) ?? [];

        $stepMapping = [
            1 => [1, 2, 3, 4, 5, 6],
            2 => [7, 8, 9, 10, 11, 12, 13, 14],
            3 => [15, 16, 17, 18, 19],
            4 => [20, 21, 22],
            5 => [23, 24, 25],
        ];

        if (!isset($stepMapping[$step])) {
            return 0;
        }

        $total = 0;
        foreach ($stepMapping[$step] as $itemIndex) {
            $value = $nilai[$itemIndex] ?? 0;
            // Handle string "0", "3", atau "-"
            if (is_numeric($value)) {
                $total += (int) $value;
            } else {
                // Jika string seperti "3", coba konversi
                if (is_string($value) && ctype_digit($value)) {
                    $total += (int) $value;
                }
                // Jika "-", abaikan (0)
            }
        }

        return $total;
    }
}
