<?php

namespace App\Http\Controllers\PIC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PICProgramSafetyController extends Controller
{
    /**
     * Display a listing of Program Safety per departemen.
     * PIC hanya bisa melihat, tidak bisa edit/delete.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil department user berdasarkan section
        $userDepartment = DB::table('tb_section')
            ->where('id', $user->section_id)
            ->value('department');

        // Ambil semua program safety untuk departemen user
        $programs = DB::table('tb_programsafety as ps')
            ->join('tb_section as s', 'ps.section_id', '=', 's.id')
            ->where('s.department', $userDepartment)
            ->select(
                'ps.id',
                'ps.nama_program',
                'ps.deskripsi',
                'ps.aktivitas',
                'ps.target',
                'ps.budget',
                'ps.plan_date',
                'ps.due_date',
                'ps.status',
                's.department'
            )
            ->when($request->search, function ($q) use ($request) {
                $q->where('ps.nama_program', 'like', '%' . $request->search . '%');
            })
            ->orderBy('ps.plan_date', 'desc')
            ->get();

        // Return view PIC (buat blade read-only)
        return view('PIC.programsafety', compact('programs'));
    }
}
