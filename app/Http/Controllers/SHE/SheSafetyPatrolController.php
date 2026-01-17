<?php

namespace App\Http\Controllers\SHE;

use App\Http\Controllers\Controller;
use App\Models\SafetyPatrol;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SheSafetyPatrolController extends Controller
{
    private function getPermission($feature)
    {
        return (object)['can_edit' => true, 'can_delete' => true];
    }

    public function index(Request $request)
    {
        $safetypatrols = SafetyPatrol::with(['section', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->where('eporte', 'like', '%' . $search . '%')
                      ->orWhere('area', 'like', '%' . $search . '%')
                      ->orWhere('problem', 'like', '%' . $search . '%');
            })
            ->latest()
            ->get();

        $sections = Section::all();
        $permission = $this->getPermission('safety_patrol');
        $currentUser = Auth::user();

        return view('SHE.safetypatrol', compact('safetypatrols', 'sections', 'permission', 'currentUser'));
    }

    public function store(Request $request)
{
   
    
    $validated = $request->validate([
        'tanggal' => 'required|date',
        'eporte' => 'required|string|max:255',
        'area' => 'required|string|max:255',
        'problem' => 'required|string',
        'counter_measure' => 'required|string',
        'section_id' => 'required|exists:tb_section,id',
        'due_date' => 'required|date',
        'foto_before' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'status' => 'required|in:Open,Progress,Close,Rejected',
    ]);

    try {
        // Handle foto before upload
        $fotoBeforePath = null;
        if ($request->hasFile('foto_before')) {
            if ($request->file('foto_before')->isValid()) {
                $fotoBefore = $request->file('foto_before');
                $filename = 'before_' . time() . '_' . $fotoBefore->getClientOriginalName();
                $path = $fotoBefore->storeAs('safety_patrol/before', $filename, 'public');
                $fotoBeforePath = $path;
    
            }
        } 

        $safetyPatrol = SafetyPatrol::create([
            'tanggal' => $request->tanggal,
            'eporte' => $request->eporte,
            'area' => $request->area,
            'problem' => $request->problem,
            'counter_measure' => $request->counter_measure,
            'section_id' => $request->section_id,
            'due_date' => $request->due_date,
            'foto_before' => $fotoBeforePath,
            'created_by' => Auth::id(),
            'status' => $request->status ?? 'Open',
        ]);

        // dd($safetyPatrol); // Debug data yang dibuat

        return redirect()->to('/she/safety-patrol')->with('success', 'Laporan Safety Patrol berhasil dibuat.');

    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', 'Gagal membuat laporan: ' . $e->getMessage());
    }
}
   public function update(Request $request, $id)
{
   
    
    $safetyPatrol = SafetyPatrol::findOrFail($id);
    
    // Validasi untuk status Open (hanya Open yang bisa di-edit)
    if ($safetyPatrol->status !== 'Open') {
        return redirect()->back()->with('error', 'Hanya laporan dengan status Open yang dapat di-edit.');
    }
    
    $request->validate([
        'tanggal' => 'required|date',
        'eporte' => 'required|string|max:255',
        'area' => 'required|string|max:255',
        'problem' => 'required|string',
        'counter_measure' => 'required|string',
        'section_id' => 'required|exists:tb_section,id',
        'due_date' => 'required|date',
        'foto_before_baru' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    try {
        $updateData = [
            'tanggal' => $request->tanggal,
            'eporte' => $request->eporte,
            'area' => $request->area,
            'problem' => $request->problem,
            'counter_measure' => $request->counter_measure,
            'section_id' => $request->section_id,
            'due_date' => $request->due_date,
            'status' => 'Open',
        ];

       
        if ($request->hasFile('foto_before_baru') && $request->file('foto_before_baru')->isValid()) {
          
            
            // Hapus foto lama jika ada
            if ($safetyPatrol->foto_before && Storage::disk('public')->exists($safetyPatrol->foto_before)) {
                
                Storage::disk('public')->delete($safetyPatrol->foto_before);
            }

            // Upload foto baru
            $fotoBefore = $request->file('foto_before_baru');
            $filename = 'before_' . time() . '_' . uniqid() . '.' . $fotoBefore->getClientOriginalExtension();
            
            $path = $fotoBefore->storeAs('safety_patrol/before', $filename, 'public');
           
            
            $updateData['foto_before'] = $path;
            
        } elseif ($request->has('hapus_foto_before') && $request->hapus_foto_before == '1') {
           
            if ($safetyPatrol->foto_before && Storage::disk('public')->exists($safetyPatrol->foto_before)) {
                
                Storage::disk('public')->delete($safetyPatrol->foto_before);
            }
            $updateData['foto_before'] = null;
            
        } else {
            
            $updateData['foto_before'] = $safetyPatrol->foto_before;
        }


        $safetyPatrol->update($updateData);
       

        return redirect()->to('/she/safety-patrol')->with('success', 'Laporan Safety Patrol berhasil diperbarui.');

    } catch (\Exception $e) {
       
        
        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui laporan: ' . $e->getMessage());
    }
}

    public function destroy($id)
    {
        try {
            $safetyPatrol = SafetyPatrol::findOrFail($id);
            
            // Hapus foto before jika ada
            if ($safetyPatrol->foto_before && Storage::disk('public')->exists($safetyPatrol->foto_before)) {
                Storage::disk('public')->delete($safetyPatrol->foto_before);
            }
            
            // Hapus foto after jika ada
            if ($safetyPatrol->foto_after && Storage::disk('public')->exists($safetyPatrol->foto_after)) {
                Storage::disk('public')->delete($safetyPatrol->foto_after);
            }
            
            $safetyPatrol->delete();

            return redirect()->to('/she/safety-patrol')->with('success', 'Laporan Safety Patrol berhasil dihapus!');
            
        } catch (\Exception $e) {
            return redirect()->to('/she/safety-patrol')->with('error', 'Gagal menghapus laporan: ' . $e->getMessage());
        }
    }

    public function deleteImage($id, $type)
    {
        try {
            $safetyPatrol = SafetyPatrol::findOrFail($id);
            
            if ($type === 'before' && $safetyPatrol->foto_before) {
                if (Storage::disk('public')->exists($safetyPatrol->foto_before)) {
                    Storage::disk('public')->delete($safetyPatrol->foto_before);
                }
                $safetyPatrol->update(['foto_before' => null]);
            } elseif ($type === 'after' && $safetyPatrol->foto_after) {
                if (Storage::disk('public')->exists($safetyPatrol->foto_after)) {
                    Storage::disk('public')->delete($safetyPatrol->foto_after);
                }
                $safetyPatrol->update(['foto_after' => null]);
            }
            
            return response()->json(['success' => true, 'message' => 'Foto berhasil dihapus']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function tindakLanjut(Request $request, $id)
    {
        $safetyPatrol = SafetyPatrol::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:Progress,Close,Rejected',
            'catatan' => 'nullable|string|max:1000',
        ]);

        try {
            $updateData = [
                'status' => $request->status,
            ];

            if ($request->filled('catatan')) {
                $updateData['catatan'] = $request->catatan;
            }

        


            // Validasi khusus untuk status Rejected
            if ($request->status === 'Rejected' && empty($request->catatan)) {
                return redirect()->back()->withInput()->with('error', 'Harap isi catatan untuk menolak laporan.');
            }

            $safetyPatrol->update($updateData);

            return redirect()->back()->with('success', 'Status berhasil diupdate menjadi ' . $request->status);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate status: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $safetypatrols = SafetyPatrol::with(['section', 'user'])->latest()->get();
        
        $csvContent = "No,Tanggal,E-PORTE,Area,Problem,Counter Measure,Section,Due Date,Status,Created By\n";
        
        foreach ($safetypatrols as $index => $patrol) {
            $csvContent .= ($index + 1) . ",";
            $csvContent .= '"' . Carbon::parse($patrol->tanggal)->format('d-m-Y') . '",';
            $csvContent .= '"' . $patrol->eporte . '",';
            $csvContent .= '"' . $patrol->area . '",';
            $csvContent .= '"' . str_replace('"', '""', $patrol->problem) . '",';
            $csvContent .= '"' . str_replace('"', '""', $patrol->counter_measure) . '",';
            $csvContent .= '"' . ($patrol->section->section ?? 'N/A') . '",';
            $csvContent .= '"' . ($patrol->due_date ? Carbon::parse($patrol->due_date)->format('d-m-Y') : '') . '",';
            $csvContent .= '"' . $patrol->status . '",';
            $csvContent .= '"' . ($patrol->user->nama ?? 'N/A') . '"';
            $csvContent .= "\n";
        }
        
        $filename = "safety_patrol_export_" . date('Ymd_His') . ".csv";
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function downloadLaporan($id)
    {
        $laporan = SafetyPatrol::with(['section', 'user'])->findOrFail($id);
        
        $csvContent = "LAPORAN SAFETY PATROL\n";
        $csvContent .= "No. Laporan: {$laporan->id}\n";
        $csvContent .= "Tanggal: " . Carbon::parse($laporan->tanggal)->format('d-m-Y') . "\n";
        $csvContent .= "E-PORTE: {$laporan->eporte}\n";
        $csvContent .= "Area: {$laporan->area}\n";
        $csvContent .= "Problem: {$laporan->problem}\n";
        $csvContent .= "Counter Measure: {$laporan->counter_measure}\n";
        $csvContent .= "Section: " . ($laporan->section->section ?? 'N/A') . "\n";
        $csvContent .= "Due Date: " . ($laporan->due_date ? Carbon::parse($laporan->due_date)->format('d-m-Y') : '-') . "\n";
        $csvContent .= "Status: {$laporan->status}\n";
        $csvContent .= "Created By: " . ($laporan->user->nama ?? 'N/A') . "\n";
        
        if ($laporan->catatan) {
            $csvContent .= "Catatan: {$laporan->catatan}\n";
        }
        
        $filename = "safety_patrol_{$laporan->id}_" . date('Ymd_His') . ".txt";
        
        return response($csvContent)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}