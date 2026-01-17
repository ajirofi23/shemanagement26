<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SafetyRiding;

class ManagerSafetyRidingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ==============================
        // CEK MANAGER
        // ==============================
        $approval = DB::table('tb_approval')
            ->where('user_id', $user->id)
            ->where('approval_level', 'Manager')
            ->first();

        if (!$approval) {
            abort(403, 'Anda bukan Manager');
        }

        // ==============================
        // AMBIL DEPARTMENT MANAGER
        // ==============================
        $department = DB::table('tb_section')
            ->where('id', $user->section_id)
            ->value('department');

        // ==============================
        // AMBIL SAFETY RIDING SESUAI DEPARTMENT
        // ==============================
        $safetyridings = SafetyRiding::with([
                'user.section',
                'pds',
                'pfs'
            ])
            ->whereHas('user.section', function ($q) use ($department) {
                $q->where('department', $department);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // VIEW ONLY
        $permission = (object)[
            'can_add' => false,
            'can_edit' => false,
            'can_delete' => false,
        ];

        return view('manager.safety_riding', [
            'safetyridings' => $safetyridings,
            'permission'    => $permission,
        ]);
    }
}
