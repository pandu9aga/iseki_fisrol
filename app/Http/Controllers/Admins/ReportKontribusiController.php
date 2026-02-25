<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportKontribusiController extends Controller
{
    public function index(Request $request)
    {
        // Default to current month/year if not provided
        $month = $request->input('month', date('m'));
        $year  = $request->input('year', date('Y'));

        // === Query 1: Penemu Temuan (Based on Patrol Date) ===
        $penemuQuery = DB::table('temuans')
            ->join('patrols', 'temuans.Id_Patrol', '=', 'patrols.Id_Patrol')
            ->select('temuans.nik_penemu', DB::raw('COUNT(*) as total'))
            ->whereNotNull('temuans.nik_penemu')
            ->where('temuans.nik_penemu', '!=', '')
            ->whereMonth('patrols.Time_Patrol', $month)
            ->whereYear('patrols.Time_Patrol', $year)
            ->groupBy('temuans.nik_penemu');

        $penemuData = $penemuQuery->orderByDesc('total')->get();

        // Map employee names from rifa
        foreach ($penemuData as $row) {
            $emp = DB::connection('rifa')
                ->table('employees')
                ->where('nik', $row->nik_penemu)
                ->first(['nama']);
            $row->nama = $emp->nama ?? $row->nik_penemu;
        }

        // === Query 2: PIC Proses Perbaikan (Based on Patrol Date) ===
        $picQuery = DB::table('temuans')
            ->join('patrols', 'temuans.Id_Patrol', '=', 'patrols.Id_Patrol')
            ->select('temuans.pic_proses_nik', DB::raw('COUNT(*) as total'))
            ->whereNotNull('temuans.pic_proses_nik')
            ->where('temuans.pic_proses_nik', '!=', '')
            ->whereMonth('patrols.Time_Patrol', $month)
            ->whereYear('patrols.Time_Patrol', $year)
            ->groupBy('temuans.pic_proses_nik');

        $picData = $picQuery->orderByDesc('total')->get();

        // Map employee names from rifa
        foreach ($picData as $row) {
            $emp = DB::connection('rifa')
                ->table('employees')
                ->where('nik', $row->pic_proses_nik)
                ->first(['nama']);
            $row->nama = $emp->nama ?? $row->pic_proses_nik;
        }

        // Dropdown data
        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        // Dynamic years (e.g., from 2024 to current year + 1)
        $years = range(2024, date('Y') + 1);

        // === Query 3: List Patrols ===
        $patrolsList = DB::table('patrols')
            ->select('Id_Patrol', 'Name_Patrol', 'Time_Patrol')
            ->whereMonth('Time_Patrol', $month)
            ->whereYear('Time_Patrol', $year)
            ->orderBy('Time_Patrol', 'asc')
            ->get();

        return view('admins.reports.kontribusi', compact('penemuData', 'picData', 'month', 'year', 'months', 'years', 'patrolsList'));
    }
}
