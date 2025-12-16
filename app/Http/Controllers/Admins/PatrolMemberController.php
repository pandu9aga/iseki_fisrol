<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use App\Models\PatrolMember;
use App\Models\Patrol;
use App\Models\User;
use App\Models\Member;

class PatrolMemberController extends Controller
{
    public function index($id)
    {
        if (!Session::has('login_id')) {
            return redirect()->route('login')->withErrors(['unauthorized' => 'Silakan login terlebih dahulu.']);
        }

        $patrol_members = PatrolMember::with(['patrol', 'user', 'member'])
            ->where('Id_Patrol', $id)
            ->get();

        $patrols = Patrol::where('Id_Patrol', $id)->get();
        $members = Member::all();

        // KUMPULKAN user yang sudah dipakai di patrol ini
        $usedUserIds = $patrol_members->pluck('Id_User')->toArray();

        // KIRIM SEMUA user tipe 2 (untuk Edit; filter "Tambah" dilakukan di Blade)
        $users = User::where('Id_Type_User', 2)->get();

        return view('admins.patrol_members.index', compact(
            'patrol_members',
            'patrols',
            'users',
            'members',
            'usedUserIds'
        ));
    }


    public function create(Request $request)
    {
        $validated = $request->validate([
            'Id_Patrol' => 'required|exists:patrols,Id_Patrol',
            'Id_User'   => [
                'required',
                'exists:users,Id_User',
                // Unik per (Id_Patrol, Id_User)
                Rule::unique('patrol_members', 'Id_User')
                    ->where(fn($q) => $q->where('Id_Patrol', $request->Id_Patrol)),
            ],
            'Id_Member' => 'required|exists:members,Id_Member',
        ], [
            'Id_User.unique' => 'User ini sudah terdaftar di patrol tersebut.',
        ]);

        PatrolMember::create($validated);

        return redirect()
            ->route('patrol_member.index', ['id' => $request->Id_Patrol])
            ->with('success', 'Data Patrol Member berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'Id_Patrol' => 'required|exists:patrols,Id_Patrol',
            'Id_User'   => [
                'required',
                'exists:users,Id_User',
                Rule::unique('patrol_members', 'Id_User')
                    ->where(fn($q) => $q->where('Id_Patrol', $request->Id_Patrol))
                    ->ignore($id, 'Id_Patrol_Member'), // abaikan baris yang sedang diedit
            ],
            'Id_Member' => 'required|exists:members,Id_Member',
        ], [
            'Id_User.unique' => 'User ini sudah terdaftar di patrol tersebut.',
        ]);

        $patrolMember = PatrolMember::findOrFail($id);
        $patrolMember->update($validated);

        return redirect()
            ->route('patrol_member.index', ['id' => $patrolMember->Id_Patrol])
            ->with('success', 'Data Patrol Member berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $patrolMember = PatrolMember::findOrFail($id);
        $idPatrol = $patrolMember->Id_Patrol;
        $patrolMember->delete();

        return redirect()->route('patrol_member.index', ['id' => $idPatrol])
            ->with('success', 'Data Patrol Member berhasil dihapus.');
    }
}
