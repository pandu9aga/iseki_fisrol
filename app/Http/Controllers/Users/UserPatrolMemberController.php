<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\PatrolMember;
use App\Models\Patrol;
use App\Models\User;
use App\Models\Member;

class UserPatrolMemberController extends Controller
{
    // Tampilkan daftar Patrol Member untuk patrol tertentu
    public function index($id)
    {
        if (!Session::has('login_id')) {
            return redirect()->route('login')->withErrors(['unauthorized' => 'Silakan login terlebih dahulu.']);
        }

        $patrol_members = PatrolMember::with(['patrol', 'user', 'member'])
                                      ->where('Id_Patrol', $id)
                                      ->get();

        $patrol = Patrol::findOrFail($id); // cukup 1 patrol
        $users = User::where('Id_Type_User', 2)->get(); // tipe user biasa
        $members = Member::all();

        return view('users.patrol_members.index', compact('patrol_members', 'patrol', 'users', 'members'));
    }

    

}
