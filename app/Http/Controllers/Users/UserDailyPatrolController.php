<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\DailyPatrol;
use App\Models\Member;

class UserDailyPatrolController extends Controller
{
    public function index()
    {
        if (!Session::has('login_id')) {
            return redirect()->route('login')->withErrors(['unauthorized' => 'Silakan login terlebih dahulu.']);
        }

        $daily_patrols = DailyPatrol::orderBy('Time_Daily_Patrol', 'desc')->get();
        $member = Member::where('NIK', Session::get('login_nik'))->first();

        return view('users.daily_patrols.index', compact('daily_patrols', 'member'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'Name_Daily_Patrol' => 'required|string|max:255',
            'Time_Daily_Patrol' => 'required|date',
        ]);

        DailyPatrol::create($request->only(['Name_Daily_Patrol', 'Time_Daily_Patrol']));

        return redirect()->route('user_daily_patrol')->with('success', 'Data daily patrol berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Name_Daily_Patrol' => 'required|string|max:255',
            'Time_Daily_Patrol' => 'required|date',
        ]);

        $patrol = DailyPatrol::findOrFail($id);
        $patrol->update($request->only(['Name_Daily_Patrol', 'Time_Daily_Patrol']));

        return redirect()->route('user_daily_patrol')->with('success', 'Data daily patrol berhasil diperbarui');
    }

    public function destroy($id)
    {
        $patrol = DailyPatrol::findOrFail($id);
        $patrol->delete();

        return redirect()->route('user_daily_patrol')->with('success', 'Data daily patrol berhasil dihapus');
    }
}
