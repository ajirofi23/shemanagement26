<?php

namespace App\Http\Controllers\PIC;

use App\Http\Controllers\Controller;
use App\Models\SafetyPatrol;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PicSafetyPatrolController extends Controller
{
    /* ======================================================
     * INDEX – LIST LAPORAN (PIC, FILTER BY SECTION)
     * ====================================================== */
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        $safetypatrols = SafetyPatrol::with(['section', 'user'])
            ->where('section_id', $currentUser->section_id)
            ->when($request->search, function ($q, $search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('eporte', 'like', "%$search%")
                       ->orWhere('area', 'like', "%$search%")
                       ->orWhere('problem', 'like', "%$search%");
                });
            })
            ->latest()
            ->get();

        $sections = Section::all();

        return view('PIC.safetypatrol', compact(
            'safetypatrols',
            'sections',
            'currentUser'
        ));
    }

    /* ======================================================
     * UPLOAD FOTO AFTER (FIXED – MOVE, NOT STORE)
     * ====================================================== */
    public function uploadAfter(Request $request, $id)
    {
        $patrol = SafetyPatrol::findOrFail($id);
        $user = Auth::user();

        if ($patrol->section_id !== $user->section_id) {
            return back()->with('error', 'Akses ditolak.');
        }

        if (!in_array($patrol->status, ['Open', 'Progress', 'Rejected'])) {
            return back()->with('error', 'Status tidak valid.');
        }

        $request->validate([
            'foto_after' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            /* HAPUS FOTO LAMA */
            if ($patrol->foto_after) {
                $old = public_path('storage/' . $patrol->foto_after);
                if (file_exists($old)) {
                    unlink($old);
                }
            }

            /* UPLOAD FOTO BARU */
            $file = $request->file('foto_after');
            $filename = 'after_' . $patrol->id . '_' . time() . '.' . $file->getClientOriginalExtension();

            $dir = public_path('storage/safety_patrol/after');
            if (!file_exists($dir)) {
                mkdir($dir, 0775, true);
            }

            $file->move($dir, $filename);

            /* UPDATE DB */
            $patrol->update([
                'foto_after' => 'safety_patrol/after/' . $filename,
                'status'     => 'Progress',
                'updated_by' => $user->id,
                'updated_at' => now(),
            ]);

            return redirect('/pic/safety-patrol')
                ->with('success', 'Foto after berhasil diupload.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Upload gagal: ' . $e->getMessage());
        }
    }

    /* ======================================================
     * DELETE FOTO AFTER
     * ====================================================== */
    public function deleteAfter($id)
    {
        $patrol = SafetyPatrol::findOrFail($id);
        $user = Auth::user();

        if ($patrol->section_id !== $user->section_id) {
            return response()->json(['success' => false], 403);
        }

        if ($patrol->foto_after) {
            $path = public_path('storage/' . $patrol->foto_after);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $patrol->update([
            'foto_after' => null,
            'status'     => 'Open',
            'updated_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Foto after dihapus.'
        ]);
    }

    /* ======================================================
     * EXPORT CSV (PIC)
     * ====================================================== */
    public function export()
    {
        $user = Auth::user();

        $patrols = SafetyPatrol::with(['section', 'user'])
            ->where('section_id', $user->section_id)
            ->latest()
            ->get();

        $csv = "No,Tanggal,E-PORTE,Area,Problem,Counter Measure,Section,Due Date,Status\n";

        foreach ($patrols as $i => $p) {
            $csv .= ($i+1).",";
            $csv .= Carbon::parse($p->tanggal)->format('d-m-Y').",";
            $csv .= "\"$p->eporte\",\"$p->area\",";
            $csv .= "\"".str_replace('"','""',$p->problem)."\",";
            $csv .= "\"".str_replace('"','""',$p->counter_measure)."\",";
            $csv .= "\"{$p->section->section}\",";
            $csv .= ($p->due_date ? Carbon::parse($p->due_date)->format('d-m-Y') : '').",";
            $csv .= "$p->status\n";
        }

        return response($csv)
            ->header('Content-Type','text/csv')
            ->header('Content-Disposition','attachment; filename=safety_patrol_pic.csv');
    }

    /* ======================================================
     * VIEW DETAIL (AJAX / MODAL)
     * ====================================================== */
    public function viewDetail($id)
    {
        $patrol = SafetyPatrol::with(['section','user'])->findOrFail($id);
        $user = Auth::user();

        if ($patrol->section_id !== $user->section_id) {
            return response()->json(['success' => false], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id'            => $patrol->id,
                'tanggal'       => Carbon::parse($patrol->tanggal)->format('d-m-Y'),
                'eporte'        => $patrol->eporte,
                'area'          => $patrol->area,
                'problem'       => $patrol->problem,
                'counter'       => $patrol->counter_measure,
                'section'       => $patrol->section->section,
                'status'        => $patrol->status,
                'foto_before'   => $patrol->foto_before ? asset('storage/'.$patrol->foto_before) : null,
                'foto_after'    => $patrol->foto_after ? asset('storage/'.$patrol->foto_after) : null,
            ]
        ]);
    }
}
