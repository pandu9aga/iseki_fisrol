<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use App\Models\DailyPatrolMember;
use App\Models\DailyPatrol;
use App\Models\User;
use App\Models\Member;

class DailyPatrolMemberController extends Controller
{
    public function index($id)
    {
        if (!Session::has('login_id')) {
            return redirect()->route('login')->withErrors(['unauthorized' => 'Silakan login terlebih dahulu.']);
        }

        $patrol_members = DailyPatrolMember::with(['dailyPatrol', 'user', 'member'])
            ->where('Id_Daily_Patrol', $id)
            ->get();

        $daily_patrols = DailyPatrol::where('Id_Daily_Patrol', $id)->get();
        $members = Member::all();

        $usedUserIds = $patrol_members->pluck('Id_User')->toArray();
        $users = User::where('Id_Type_User', 2)->get();

        return view('admins.daily_patrol_members.index', compact(
            'patrol_members',
            'daily_patrols',
            'users',
            'members',
            'usedUserIds'
        ));
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'Id_Daily_Patrol' => 'required|exists:daily_patrols,Id_Daily_Patrol',
            'Id_User'   => [
                'required',
                'exists:users,Id_User',
                Rule::unique('daily_patrol_members', 'Id_User')
                    ->where(fn($q) => $q->where('Id_Daily_Patrol', $request->Id_Daily_Patrol)),
            ],
            'Id_Member' => 'required|exists:members,Id_Member',
        ], [
            'Id_User.unique' => 'User ini sudah terdaftar di daily patrol tersebut.',
        ]);

        DailyPatrolMember::create($validated);

        return redirect()
            ->route('daily_patrol_member.index', ['id' => $request->Id_Daily_Patrol])
            ->with('success', 'Data Daily Patrol Member berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'Id_Daily_Patrol' => 'required|exists:daily_patrols,Id_Daily_Patrol',
            'Id_User'   => [
                'required',
                'exists:users,Id_User',
                Rule::unique('daily_patrol_members', 'Id_User')
                    ->where(fn($q) => $q->where('Id_Daily_Patrol', $request->Id_Daily_Patrol))
                    ->ignore($id, 'Id_Daily_Patrol_Member'),
            ],
            'Id_Member' => 'required|exists:members,Id_Member',
        ], [
            'Id_User.unique' => 'User ini sudah terdaftar di daily patrol tersebut.',
        ]);

        $patrolMember = DailyPatrolMember::findOrFail($id);
        $patrolMember->update($validated);

        return redirect()
            ->route('daily_patrol_member.index', ['id' => $patrolMember->Id_Daily_Patrol])
            ->with('success', 'Data Daily Patrol Member berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $patrolMember = DailyPatrolMember::findOrFail($id);
        $idPatrol = $patrolMember->Id_Daily_Patrol;
        $patrolMember->delete();

        return redirect()->route('daily_patrol_member.index', ['id' => $idPatrol])
            ->with('success', 'Data Daily Patrol Member berhasil dihapus.');
    }
}
