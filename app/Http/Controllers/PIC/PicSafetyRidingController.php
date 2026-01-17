<?php

namespace App\Http\Controllers\PIC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\SafetyRiding;
use App\Models\PelanggaranDokumen;
use App\Models\PelanggaranFisik;
use App\Models\User;
use App\Models\Section;

class PicSafetyRidingController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        
        // Ambil data berdasarkan section_id dari user yang login
        $safetyridings = SafetyRiding::with(['pds', 'pfs', 'user.section'])
            ->whereHas('user', function ($query) use ($currentUser) {
                $query->where('section_id', $currentUser->section_id);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nopol', 'like', '%' . $search . '%')
                        ->orWhere('keterangan_pelanggaran', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('nama', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest()
            ->get();

        $masterPds = PelanggaranDokumen::all();
        $masterPfs = PelanggaranFisik::all();
        $sections = Section::all();
        
        // Permission sederhana untuk PIC
        $permission = (object)[
            'can_edit' => false, // PIC tidak bisa edit data laporan
            'can_delete' => false, // PIC tidak bisa delete laporan
            'can_create' => false, // PIC tidak bisa create laporan baru
        ];

        return view('PIC.safetyriding', compact('safetyridings', 'masterPds', 'masterPfs', 'sections', 'permission', 'currentUser'));
    }

    public function uploadAfter(Request $request, $id)
{
    $laporan = SafetyRiding::findOrFail($id);
    
    $request->validate([
        'bukti_after.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120'
        // Hapus validasi untuk status
    ]);
    
    // Get existing after files
    $existingAfter = $laporan->bukti_after ? json_decode($laporan->bukti_after, true) : [];
    
    // Handle new uploads
    $newAfterFiles = [];
    
    if ($request->hasFile('bukti_after')) {
        foreach ($request->file('bukti_after') as $key => $file) {
            if ($file && $file->isValid()) {
                // Generate unique filename
                $filename = 'after_' . $laporan->id . '_' . time() . '_' . ($key + 1) . '.' . $file->getClientOriginalExtension();
                
                // Store file
                $path = $file->storeAs('safety_riding/after', $filename, 'public');
                
                if ($path) {
                    $newAfterFiles[] = $path;
                }
            }
        }
    }
    
    // Merge existing and new files
    $existingFromRequest = $request->input('existing_after', []);
    $allAfterFiles = array_merge($existingFromRequest, $newAfterFiles);
    
    // Update database - SELALU KE PROGRESS
    $laporan->bukti_after = json_encode($allAfterFiles);
    $laporan->status = 'Progress'; // <-- SELALU PROGRESS
    $laporan->save();
    
    return redirect()->back()->with('success', 'Foto after berhasil diupload! Status diubah menjadi Progress.');
}

public function deleteAfterImage($id, $index)
{
    $laporan = SafetyRiding::findOrFail($id);
    
    $buktiAfter = json_decode($laporan->bukti_after, true) ?? [];
    
    if (isset($buktiAfter[$index])) {
        // Delete file from storage
        Storage::disk('public')->delete($buktiAfter[$index]);
        
        // Remove from array
        unset($buktiAfter[$index]);
        
        // Re-index array
        $buktiAfter = array_values($buktiAfter);
        
        // Update database
        $laporan->bukti_after = json_encode($buktiAfter);
        $laporan->save();
        
        return response()->json(['success' => true]);
    }
    
    return response()->json(['success' => false, 'message' => 'Foto tidak ditemukan'], 404);
}

}