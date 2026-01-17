<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ManagementMenuController extends Controller
{
    /**
     * Menampilkan daftar menu dengan fitur pencarian
     */
    public function index(Request $request)
    {
        $query = DB::table('tb_menus');

        // Fitur Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('menu_name', 'like', "%$search%")
                    ->orWhere('url', 'like', "%$search%")
                    ->orWhere('apps_group', 'like', "%$search%")
                    ->orWhere('icon', 'like', "%$search%");
            });
        }

        // Urutkan berdasarkan group aplikasi dan urutan menu
        $menus = $query->orderBy('apps_group', 'asc')
            ->orderBy('urutan_menu', 'asc')
            ->get();

        // Ambil permission user login untuk akses fitur di halaman ini
        // Mengarahkan ke URL /it/management-menu
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            ->where('m.url', '/it/management-menu')
            ->select('up.can_edit', 'up.can_delete')
            ->first();

        return view('it.management-menu', compact('menus', 'permission'));
    }

    /**
     * Menyimpan data menu baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'urutan_menu' => 'required|integer',
            'menu_name' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'parent_id' => 'nullable|integer',
            'icon' => 'nullable|string|max:100',
            'has_extra_permissions' => 'required|boolean',
            'apps_group' => 'required|string|max:50',
        ]);

        DB::table('tb_menus')->insert([
            'urutan_menu' => $request->urutan_menu,
            'menu_name' => $request->menu_name,
            'url' => $request->url,
            'parent_id' => $request->parent_id,
            'icon' => $request->icon,
            'has_extra_permissions' => $request->has_extra_permissions,
            'apps_group' => $request->apps_group,
        ]);

        return redirect('/it/management-menu')->with('success', 'Menu baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data menu
     */
    public function update(Request $request, $id)
    {
        // Proteksi Permission Edit
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            ->where('m.url', '/it/management-menu')
            ->select('up.can_edit')
            ->first();

        if (!($permission && $permission->can_edit)) {
            return redirect('/it/management-menu')->with('error', 'Anda tidak punya izin untuk mengedit menu.');
        }

        $request->validate([
            'urutan_menu' => 'required|integer',
            'menu_name' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'parent_id' => 'nullable|integer',
            'icon' => 'nullable|string|max:100',
            'has_extra_permissions' => 'required|boolean',
            'apps_group' => 'required|string|max:50',
        ]);

        DB::table('tb_menus')->where('id', $id)->update([
            'urutan_menu' => $request->urutan_menu,
            'menu_name' => $request->menu_name,
            'url' => $request->url,
            'parent_id' => $request->parent_id,
            'icon' => $request->icon,
            'has_extra_permissions' => $request->has_extra_permissions,
            'apps_group' => $request->apps_group,
            'updated_at' => now(),
        ]);

        return redirect('/it/management-menu')->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Menghapus menu
     */
    public function destroy($id)
    {
        // Proteksi Permission Delete
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            ->where('m.url', '/it/management-menu')
            ->select('up.can_delete')
            ->first();

        if ($permission && $permission->can_delete) {
            DB::table('tb_menus')->where('id', $id)->delete();
            return redirect('/it/management-menu')->with('success', 'Menu berhasil dihapus.');
        } else {
            return redirect('/it/management-menu')->with('error', 'Anda tidak punya izin untuk menghapus menu.');
        }
    }
}