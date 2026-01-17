<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProgramSafetyController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // ==============================
        // CEK HAK AKSES MANAGER
        // ==============================
        $hasAccess = DB::table('tb_approval')
            ->where('user_id', $user->id)
            ->where('section_id', $user->section_id)
            ->where('approval_level', 'Manager')
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki hak akses ke Program Safety');
        }

        // ==============================
        // AMBIL DATA PROGRAM SAFETY
        // ==============================
        $programs = DB::table('tb_programsafety as ps')
    ->join('tb_section as s', 'ps.section_id', '=', 's.id')
    ->where('ps.section_id', $user->section_id)
    ->select(
        'ps.*',
        's.department'
    )
    ->when($request->search, function ($q) use ($request) {
        $q->where('ps.nama_program', 'like', '%' . $request->search . '%');
    })
    ->orderBy('ps.plan_date', 'desc')
    ->get();


        return view('manager.programsafety', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_program' => 'required',
            'deskripsi'    => 'required',
            'aktivitas'    => 'required',
        ]);

        DB::table('tb_programsafety')->insert([
            'nama_program' => $request->nama_program,
            'deskripsi'    => $request->deskripsi,
            'aktivitas'    => $request->aktivitas,
            'target'       => $request->target,
            'budget'       => $request->budget,
            'plan_date'    => $request->plan_date,
            'due_date'     => $request->due_date,
            'remark'       => $request->remark,
            'status'       => 'Open',
            'section_id'   => Auth::user()->section_id,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return back()->with('success', 'Program Safety berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        DB::table('tb_programsafety')
            ->where('id', $id)
            ->where('section_id', Auth::user()->section_id)
            ->update([
                'nama_program' => $request->nama_program,
                'deskripsi'    => $request->deskripsi,
                'aktivitas'    => $request->aktivitas,
                'target'       => $request->target,
                'budget'       => $request->budget,
                'plan_date'    => $request->plan_date,
                'due_date'     => $request->due_date,
                'remark'       => $request->remark,
                'status'       => $request->status,
                'updated_at'   => now(),
            ]);

        return back()->with('success', 'Program Safety berhasil diperbarui');
    }

    public function destroy($id)
    {
        DB::table('tb_programsafety')
            ->where('id', $id)
            ->where('section_id', Auth::user()->section_id)
            ->delete();

        return back()->with('success', 'Program Safety berhasil dihapus');
    }
}
