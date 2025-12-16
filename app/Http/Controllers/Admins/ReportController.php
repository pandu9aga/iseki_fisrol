<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Patrol;
use App\Models\Nilai;
use App\Models\User;
use App\Models\Member;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman daftar laporan
     */
    public function index()
    {
        if (!Session::has('login_id')) {
            return redirect()->route('login')->withErrors(['unauthorized' => 'Silakan login terlebih dahulu.']);
        }

        return view('admins.reports.index');
    }

    /**
     * Menampilkan detail laporan untuk satu patrol
     */
    public function show($id)
    {
        // Ambil semua nilai untuk 1 patrol
        $nilaiRecords = Nilai::where('Id_Patrol', $id)->get();

        // Ambil semua ID user dan member yang terlibat
        $userIds = $nilaiRecords->pluck('Id_User')->filter()->unique();
        $memberIds = $nilaiRecords->pluck('Id_Member')->filter()->unique();

        // Ambil nama dari dua tabel
        $userNames = User::whereIn('Id_User', $userIds)->pluck('Name_User', 'Id_User')->toArray();
        $memberNames = Member::on('rifa')->whereIn('id', $memberIds)->pluck('nama', 'id')->toArray();

        // Gabungkan nama auditor dari dua sumber
        $auditors = collect();

        foreach ($nilaiRecords as $n) {
            $auditors->push([
                'id_user' => $n->Id_User,
                'id_member' => $n->Id_Member,
                'auditor_name' => $memberNames[$n->Id_Member] ?? $userNames[$n->Id_User] ?? 'Auditor',
            ]);
        }

        // Hilangkan duplikat nama
        $auditors = $auditors->unique('auditor_name')->values();

        return view('admins.reports.show', compact('nilaiRecords', 'auditors'));
    }

}
