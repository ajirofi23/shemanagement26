<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login (MD5 – sesuai requirement)
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'usr'  => 'required',
            'pswd' => 'required',
        ]);

        // Cari user (MD5 password)
        $user = User::where('usr', $request->usr)
            ->where('pswd', md5($request->pswd))
            ->where('is_active', 1) // optional tapi disarankan
            ->first();

        if (!$user) {
            return back()->with('error', 'Username atau password salah');
        }

        // Login ke Laravel (SESSION STABIL)
        Auth::login($user);

        // ================= PERMISSIONS =================

        $permissions = DB::table('tb_user_permissions')
            ->where('user_id', $user->id)
            ->get();

        session(['user_permissions' => $permissions]);

        // ================= REDIRECT MENU =================

        $firstMenu = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'm.id', '=', 'up.menu_id')
            ->where('up.user_id', $user->id)
            ->where('up.can_access', 1)
            ->orderBy('m.urutan_menu', 'ASC')
            ->select('m.url')
            ->first();

        if ($firstMenu) {
            return redirect($firstMenu->url);
        }

        // Kalau tidak punya menu
        Auth::logout();
        return redirect('/login')->with('error', 'Anda tidak punya akses menu');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
