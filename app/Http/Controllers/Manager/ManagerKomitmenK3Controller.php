<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagerKomitmenK3Controller extends Controller
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
         * AMBIL KOMITMEN K3
         * SEMUA SECTION DALAM DEPARTMENT
         * ==============================
         */
        $komitmenK3 = DB::table('tb_komitment_k3 as k3')
            ->join('tb_user as u', 'k3.user_id', '=', 'u.id')
            ->join('tb_section as s', 'u.section_id', '=', 's.id')
            ->where('s.department', $department)
            ->select(
                'k3.*',
                'u.nama as nama_user',
                's.section',
                's.department'
            )
            ->orderBy('k3.created_at', 'desc')
            ->get();

        return view('manager.komitmen_k3', compact('komitmenK3'));
    }
}
