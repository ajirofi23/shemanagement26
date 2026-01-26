<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ManagementAksesController extends Controller
{
    public function index(Request $request)
    {
        $users = [];
        $selectedUser = null;

        // Jika BELUM memilih user, tampilkan daftar user
        if (!$request->filled('user_id')) {
            $users = DB::table('tb_user')
                ->when($request->search_user, function ($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->search_user . '%')
                        ->orWhere('usr', 'like', '%' . $request->search_user . '%');
                })
                ->orderBy('nama')
                ->get();
        }

        // Jika SUDAH memilih user, ambil data user & permission-nya
        $permissions = collect();
        if ($request->filled('user_id')) {
            $selectedUser = DB::table('tb_user')->where('id', $request->user_id)->first();

            if (!$selectedUser) {
                return redirect('/it/management-akses')->with('error', 'User tidak ditemukan');
            }

            $permissions = DB::table('tb_user_permissions')
                ->where('user_id', $request->user_id)
                ->get()
                ->keyBy('menu_id');
        }

        // ===== MENU LIST (Selalu diload untuk mapping permission) =====
        $menus = DB::table('tb_menus')
            ->orderBy('apps_group')
            ->orderBy('urutan_menu')
            ->get();

        return view('IT.management-akses', compact(
            'users',
            'menus',
            'permissions',
            'selectedUser'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'permissions' => 'required|array'
        ]);

        DB::beginTransaction();
        try {

            foreach ($request->permissions as $menuId => $perm) {
                DB::table('tb_user_permissions')->updateOrInsert(
                    [
                        'user_id' => $request->user_id,
                        'menu_id' => $menuId
                    ],
                    [
                        'can_access' => $perm['can_access'] ?? 0,
                        'can_read' => $perm['can_read'] ?? 0,
                        'can_add' => $perm['can_add'] ?? 0,
                        'can_edit' => $perm['can_edit'] ?? 0,
                        'can_delete' => $perm['can_delete'] ?? 0,
                        'can_approve1' => $perm['can_approve1'] ?? 0,
                        'can_approve2' => $perm['can_approve2'] ?? 0,
                        'can_approve3' => $perm['can_approve3'] ?? 0,
                        'can_approve4' => $perm['can_approve4'] ?? 0
                    ]
                );
            }

            DB::commit();
            return redirect('/it/management-akses?user_id=' . $request->user_id)
                ->with('success', 'Hak akses berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
