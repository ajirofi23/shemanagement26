<?php

namespace App\Http\Controllers\PIC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\HyariHatto;
use App\Models\Pta;
use App\Models\Kta;
use App\Models\Pb;
use App\Exports\HyariHattoExport;
use Maatwebsite\Excel\Facades\Excel;

class PicHyariHattoController extends Controller
{
    /**
     * =====================================================
     * INDEX
     * PIC HANYA MELIHAT DATA SESUAI SECTION LOGIN
     * =====================================================
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $hyarihattos = HyariHatto::with(['ptas', 'ktas', 'pbs'])
            // 🔒 FILTER UTAMA (WAJIB)
            ->where('section_id', $user->section_id)

            // 🔍 SEARCH (TIDAK BOLEH MENEMBUS SECTION)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('deskripsi', 'like', '%' . $search . '%')
                        ->orWhereHas('ptas', function ($q2) use ($search) {
                            $q2->where('nama_pta', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('ktas', function ($q3) use ($search) {
                            $q3->where('nama_kta', 'like', '%' . $search . '%');
                        });
                });
            })

            ->orderBy('created_at', 'desc')
            ->get();

        // Master data
        $masterPtas = Pta::all();
        $masterKtas = Kta::all();
        $masterPbs = Pb::all();

        $permission = (object) [
            'can_edit' => true,
            'can_delete' => true
        ];

        return view('PIC.laporanhyarihatto', compact(
            'hyarihattos',
            'masterPtas',
            'masterKtas',
            'masterPbs',
            'permission'
        ));
    }

    /**
     * =====================================================
     * STORE
     * =====================================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'pta_id' => 'required|array',
            'kta_id' => 'required|array',
            'pb_id' => 'required|array',
            'lokasi' => 'required|string',
            'deskripsi' => 'required|string|max:1000',
            'usulan' => 'required|string|max:1000',
            'bukti' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();

        $path_bukti = null;
        if ($request->hasFile('bukti')) {
            $path_bukti = $request->file('bukti')->store('hyarihatto_bukti', 'public');
        }

        $laporan = HyariHatto::create([
            'deskripsi' => $request->deskripsi,
            'usulan' => $request->usulan,
            'lokasi' => $request->lokasi,
            'bukti' => $path_bukti,
            'rekomendasi' => null,
            'section_id' => $user->section_id, // 🔒 AMBIL DARI SESSION
            'pelapor' => $user->id,
        ]);

        $laporan->ptas()->sync($request->pta_id);
        $laporan->ktas()->sync($request->kta_id);
        $laporan->pbs()->sync($request->pb_id);

        return redirect('/pic/laporanhyarihatto')
            ->with('success', 'Laporan Hyari Hatto berhasil dibuat.');
    }

    /**
     * =====================================================
     * UPDATE
     * =====================================================
     */
    public function update(Request $request, $id)
    {
        $laporan = HyariHatto::where('id', $id)
            ->where('section_id', Auth::user()->section_id) // 🔒 PROTEKSI UPDATE
            ->firstOrFail();

        $request->validate([
            'pta_id' => 'required|array',
            'kta_id' => 'required|array',
            'pb_id' => 'required|array',
            'deskripsi' => 'required|string|max:1000',
            'usulan' => 'required|string|max:1000',
            'rekomendasi' => 'nullable|string|max:1000',
            'bukti' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path_bukti = $laporan->bukti;
        if ($request->hasFile('bukti')) {
            if ($laporan->bukti) {
                Storage::disk('public')->delete($laporan->bukti);
            }
            $path_bukti = $request->file('bukti')->store('hyarihatto_bukti', 'public');
        }

        $laporan->update([
            'deskripsi' => $request->deskripsi,
            'usulan' => $request->usulan,
            'rekomendasi' => $request->rekomendasi,
            'bukti' => $path_bukti,
            // ❌ section_id TIDAK BOLEH DIUBAH
        ]);

        $laporan->ptas()->sync($request->pta_id);
        $laporan->ktas()->sync($request->kta_id);
        $laporan->pbs()->sync($request->pb_id);

        return redirect('/pic/laporanhyarihatto')
            ->with('success', 'Laporan Hyari Hatto berhasil diperbarui.');
    }

    /**
     * =====================================================
     * DESTROY
     * =====================================================
     */
    public function destroy($id)
    {
        $laporan = HyariHatto::where('id', $id)
            ->where('section_id', Auth::user()->section_id) // 🔒 PROTEKSI DELETE
            ->firstOrFail();

        if ($laporan->bukti) {
            Storage::disk('public')->delete($laporan->bukti);
        }

        $laporan->delete();

        return redirect('/pic/laporanhyarihatto')
            ->with('success', 'Laporan Hyari Hatto berhasil dihapus.');
    }

    /**
     * =====================================================
     * EXPORT EXCEL
     * =====================================================
     */
    public function exportExcel(Request $request)
    {
        $date = now()->format('Ymd_His');
        return Excel::download(
            new HyariHattoExport($request->search),
            "laporan_hyari_hatto_{$date}.xlsx"
        );
    }
    /**
     * =====================================================
     * DOWNLOAD PDF
     * =====================================================
     */
    public function downloadPdf($id)
    {
        $data = HyariHatto::with(['ptas', 'ktas', 'pbs', 'user', 'section'])
            ->where('id', $id)
            ->where('section_id', Auth::user()->section_id) // 🔒 PROTEKSI
            ->firstOrFail();

        $masterPtas = Pta::all();
        $masterKtas = Kta::all();
        $masterPbs = Pb::all();

        // Menggunakan view yang sama dengan SHE karena formatnya standar
        $html = view('SHE.pdf_hyari_hatto', compact(
            'data',
            'masterPtas',
            'masterKtas',
            'masterPbs'
        ))->render();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        $mpdf->WriteHTML($html);
        return $mpdf->Output("Hiyari_Hatto_{$data->id}.pdf", 'I');
    }
}
