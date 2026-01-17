<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagerInsidenController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        /**
         * ==============================
         * CEK HAK AKSES MANAGER
         * ==============================
         */
        $approval = DB::table('tb_approval')
            ->where('user_id', $user->id)
            ->where('approval_level', 'Manager')
            ->first();

        if (!$approval) {
            abort(403, 'Anda bukan Manager');
        }

        /**
         * ==============================
         * AMBIL DEPARTMENT MANAGER
         * ==============================
         */
        $department = DB::table('tb_section')
            ->where('id', $user->section_id)
            ->value('department');

        /**
         * ==============================
         * AMBIL SEMUA INSIDEN
         * DALAM DEPARTMENT YANG SAMA
         * ==============================
         */
        $insidens = DB::table('tb_insiden as i')
            ->join('tb_section as s', 'i.section_id', '=', 's.id')
            ->where('s.department', $department)
            ->select(
                'i.*',
                's.section',
                's.department'
            )
            ->orderBy('i.created_at', 'desc')
            ->get();

        return view('manager.insiden', compact('insidens'));
    }
}
