<?php

namespace App\Http\Controllers\SHE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MasterPtaController extends Controller
{
    /**
     * Menampilkan daftar Perilaku Tidak Aman (PTA) dan mengurus pencarian.
     * URL Permission: /she/master/pta
     */
    public function index(Request $request)
    {
        // Akses ke tabel tb_master_pta
        $query = DB::table('tb_master_pta');

        // Search hanya berdasarkan kolom nama_pta
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama_pta', 'like', "%$search%");
        }

        // Variabel diubah namanya menjadi $ptas
        $ptas = $query->orderBy('nama_pta')->get(); // Ambil semua data PTA

        // Ambil permission user login untuk menu /she/master/pta
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            ->where('m.url', '/she/master/pta') // Sesuaikan URL menu
            ->select('up.can_edit', 'up.can_delete')
            ->first();

        // Variabel $ptas sekarang dikirimkan ke view
        return view('SHE.masterpta', compact('ptas', 'permission'));
    }

    /**
     * Menyimpan data PTA baru.
     * URL Permission: /she/master/pta (asumsi store menggunakan permission can_edit/can_add)
     */
    public function store(Request $request)
    {
        // 1. Cek izin untuk menambahkan (asumsi menggunakan can_edit sebagai indikator bisa menambah/mengubah)
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            ->where('m.url', '/she/master/pta')
            ->select('up.can_edit')
            ->first();

        if (!($permission && $permission->can_edit)) {
            return redirect('/she/master/pta')->with('error', 'Anda tidak punya izin menambah data PTA.');
        }
        
        // 2. Validasi input: hanya nama_pta
        $request->validate([
            'nama_pta' => 'required|string|max:255|unique:tb_master_pta,nama_pta',
        ]);

        // 3. Simpan data ke tb_master_pta
        DB::table('tb_master_pta')->insert([
            'nama_pta' => $request->nama_pta,
           
        ]);

        return redirect('/she/master/pta')->with('success', 'Data Perilaku Tidak Aman berhasil ditambahkan.');
    }

    /**
     * Mengupdate data PTA berdasarkan ID.
     * URL Permission: /she/master/pta
     */
    public function update(Request $request, $id)
    {
        // 1. Cek izin untuk mengedit
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            ->where('m.url', '/she/master/pta')
            ->select('up.can_edit')
            ->first();

        if (!($permission && $permission->can_edit)) {
            return redirect('/she/master/pta')->with('error', 'Anda tidak punya izin mengedit data PTA.');
        }

        // 2. Validasi input: nama_pta dan pastikan unique kecuali dirinya sendiri
        $request->validate([
            'nama_pta' => 'required|string|max:255|unique:tb_master_pta,nama_pta,'.$id,
        ]);

        // 3. Update data di tb_master_pta
        DB::table('tb_master_pta')->where('id', $id)->update([
            'nama_pta' => $request->nama_pta,
            
        ]);

        return redirect('/she/master/pta')->with('success', 'Data Perilaku Tidak Aman berhasil diupdate.');
    }

    /**
     * Menghapus data PTA berdasarkan ID.
     * URL Permission: /she/master/pta
     */
    public function destroy($id)
    {
        // 1. Cek izin untuk menghapus
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            ->where('m.url', '/she/master/pta')
            ->select('up.can_delete')
            ->first();

        if ($permission && $permission->can_delete) {
            // 2. Hapus data dari tb_master_pta
            DB::table('tb_master_pta')->where('id', $id)->delete();
            return redirect('/she/master/pta')->with('success', 'Data Perilaku Tidak Aman berhasil dihapus.');
        } else {
            return redirect('/she/master/pta')->with('error', 'Anda tidak punya izin menghapus data PTA.');
        }
    }
}