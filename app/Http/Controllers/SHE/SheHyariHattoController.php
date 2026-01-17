<?php

namespace App\Http\Controllers\SHE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\HyariHatto;
use App\Models\Pta; 
use App\Models\Kta; 
use App\Models\Pb; 
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use App\Exports\HyariHattoExport;
use Maatwebsite\Excel\Facades\Excel;

class SheHyariHattoController extends Controller
{
    public function index(Request $request)
    {

        $hyarihattos = HyariHatto::with(['ptas', 'ktas', 'pbs'])
            ->when($request->search, function ($query, $search) {
                $query->where('deskripsi', 'like', '%' . $search . '%')
                      ->orWhereHas('ptas', function ($q) use ($search) {
                          $q->where('nama_pta', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('ktas', function ($q) use ($search) {
                          $q->where('nama_kta', 'like', '%' . $search . '%');
                      });
            })
            
            ->get(); 

        $masterPtas = Pta::all();
        $masterKtas = Kta::all();
        $masterPbs  = Pb::all();
        
        $permission = (object)['can_edit' => true, 'can_delete' => true]; 

        return view('SHE.laporanhyarihatto', compact('hyarihattos', 'masterPtas', 'masterKtas', 'masterPbs', 'permission'));
    }

    
    public function update(Request $request, $id)
    {
        $laporan = HyariHatto::findOrFail($id);

        $request->validate([
            'rekomendasi' => 'nullable|string|max:1000',
        ]);

        $laporan->update([
            'rekomendasi' => $request->rekomendasi, 
        ]);

        $laporan->ptas()->sync($request->pta_id);
        $laporan->ktas()->sync($request->kta_id);
        $laporan->pbs()->sync($request->pb_id);

        return redirect()->to('/she/hyari-hatto')->with('success', 'Laporan Hyari Hatto berhasil diperbarui.');
    }
    
    public function downloadPdf($id)
    {
        $data           = HyariHatto::with(['ptas', 'ktas', 'pbs', 'user','section'])->findOrFail($id);
        $masterPtas     = Pta::all();
        $masterKtas     = Kta::all();
        $masterPbs      = Pb::all();
        $html = view('SHE.pdf_hyari_hatto', compact('data', 'masterPtas', 'masterKtas', 'masterPbs'))->render();

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

    public function exportExcel(Request $request)
    {
        $search = $request->get('search');
        $date = now()->format('Ymd_His');
        
        return Excel::download(new HyariHattoExport($search), "laporan_hyari_hatto_{$date}.xlsx");
    }

}