<?php

namespace App\Http\Controllers\SHE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

// DIUBAH: MasterPfController -> MasterPdController
class MasterPdController extends Controller
{
    // DIUBAH: URL yang digunakan untuk otorisasi dan redirect /pf -> /pd
    protected $menuUrl = '/she/master/pd';

    /**
     * Menampilkan daftar Pelanggaran Dokumen (PD) dengan fungsi pencarian.
     * Menggunakan variabel $pds
     */
    public function index(Request $request)
    {
        // DIUBAH: Target tabel tb_master_pf -> tb_master_pd
        $query = DB::table('tb_master_pd');

        // Search berdasarkan kolom nama_pd
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            // DIUBAH: Kolom diubah ke 'nama_pd'
            $query->where('nama_pd', 'like', "%$search%");
        }

        // Ambil semua data PD
        // DIUBAH: Variabel diubah dari $pfs menjadi $pds
        $pds = $query->orderBy('nama_pd')->get();

        // Ambil permission user login untuk menu /she/master/pd
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            // DIUBAH: URL menu disesuaikan /pf -> /pd
            ->where('m.url', $this->menuUrl)
            ->select('up.can_edit', 'up.can_delete')
            ->first();

        // DIUBAH: Mengembalikan view yang sudah disesuaikan (masterpf -> masterpd)
        return view('SHE.masterpd', compact('pds', 'permission'));
    }

    /**
     * Menyimpan data Pelanggaran Dokumen baru.
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
            // DIUBAH: Pesan error (Pelanggaran Fisik -> Pelanggaran Dokumen)
            return redirect($this->menuUrl)->with('error', 'Anda tidak punya izin menambah data Pelanggaran Dokumen.');
        }

        // 2. Validasi input
        $validator = Validator::make($request->all(), [
            // DIUBAH: Kolom dan tabel disesuaikan ke nama_pd & tb_master_pd
            'nama_pd' => 'required|string|max:255|unique:tb_master_pd,nama_pd',
        ], [
            // DIUBAH: Pesan validasi
            'nama_pd.required' => 'Kolom Pelanggaran Dokumen wajib diisi.',
            'nama_pd.unique' => 'Pelanggaran Dokumen ini sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 3. Simpan data ke tb_master_pd
        // DIUBAH: Tabel dan kolom disesuaikan
        DB::table('tb_master_pd')->insert([
            'nama_pd' => $request->nama_pd,
            // 'created_at' => now(), // Opsional: Tambahkan jika tabel memiliki timestamps
        ]);

        // DIUBAH: Pesan sukses
        return redirect($this->menuUrl)->with('success', 'Data Pelanggaran Dokumen berhasil ditambahkan.');
    }

    /**
     * Mengupdate data Pelanggaran Dokumen berdasarkan ID.
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
            return redirect($this->menuUrl)->with('error', 'Anda tidak punya izin mengedit data Pelanggaran Dokumen.');
        }

        // 2. Validasi input: pastikan unique kecuali dirinya sendiri
        $validator = Validator::make($request->all(), [
            // DIUBAH: Kolom dan tabel disesuaikan ke nama_pd & tb_master_pd
            'nama_pd' => 'required|string|max:255|unique:tb_master_pd,nama_pd,' . $id,
        ], [
            // DIUBAH: Pesan validasi
            'nama_pd.required' => 'Kolom Pelanggaran Dokumen wajib diisi.',
            'nama_pd.unique' => 'Pelanggaran Dokumen ini sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 3. Update data di tb_master_pd
        // DIUBAH: Tabel dan kolom disesuaikan
        DB::table('tb_master_pd')->where('id', $id)->update([
            'nama_pd' => $request->nama_pd,
            // 'updated_at' => now(), // Opsional: Tambahkan jika tabel memiliki timestamps
        ]);

        // DIUBAH: Pesan sukses
        return redirect($this->menuUrl)->with('success', 'Data Pelanggaran Dokumen berhasil diupdate.');
    }

    /**
     * Menghapus data Pelanggaran Dokumen berdasarkan ID.
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
            // 2. Hapus data dari tb_master_pd
            // DIUBAH: Tabel disesuaikan
            DB::table('tb_master_pd')->where('id', $id)->delete();
            // DIUBAH: Pesan sukses
            return redirect($this->menuUrl)->with('success', 'Data Pelanggaran Dokumen berhasil dihapus.');
        } else {
            // DIUBAH: Pesan error
            return redirect($this->menuUrl)->with('error', 'Anda tidak punya izin menghapus data Pelanggaran Dokumen.');
        }
    }

    /**
     * Mengambil satu data PD (digunakan untuk keperluan AJAX/API, meskipun fungsi edit di Blade tidak menggunakannya).
     */
    public function edit($id)
    {
        // DIUBAH: Variabel dan tabel disesuaikan (pf -> pd)
        $pd = DB::table('tb_master_pd')->where('id', $id)->first();
        if (!$pd) {
            return response()->json(['error' => 'Data tidak ditemukan.'], 404);
        }
        // DIUBAH: Variabel disesuaikan
        return response()->json($pd);
    }
}