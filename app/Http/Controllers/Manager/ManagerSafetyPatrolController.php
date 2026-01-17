<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SafetyPatrol;

class ManagerSafetyPatrolController extends Controller
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
         * AMBIL DATA SAFETY PATROL
         * FILTER BERDASARKAN DEPARTMENT
         * ==============================
         */
        $safetypatrols = SafetyPatrol::with('section')
            ->whereHas('section', function ($query) use ($department) {
                $query->where('department', $department);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        /**
         * ==============================
         * VIEW ONLY (TANPA AKSI)
         * ==============================
         */
        $permission = (object) [
            'can_edit'   => false,
            'can_delete' => false,
        ];

        return view('manager.safety_patrol', compact(
            'safetypatrols',
            'permission'
        ));
    }
}
