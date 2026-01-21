<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Member;

class AuthController extends Controller
{
    public function index()
    {
        // Jika sudah login, arahkan ke dashboard sesuai tipe
        if (session()->has('login_type')) {
            if (session('login_type') == 1) {
                return redirect()->route('admins.dashboard.index');
            } elseif (session('login_type') == 2) {
                return redirect()->route('users.patrols.index');
            }
        }

        return view('auths.login');
    }

    // ======================
    // LOGIN ADMIN
    // ======================
    public function login_admin(Request $request)
    {
        $request->validate([
            'Username_User' => 'required',
            'Password_User' => 'required'
        ]);

        $user = User::where('Username_User', $request->Username_User)
            ->where('Password_User', $request->Password_User)
            ->first();

        if (!$user) {
            return back()->withErrors(['loginError' => 'Username atau password salah']);
        }

        session([
            'login_id' => $user->Id_User,
            'login_name' => $user->Name_User,
            'login_type' => $user->Id_Type_User, // 1 = admin
        ]);

        if ($user->Id_Type_User == 1) {
            return redirect()->route('admins.dashboard.index');
        } else {
            return redirect()->route('users.patrols.index');
        }
    }

    // ======================
    // LOGIN MEMBER (NIK SAJA)
    // ======================
    public function login_member(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'password' => 'required'
        ]);

        $member = Member::where('nik', $request->nik)->first();

        if (!$member || $member->password != $request->password) {
            return back()->withErrors(['loginError' => 'NIK atau password salah']);
        }

        session([
            'login_id'   => $member->id,
            'login_name' => $member->nama,
            'login_nik'  => $member->nik, // ✅ tambahkan ini
            'login_type' => 2, // tipe 2 = member
        ]);

        return redirect()->route('users.patrols.index');
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
}
