<?php

namespace App\Http\Controllers\SHE;

use App\Http\Controllers\Controller;
use App\Models\SafetyRiding;
use App\Models\PelanggaranDokumen;
use App\Models\PelanggaranFisik;
use App\Models\Section;
use App\Models\User;
use App\Models\BuktiSafetyRiding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SheSafetyRidingController extends Controller
{
      private function getPermission($feature)
    {
        return (object)['can_edit' => true, 'can_delete' => true];
    }

    public function index(Request $request)
    {
        $safetyridings = SafetyRiding::with(['pds', 'pfs', 'user.section']) 
            ->when($request->search, function ($query, $search) {
                $query->where('nopol', 'like', '%' . $search . '%')
                      ->orWhere('keterangan_pelanggaran', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('nama', 'like', '%' . $search . '%');
                      });
            })
            ->latest() 
            ->get(); 

        $masterPds = PelanggaranDokumen::all();
        $masterPfs = PelanggaranFisik::all();
        $sections = Section::all();
        $permission = $this->getPermission('safety_riding'); 
        $currentUser = Auth::user();

        return view('SHE.safetyriding', compact('safetyridings', 'masterPds', 'masterPfs', 'sections', 'permission', 'currentUser'));
    }

    public function getUsersBySection($section_id)
    {
        $users = User::where('section_id', $section_id)
                    ->select('id', 'nama')
                    ->get();
        
        return response()->json(['users' => $users]);
    }

    public function edit($id)
    {
        $laporan = SafetyRiding::with(['pds', 'pfs', 'user.section', 'buktiFiles'])->findOrFail($id);
        $masterPds = PelanggaranDokumen::all();
        $masterPfs = PelanggaranFisik::all();
        $sections = Section::all();
        $permission = $this->getPermission('safety_riding');
        
        return view('SHE.edit_safetyriding', compact('laporan', 'masterPds', 'masterPfs', 'sections', 'permission'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'waktu_kejadian' => 'required|date',
            'section_id' => 'required|exists:tb_section,id',
            'user_id' => 'required|exists:tb_user,id',
            'type_kendaraan' => 'required|string|max:100',
            'nopol' => 'required|string|max:15',
            'pd_id' => 'nullable|array', 
            'pf_id' => 'nullable|array', 
            'keterangan_pelanggaran' => 'required|string|max:1000', 
            'bukti.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $totalPelanggaran = (is_array($request->pd_id) ? count($request->pd_id) : 0) + 
                            (is_array($request->pf_id) ? count($request->pf_id) : 0);

            // Upload multiple gambar
            $buktiPaths = [];
            if ($request->hasFile('bukti')) {
                foreach ($request->file('bukti') as $key => $file) {
                    if ($file && $file->isValid()) {
                        $filename = time() . '_' . $key . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('safety_riding/bukti', $filename, 'public');
                        $buktiPaths[] = $path; // Simpan sebagai array
                    }
                }
            }

            // Simpan data - Laravel akan otomatis cast array ke JSON karena casting
            $laporan = SafetyRiding::create([
                'user_id' => $request->user_id,
                'section_id' => $request->section_id,
                'waktu_kejadian' => $request->waktu_kejadian,
                'type_kendaraan' => $request->type_kendaraan,
                'nopol' => $request->nopol,
                'keterangan_pelanggaran' => $request->keterangan_pelanggaran,
                'total_pelanggaran' => $totalPelanggaran,
                'bukti' => $buktiPaths, // Simpan sebagai array, Laravel akan convert ke JSON
                'status' => 'Open',
            ]);
            
            // Simpan ke tabel pivot
            if (!empty($request->pd_id) && is_array($request->pd_id)) {
                $laporan->pds()->attach($request->pd_id);
            }
            
            if (!empty($request->pf_id) && is_array($request->pf_id)) {
                $laporan->pfs()->attach($request->pf_id);
            }

            return redirect()->to('/she/safety-riding')->with('success', 'Laporan Safety Riding berhasil dibuat.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat laporan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $laporan = SafetyRiding::findOrFail($id);
        
        $request->validate([
            'waktu_kejadian'            => 'required|date',
            'user_id'                   => 'required|exists:tb_user,id',
            'section_id'                => 'required|exists:tb_section,id',
            'type_kendaraan'            => 'required|string|max:100',
            'nopol'                     => 'required|string|max:15',
            'pd_id'                     => 'nullable|array', 
            'pf_id'                     => 'nullable|array', 
            'keterangan_pelanggaran'    => 'required|string|max:1000', 
            'status'                    => 'required|in:Open,Close', 
            'bukti_baru.*'              => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        try {
            $totalPelanggaran = (is_array($request->pd_id) ? count($request->pd_id) : 0) + 
                                (is_array($request->pf_id) ? count($request->pf_id) : 0);

            // 1. Hapus semua gambar lama jika ada request untuk clear
            if ($request->has('clear_all_bukti') && $laporan->bukti) {
                $oldBukti = json_decode($laporan->bukti, true);
                foreach ($oldBukti as $path) {
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }

            // 2. Handle new uploads
            $newBuktiPaths = [];
            if ($request->hasFile('bukti_baru')) {
                foreach ($request->file('bukti_baru') as $key => $file) {
                    if ($file && $file->isValid()) {
                        $filename = time() . '_' . $key . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('safety_riding/bukti', $filename, 'public');
                        $newBuktiPaths[] = $path;
                    }
                }
            }

            // 3. Update data - hanya gunakan gambar baru
            $updateData = [
                'user_id'                   => $request->user_id,
                'section_id'                => $request->section_id,
                'waktu_kejadian'            => $request->waktu_kejadian,
                'type_kendaraan'            => $request->type_kendaraan,
                'nopol'                     => $request->nopol,
                'keterangan_pelanggaran'    => $request->keterangan_pelanggaran,
                'total_pelanggaran'         => $totalPelanggaran,
                'status'                    => $request->status,
            ];

            // Jika ada gambar baru, update bukti
            if (!empty($newBuktiPaths)) {
                $updateData['bukti'] = json_encode($newBuktiPaths);
            } elseif ($request->has('clear_all_bukti')) {
                // Jika clear all dan tidak ada upload baru, set ke null
                $updateData['bukti'] = null;
            }

            $laporan->update($updateData);

            // 4. Update many-to-many relations
            $laporan->pds()->sync($request->pd_id ?? []);
            $laporan->pfs()->sync($request->pf_id ?? []);

            return redirect()->to('/she/safety-riding')->with('success', 'Laporan Safety Riding berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui laporan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $safetyRiding = SafetyRiding::findOrFail($id);
            
            $safetyRiding->pds()->detach();
            $safetyRiding->pfs()->detach();
            
            $buktiFiles = $safetyRiding->buktiFiles;
            foreach ($buktiFiles as $bukti) {
                Storage::disk('public')->delete($bukti->path);
                $bukti->delete();
            }
            
            $safetyRiding->delete();

            return redirect()->route('safety-riding.index')->with('success', 'Laporan Safety Riding berhasil dihapus!');
            
        } catch (\Exception $e) {
            return redirect()->route('safety-riding.index')->with('error', 'Gagal menghapus laporan: ' . $e->getMessage());
        }
    }



    public function deleteImage($id, $imageIndex)
    {
        try {
            $laporan = SafetyRiding::findOrFail($id);
            
            // Decode JSON bukti
            $buktiArray = $laporan->bukti ? json_decode($laporan->bukti, true) : [];
            
            if (isset($buktiArray[$imageIndex])) {
                // Hapus file dari storage
                $pathToDelete = $buktiArray[$imageIndex];
                if (Storage::disk('public')->exists($pathToDelete)) {
                    Storage::disk('public')->delete($pathToDelete);
                }
                
                // Hapus dari array
                array_splice($buktiArray, $imageIndex, 1);
                
                // Update database
                $laporan->update(['bukti' => json_encode($buktiArray)]);
                
                return response()->json(['success' => true]);
            }
            
            return response()->json(['success' => false, 'message' => 'Gambar tidak ditemukan']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function downloadLaporan($id)
    {
        $laporan = SafetyRiding::with(['pds', 'pfs', 'user.section'])->findOrFail($id);
        
        // Buat konten CSV
        $csvContent = "LAPORAN SAFETY RIDING\n";
        $csvContent .= "No. Laporan: {$laporan->id}\n";
        $csvContent .= "Waktu Kejadian: " . \Carbon\Carbon::parse($laporan->waktu_kejadian)->format('d-m-Y H:i') . "\n";
        $csvContent .= "Section: " . ($laporan->user->section->section ?? 'N/A') . "\n";
        $csvContent .= "Nama Pelanggar: " . ($laporan->user->nama ?? 'N/A') . "\n";
        $csvContent .= "Tipe Kendaraan: {$laporan->type_kendaraan}\n";
        $csvContent .= "NOPOL: {$laporan->nopol}\n";
        $csvContent .= "Keterangan: {$laporan->keterangan_pelanggaran}\n";
        $csvContent .= "Status: {$laporan->status}\n";
        
        $csvContent .= "\nPelanggaran Dokumen:\n";
        foreach ($laporan->pds as $pd) {
            $csvContent .= "- {$pd->nama_pd}\n";
        }
        
        $csvContent .= "\nPelanggaran Fisik:\n";
        foreach ($laporan->pfs as $pf) {
            $csvContent .= "- {$pf->nama_pf}\n";
        }
        
        $filename = "safety_riding_{$laporan->id}_" . date('Ymd_His') . ".txt";
        
        return response($csvContent)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function tindakLanjut(Request $request, $id)
    {
        $laporan = SafetyRiding::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:Close,Rejected',
            'catatan' => 'nullable|string|max:1000'
        ]);
        
        $laporan->status = $request->status;

        if ($request->filled('catatan')) {
            $laporan->catatan = $request->catatan;
        }
        
        $laporan->save();
        
        return redirect()->back()->with('success', 'Status berhasil diupdate menjadi ' . $request->status);
    }
}