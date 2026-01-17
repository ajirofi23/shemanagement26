<?php

namespace App\Http\Controllers\SHE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MasterKtaController extends Controller
{
    /**
     * Menampilkan daftar Kondisi Tidak Aman (KTA).
     * URL Permission: /she/master/kta
     * Menggunakan variabel $ktas
     */
    public function index(Request $request)
    {
        // Target tabel tb_master_kta
        $query = DB::table('tb_master_kta');

        // Search berdasarkan kolom nama_kta
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama_kta', 'like', "%$search%");
        }

        // Ambil semua data KTA
        $ktas = $query->orderBy('nama_kta')->get(); 

        // Ambil permission user login untuk menu /she/master/kta
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            ->where('m.url', '/she/master/kta') // URL menu yang baru
            ->select('up.can_edit', 'up.can_delete')
            ->first();

        // Mengembalikan view SHE.masterkta
        return view('SHE.masterkta', compact('ktas', 'permission'));
    }

    /**
     * Menyimpan data KTA baru.
     */
    public function store(Request $request)
    {
        // 1. Cek izin
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            ->where('m.url', '/she/master/kta')
            ->select('up.can_edit')
            ->first();

        if (!($permission && $permission->can_edit)) {
            return redirect('/she/master/kta')->with('error', 'Anda tidak punya izin menambah data KTA.');
        }
        
        // 2. Validasi input: hanya nama_kta
        $request->validate([
            'nama_kta' => 'required|string|max:255|unique:tb_master_kta,nama_kta',
        ]);

        // 3. Simpan data ke tb_master_kta
        DB::table('tb_master_kta')->insert([
            'nama_kta' => $request->nama_kta,
            
        ]);

        return redirect('/she/master/kta')->with('success', 'Data Kondisi Tidak Aman berhasil ditambahkan.');
    }

    /**
     * Mengupdate data KTA berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        // 1. Cek izin
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            ->where('m.url', '/she/master/kta')
            ->select('up.can_edit')
            ->first();

        if (!($permission && $permission->can_edit)) {
            return redirect('/she/master/kta')->with('error', 'Anda tidak punya izin mengedit data KTA.');
        }

        // 2. Validasi input: pastikan unique kecuali dirinya sendiri
        $request->validate([
            'nama_kta' => 'required|string|max:255|unique:tb_master_kta,nama_kta,'.$id,
        ]);

        // 3. Update data di tb_master_kta
        DB::table('tb_master_kta')->where('id', $id)->update([
            'nama_kta' => $request->nama_kta,
           
            
        ]);

        return redirect('/she/master/kta')->with('success', 'Data Kondisi Tidak Aman berhasil diupdate.');
    }

    /**
     * Menghapus data KTA berdasarkan ID.
     */
    public function destroy($id)
    {
        // 1. Cek izin
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            ->where('m.url', '/she/master/kta')
            ->select('up.can_delete')
            ->first();

        if ($permission && $permission->can_delete) {
            // 2. Hapus data dari tb_master_kta
            DB::table('tb_master_kta')->where('id', $id)->delete();
            return redirect('/she/master/kta')->with('success', 'Data Kondisi Tidak Aman berhasil dihapus.');
        } else {
            return redirect('/she/master/kta')->with('error', 'Anda tidak punya izin menghapus data KTA.');
        }
    }

    // Tambahkan fungsi 'edit' jika rute /edit/{id} diaktifkan, meskipun fungsinya mungkin tidak digunakan di frontend.
    public function edit($id)
    {
        // Logika edit jika diperlukan, biasanya hanya mengembalikan data dalam format JSON
        $kta = DB::table('tb_master_kta')->where('id', $id)->first();
        if (!$kta) {
            return response()->json(['error' => 'Data tidak ditemukan.'], 404);
        }
        return response()->json($kta);
    }
}