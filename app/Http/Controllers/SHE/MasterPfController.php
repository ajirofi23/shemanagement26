<?php

namespace App\Http\Controllers\SHE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

// DIUBAH: MasterPbController -> MasterPfController
class MasterPfController extends Controller
{
    // DIUBAH: URL yang digunakan untuk otorisasi dan redirect
    protected $menuUrl = '/she/master/pf';

    /**
     * Menampilkan daftar Pelanggaran Fisik (PF) dengan fungsi pencarian.
     * Menggunakan variabel $pfs
     */
    public function index(Request $request)
    {
        // DIUBAH: Target tabel tb_master_pb -> tb_master_pf
        $query = DB::table('tb_master_pf');

        // Search berdasarkan kolom nama_pf
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            // DIUBAH: Kolom diubah ke 'nama_pf'
            $query->where('nama_pf', 'like', "%$search%");
        }

        // Ambil semua data PF
        // DIUBAH: Variabel diubah dari $pbs menjadi $pfs
        $pfs = $query->orderBy('nama_pf')->get(); 

        // Ambil permission user login untuk menu /she/master/pf
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            // DIUBAH: URL menu disesuaikan /pb -> /pf
            ->where('m.url', $this->menuUrl) 
            ->select('up.can_edit', 'up.can_delete')
            ->first();

        // DIUBAH: Mengembalikan view yang sudah disesuaikan
        return view('she.masterpf', compact('pfs', 'permission'));
    }

    /**
     * Menyimpan data Pelanggaran Fisik baru.
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
            // DIUBAH: Pesan error
            return redirect($this->menuUrl)->with('error', 'Anda tidak punya izin menambah data Pelanggaran Fisik.');
        }
        
        // 2. Validasi input
        $validator = Validator::make($request->all(), [
            // DIUBAH: Kolom dan tabel disesuaikan ke nama_pf & tb_master_pf
            'nama_pf' => 'required|string|max:255|unique:tb_master_pf,nama_pf',
        ], [
            // DIUBAH: Pesan validasi
            'nama_pf.required' => 'Kolom Pelanggaran Fisik wajib diisi.',
            'nama_pf.unique'   => 'Pelanggaran Fisik ini sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 3. Simpan data ke tb_master_pf
        // DIUBAH: Tabel dan kolom disesuaikan
        DB::table('tb_master_pf')->insert([
            'nama_pf' => $request->nama_pf,
            // 'created_at' => now(), // Opsional: Tambahkan jika tabel memiliki timestamps
        ]);

        // DIUBAH: Pesan sukses
        return redirect($this->menuUrl)->with('success', 'Data Pelanggaran Fisik berhasil ditambahkan.');
    }

    /**
     * Mengupdate data Pelanggaran Fisik berdasarkan ID.
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
            // DIUBAH: Pesan error
            return redirect($this->menuUrl)->with('error', 'Anda tidak punya izin mengedit data Pelanggaran Fisik.');
        }

        // 2. Validasi input: pastikan unique kecuali dirinya sendiri
        $validator = Validator::make($request->all(), [
            // DIUBAH: Kolom dan tabel disesuaikan ke nama_pf & tb_master_pf
            'nama_pf' => 'required|string|max:255|unique:tb_master_pf,nama_pf,'.$id,
        ], [
            // DIUBAH: Pesan validasi
            'nama_pf.required' => 'Kolom Pelanggaran Fisik wajib diisi.',
            'nama_pf.unique'   => 'Pelanggaran Fisik ini sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 3. Update data di tb_master_pf
        // DIUBAH: Tabel dan kolom disesuaikan
        DB::table('tb_master_pf')->where('id', $id)->update([
            'nama_pf' => $request->nama_pf,
            // 'updated_at' => now(), // Opsional: Tambahkan jika tabel memiliki timestamps
        ]);

        // DIUBAH: Pesan sukses
        return redirect($this->menuUrl)->with('success', 'Data Pelanggaran Fisik berhasil diupdate.');
    }

    /**
     * Menghapus data Pelanggaran Fisik berdasarkan ID.
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
            // 2. Hapus data dari tb_master_pf
            // DIUBAH: Tabel disesuaikan
            DB::table('tb_master_pf')->where('id', $id)->delete();
            // DIUBAH: Pesan sukses
            return redirect($this->menuUrl)->with('success', 'Data Pelanggaran Fisik berhasil dihapus.');
        } else {
            // DIUBAH: Pesan error
            return redirect($this->menuUrl)->with('error', 'Anda tidak punya izin menghapus data Pelanggaran Fisik.');
        }
    }

    /**
     * Mengambil satu data PF (digunakan untuk keperluan AJAX/API, meskipun fungsi edit di Blade tidak menggunakannya).
     */
    public function edit($id)
    {
        // DIUBAH: Variabel dan tabel disesuaikan
        $pf = DB::table('tb_master_pf')->where('id', $id)->first();
        if (!$pf) {
            return response()->json(['error' => 'Data tidak ditemukan.'], 404);
        }
        // DIUBAH: Variabel disesuaikan
        return response()->json($pf);
    }
}