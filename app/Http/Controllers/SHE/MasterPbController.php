<?php

namespace App\Http\Controllers\SHE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MasterPbController extends Controller
{
    // URL yang digunakan untuk otorisasi dan redirect
    protected $menuUrl = '/she/master/pb';

    /**
     * Menampilkan daftar Potensi Bahaya (PB) dengan fungsi pencarian.
     * Menggunakan variabel $pbs
     */
    public function index(Request $request)
    {
        // Target tabel tb_master_pb
        $query = DB::table('tb_master_pb');

        // Search berdasarkan kolom nama_pb
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            // Kolom sudah diubah ke 'nama_pb'
            $query->where('nama_pb', 'like', "%$search%");
        }

        // Ambil semua data PB
        // Variabel diubah dari $ktas menjadi $pbs
        $pbs = $query->orderBy('nama_pb')->get(); 

        // Ambil permission user login untuk menu /she/master/pb
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            // URL menu disesuaikan
            ->where('m.url', $this->menuUrl) 
            ->select('up.can_edit', 'up.can_delete')
            ->first();

        // Mengembalikan view yang sudah disesuaikan
        return view('she.masterpb', compact('pbs', 'permission'));
    }

    /**
     * Menyimpan data Potensi Bahaya baru.
     */
    public function store(Request $request)
    {
        // 1. Cek izin
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            ->where('m.url', $this->menuUrl)
            ->select('up.can_edit')
            ->first();

        if (!($permission && $permission->can_edit)) {
            return redirect($this->menuUrl)->with('error', 'Anda tidak punya izin menambah data Potensi Bahaya.');
        }
        
        // 2. Validasi input
        $validator = Validator::make($request->all(), [
            // Kolom dan tabel disesuaikan ke nama_pb & tb_master_pb
            'nama_pb' => 'required|string|max:255|unique:tb_master_pb,nama_pb',
        ], [
            'nama_pb.required' => 'Kolom Potensi Bahaya wajib diisi.',
            'nama_pb.unique'   => 'Potensi Bahaya ini sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 3. Simpan data ke tb_master_pb
        DB::table('tb_master_pb')->insert([
            'nama_pb' => $request->nama_pb,
            // 'created_at' => now(), // Opsional: Tambahkan jika tabel memiliki timestamps
        ]);

        return redirect($this->menuUrl)->with('success', 'Data Potensi Bahaya berhasil ditambahkan.');
    }

    /**
     * Mengupdate data Potensi Bahaya berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        // 1. Cek izin
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            ->where('m.url', $this->menuUrl)
            ->select('up.can_edit')
            ->first();

        if (!($permission && $permission->can_edit)) {
            return redirect($this->menuUrl)->with('error', 'Anda tidak punya izin mengedit data Potensi Bahaya.');
        }

        // 2. Validasi input: pastikan unique kecuali dirinya sendiri
        $validator = Validator::make($request->all(), [
            // Kolom dan tabel disesuaikan ke nama_pb & tb_master_pb
            'nama_pb' => 'required|string|max:255|unique:tb_master_pb,nama_pb,'.$id,
        ], [
            'nama_pb.required' => 'Kolom Potensi Bahaya wajib diisi.',
            'nama_pb.unique'   => 'Potensi Bahaya ini sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 3. Update data di tb_master_pb
        DB::table('tb_master_pb')->where('id', $id)->update([
            'nama_pb' => $request->nama_pb,
            // 'updated_at' => now(), // Opsional: Tambahkan jika tabel memiliki timestamps
        ]);

        return redirect($this->menuUrl)->with('success', 'Data Potensi Bahaya berhasil diupdate.');
    }

    /**
     * Menghapus data Potensi Bahaya berdasarkan ID.
     */
    public function destroy($id)
    {
        // 1. Cek izin
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            ->where('m.url', $this->menuUrl)
            ->select('up.can_delete')
            ->first();

        if ($permission && $permission->can_delete) {
            // 2. Hapus data dari tb_master_pb
            DB::table('tb_master_pb')->where('id', $id)->delete();
            return redirect($this->menuUrl)->with('success', 'Data Potensi Bahaya berhasil dihapus.');
        } else {
            return redirect($this->menuUrl)->with('error', 'Anda tidak punya izin menghapus data Potensi Bahaya.');
        }
    }

    /**
     * Mengambil satu data PB (digunakan untuk keperluan AJAX/API, meskipun fungsi edit di Blade tidak menggunakannya).
     */
    public function edit($id)
    {
        // Variabel dan tabel disesuaikan
        $pb = DB::table('tb_master_pb')->where('id', $id)->first();
        if (!$pb) {
            return response()->json(['error' => 'Data tidak ditemukan.'], 404);
        }
        // Variabel disesuaikan
        return response()->json($pb);
    }
}