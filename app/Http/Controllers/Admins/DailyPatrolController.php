<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\DailyPatrol;
use App\Models\Member;

class DailyPatrolController extends Controller
{
    public function index()
    {
        if (!Session::has('login_id')) {
            return redirect()->route('login')->withErrors([
                'unauthorized' => 'Silakan login terlebih dahulu.'
            ]);
        }

        $daily_patrols = DailyPatrol::orderBy('Time_Daily_Patrol', 'desc')->get();
        $members = Member::all();

        return view('admins.daily_patrols.index', compact('daily_patrols', 'members'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'Name_Daily_Patrol' => 'required|string|max:255',
            'Time_Daily_Patrol' => 'required|date',
        ]);

        DailyPatrol::create([
            'Name_Daily_Patrol' => $request->Name_Daily_Patrol,
            'Time_Daily_Patrol' => date('Y-m-d', strtotime($request->Time_Daily_Patrol)),
        ]);

        return redirect()->route('daily_patrol')->with('success', 'Data daily patrol berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Name_Daily_Patrol' => 'required|string|max:255',
            'Time_Daily_Patrol' => 'required|date',
        ]);

        $patrol = DailyPatrol::findOrFail($id);
        $patrol->update([
            'Name_Daily_Patrol' => $request->Name_Daily_Patrol,
            'Time_Daily_Patrol' => date('Y-m-d', strtotime($request->Time_Daily_Patrol)),
        ]);

        return redirect()->route('daily_patrol')->with('success', 'Data daily patrol berhasil diperbarui');
    }

    public function destroy($id)
    {
        $patrol = DailyPatrol::findOrFail($id);
        $patrol->delete();

        return redirect()->route('daily_patrol')->with('success', 'Data daily patrol berhasil dihapus');
    }
}
