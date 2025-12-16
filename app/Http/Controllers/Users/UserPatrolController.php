<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Patrol;
use App\Models\Member;

class UserPatrolController extends Controller
{
    /**
     * Menampilkan daftar semua data patrol
     * yang bisa diakses oleh semua member yang login.
     */
    public function index()
    {
        // Cek apakah user sudah login
        if (!Session::has('login_id')) {
            return redirect()->route('login')->withErrors(['unauthorized' => 'Silakan login terlebih dahulu.']);
        }

        // Ambil semua data patrol tanpa relasi ke patrolMembers
        $patrols = Patrol::orderBy('Time_Patrol', 'desc')->get();

        // Ambil data member yang sedang login (jika ingin ditampilkan di header)
        $member = Member::where('NIK', Session::get('login_nik'))->first();

        // Kirim data ke view
        return view('users.patrols.index', compact('patrols', 'member'));
    }

    /**
     * Tambah data patrol baru (opsional jika user boleh menambah)
     */
    public function create(Request $request)
    {
        $request->validate([
            'Name_Patrol' => 'required|string|max:255',
            'Time_Patrol' => 'required|date',
        ]);

        Patrol::create($request->only(['Name_Patrol', 'Time_Patrol']));

        return redirect()->route('user_patrol')->with('success', 'Data patrol berhasil ditambahkan');
    }

    /**
     * Update data patrol (opsional jika user boleh edit)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'Name_Patrol' => 'required|string|max:255',
            'Time_Patrol' => 'required|date',
        ]);

        $patrol = Patrol::findOrFail($id);
        $patrol->update($request->only(['Name_Patrol', 'Time_Patrol']));

        return redirect()->route('user_patrol')->with('success', 'Data patrol berhasil diperbarui');
    }

    /**
     * Hapus data patrol (opsional jika user boleh hapus)
     */
    public function destroy($id)
    {
        $patrol = Patrol::findOrFail($id);
        $patrol->delete();

        return redirect()->route('user_patrol')->with('success', 'Data patrol berhasil dihapus');
    }
}
