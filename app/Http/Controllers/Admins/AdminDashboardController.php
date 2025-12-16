<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\Temuan;
use App\Models\Patrol;

class AdminDashboardController extends Controller
{
    public function index()
    {
        if (!Session::has('login_id')) {
            return redirect()->route('login')->withErrors(['unauthorized' => 'Silakan login terlebih dahulu.']);
        }

        // Statistik Patrol
        $totalPatrol = Patrol::count();

        // Hitung improvement (Done) & pending berdasarkan Status_Temuan
        $totalPerbaikan = Temuan::where('Status_Temuan', 'Done')->count();
        $pendingTemuan = Temuan::whereNull('Desc_Update_Temuan')->count();
        $totalTemuan = $totalPerbaikan + $pendingTemuan;

        $persentasePerbaikan = $totalTemuan > 0
            ? round(($totalPerbaikan / $totalTemuan) * 100)
            : 0;

        // Data Patrol
        $patrols = Patrol::orderBy('Time_Patrol', 'desc')->get();
        
        return view('admins.dashboards.index', compact(
            'totalPatrol',
            'totalPerbaikan',
            'pendingTemuan',
            'persentasePerbaikan',
            'patrols'
        ));
    }
}
