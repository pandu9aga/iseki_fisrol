<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Patrol;
use App\Models\Member;

class PatrolController extends Controller
{
    public function index()
    {
        if (!Session::has('login_id')) {
            return redirect()->route('login')->withErrors([
                'unauthorized' => 'Silakan login terlebih dahulu.'
            ]);
        }

        $patrols = Patrol::orderBy('Time_Patrol', 'desc')->get();
        $members = Member::all();

        return view('admins.patrols.index', compact('patrols', 'members'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'Name_Patrol' => 'required|string|max:255',
            'Time_Patrol' => 'required|date',
        ]);

        Patrol::create([
            'Name_Patrol' => $request->Name_Patrol,
            'Time_Patrol' => date('Y-m-d', strtotime($request->Time_Patrol)), // hanya tanggal
        ]);

        return redirect()->route('patrol')->with('success', 'Data patrol berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Name_Patrol' => 'required|string|max:255',
            'Time_Patrol' => 'required|date',
        ]);

        $patrol = Patrol::findOrFail($id);
        $patrol->update([
            'Name_Patrol' => $request->Name_Patrol,
            'Time_Patrol' => date('Y-m-d', strtotime($request->Time_Patrol)), // hanya tanggal
        ]);

        return redirect()->route('patrol')->with('success', 'Data patrol berhasil diperbarui');
    }

    public function destroy($id)
    {
        $patrol = Patrol::findOrFail($id);
        $patrol->delete();

        return redirect()->route('patrol')->with('success', 'Data patrol berhasil dihapus');
    }
}
