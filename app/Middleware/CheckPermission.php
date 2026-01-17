<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $routeName = null)
    {
        // Jika belum login, redirect langsung
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userId = Auth::user()->id;

        // Ambil URL yang sedang diakses
        $currentPath = '/' . ltrim($request->path(), '/');  // contoh: /it/dashboard

        // Ambil menu yang URL-nya sama
        $menu = DB::table('tb_menus')->where('url', $currentPath)->first();

        if (!$menu) {
            return abort(403, 'Menu tidak terdaftar dalam sistem.');
        }

        // Cek apakah user punya izin akses
        $permission = DB::table('tb_user_permissions')
            ->where('user_id', $userId)
            ->where('menu_id', $menu->id)
            ->where('can_access', 1)
            ->first();

        if (!$permission) {
            return abort(403, 'Anda tidak memiliki izin untuk mengakses menu ini.');
        }

        return $next($request);
    }
}
