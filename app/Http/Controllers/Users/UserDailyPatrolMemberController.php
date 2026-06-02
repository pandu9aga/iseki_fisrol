<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\DailyPatrolMember;
use App\Models\DailyPatrol;
use App\Models\User;
use App\Models\Member;

class UserDailyPatrolMemberController extends Controller
{
    public function index($id)
    {
        if (!Session::has('login_id')) {
            return redirect()->route('login')->withErrors(['unauthorized' => 'Silakan login terlebih dahulu.']);
        }

        $patrol_members = DailyPatrolMember::with(['dailyPatrol', 'user', 'member'])
            ->where('Id_Daily_Patrol', $id)
            ->get();

        $daily_patrol = DailyPatrol::findOrFail($id);
        $users = User::where('Id_Type_User', 2)->get();
        $members = Member::all();

        return view('users.daily_patrol_members.index', compact('patrol_members', 'daily_patrol', 'users', 'members'));
    }
}
