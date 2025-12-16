<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Member;

class MemberController extends Controller
{
    public function index()
    {
        // Cek apakah user sudah login
        if (!Session::has('login_id')) {
            return redirect()->route('login')->withErrors([
                'unauthorized' => 'Silakan login terlebih dahulu.'
            ]);
        }

        // Ambil user yang sedang login
        $Id_User = Session::get('login_id');
        $user = User::find($Id_User);

        // Set nama halaman (untuk konsistensi tampilan, misal active menu)
        $page = "member";

        // Ambil semua member dengan relasi division
        $members = Member::with('division')->get();

        // Kirim data ke view
        return view('admins.members.index', compact('page', 'user', 'members'));
    }
}
