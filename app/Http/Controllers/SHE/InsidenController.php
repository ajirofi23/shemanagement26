<?php

namespace App\Http\Controllers\SHE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Section;
use App\Models\Insiden;
use App\Services\PermissionService;
use Carbon\Carbon;

class InsidenController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Helper untuk mendapatkan informasi user
     */
    private function getUserInfo()
    {
        $user = Auth::user();
        $userDepartment = $user->section->department ?? null;
        $userSectionId = $user->section_id ?? null;

        return (object) [
            'user' => $user,
            'department' => $userDepartment,
            'section_id' => $userSectionId
        ];
    }

    /**
     * Helper untuk mendapatkan array foto dari JSON string
     */
    private function getFotoArray($fotoJson)
    {
        if (empty($fotoJson)) {
            return [];
        }

        if (is_array($fotoJson)) {
            return $fotoJson;
        }

        $decoded = json_decode($fotoJson, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Helper untuk memproses upload dan update foto
     */
    private function processFotoUpdate($currentFotoJson, $request)
    {
        $currentFotos = $this->getFotoArray($currentFotoJson);
        $keptFotos = $currentFotos;

        if ($request->filled('existing_fotos')) {
            try {
                $keptFotos = json_decode($request->existing_fotos, true);
                if (!is_array($keptFotos)) {
                    $keptFotos = $currentFotos;
                }
            } catch (\Exception $e) {
                $keptFotos = $currentFotos;
            }
        }

        $deletedFotos = array_diff($currentFotos, $keptFotos);
        foreach ($deletedFotos as $fotoPath) {
            if (Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
        }

        $newFotoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $foto) {
                $path = $foto->store('insiden_foto', 'public');
                $newFotoPaths[] = $path;
            }
        }

        $allFotos = array_merge($keptFotos, $newFotoPaths);
        return !empty($allFotos) ? json_encode($allFotos) : null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $permissions = $this->permissionService->getPermissions();

        if (!$permissions->can_read) {
            return redirect('/dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $userInfo = $this->getUserInfo();
        $currentUser = $userInfo->user;
        $currentUrl = request()->path();
        $currentUrl = '/' . ltrim($currentUrl, '/');

        $modifiedRequest = clone $request;

        $query = Insiden::select('tb_insiden.*', 'tb_section.section as section_name', 'tb_section.department as section_department')
            ->leftJoin('tb_section', 'tb_insiden.section_id', '=', 'tb_section.id')
            ->orderBy('tb_insiden.tanggal', 'desc')
            ->orderBy('tb_insiden.jam', 'desc');

        if (strpos($currentUrl, '/manager/insiden') !== false && $userInfo->department) {
            $query->where('tb_section.department', $userInfo->department);
            if (!$modifiedRequest->filled('department')) {
                $modifiedRequest->merge(['department' => $userInfo->department]);
            }
        } elseif (strpos($currentUrl, '/pic/insiden') !== false && $userInfo->section_id) {
            $query->where('tb_insiden.section_id', $userInfo->section_id);
            if (!$modifiedRequest->filled('section_id')) {
                $modifiedRequest->merge(['section_id' => $userInfo->section_id]);
            }
        }

        if ($modifiedRequest->filled('tanggal')) {
            $query->whereDate('tb_insiden.tanggal', $modifiedRequest->tanggal);
        }

        if ($modifiedRequest->filled('bulan')) {
            $bulan = $modifiedRequest->bulan;
            $query->whereYear('tb_insiden.tanggal', substr($bulan, 0, 4))
                ->whereMonth('tb_insiden.tanggal', substr($bulan, 5, 2));
        }

        if ($modifiedRequest->filled('section_id')) {
            $query->where('tb_insiden.section_id', $modifiedRequest->section_id);
        }

        if ($modifiedRequest->filled('department')) {
            $query->where('tb_insiden.departemen', 'like', '%' . $modifiedRequest->department . '%');
        }

        if ($modifiedRequest->filled('kategori')) {
            $query->where('tb_insiden.kategori', $modifiedRequest->kategori);
        }

        if ($modifiedRequest->filled('status')) {
            $query->where('tb_insiden.status', $modifiedRequest->status);
        }

        if ($modifiedRequest->filled('search_lokasi')) {
            $query->where('tb_insiden.lokasi', 'like', '%' . $modifiedRequest->search_lokasi . '%');
        }

        $sectionsQuery = Section::orderBy('section', 'asc');

        if (strpos($currentUrl, '/manager/insiden') !== false && $userInfo->department) {
            $sections = $sectionsQuery->where('department', $userInfo->department)->get();
        } elseif (strpos($currentUrl, '/pic/insiden') !== false && $userInfo->section_id) {
            $sections = $sectionsQuery->where('id', $userInfo->section_id)->get();
        } else {
            $sections = $sectionsQuery->get();
        }

        $departmentsQuery = Insiden::select('departemen')
            ->distinct()
            ->whereNotNull('departemen');

        if (strpos($currentUrl, '/manager/insiden') !== false && $userInfo->department) {
            $departments = collect([$userInfo->department]);
        } elseif (strpos($currentUrl, '/pic/insiden') !== false && $userInfo->section_id) {
            $userSection = Section::find($userInfo->section_id);
            $departments = $userSection && $userSection->department
                ? collect([$userSection->department])
                : collect();
        } else {
            $departments = $departmentsQuery->orderBy('departemen', 'asc')
                ->get()
                ->pluck('departemen')
                ->filter()
                ->values();
        }

        $kategoriList = Insiden::select('kategori')
            ->distinct()
            ->whereNotNull('kategori')
            ->orderBy('kategori', 'asc')
            ->get()
            ->pluck('kategori')
            ->filter()
            ->values();

        $insidens = $query->paginate(20)->appends($modifiedRequest->except('page'));

        $viewRequest = $modifiedRequest->all();
        $isManagerAccess = strpos($currentUrl, '/manager/insiden') !== false;
        $isPICAccess = strpos($currentUrl, '/pic/insiden') !== false;
        $isSHEAccess = strpos($currentUrl, '/she/insiden') !== false;

        return view('SHE.insiden', compact(
            'insidens',
            'sections',
            'departments',
            'kategoriList',
            'permissions',
            'currentUser',
            'userInfo',
            'viewRequest',
            'currentUrl',
            'isManagerAccess',
            'isPICAccess',
            'isSHEAccess'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = $this->permissionService->getPermissions();

        if (!$permissions->can_add) {
            return redirect('/she/dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $user = Auth::user();
        $sections = Section::all();
        $sectionUser = null;
        $namaSection = null;

        if ($user->section_id) {
            $sectionUser = Section::find($user->section_id);
            $namaSection = $sectionUser ? $sectionUser->section : null;
        }

        return view('SHE.insiden_form', compact(
            'sections',
            'permissions',
            'user',
            'namaSection'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $permissions = $this->permissionService->getPermissions();

        if (!$permissions->can_add) {
            return redirect('/dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk menambahkan data.');
        }

        // Gunakan timezone Asia/Jakarta untuk validasi tanggal
        $todayJakarta = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        $validated = $request->validate([
            'tanggal' => 'required|date|before_or_equal:' . $todayJakarta,
            'jam' => 'required|date_format:H:i',
            'lokasi' => 'required|string|max:255',
            'kategori' => 'required|in:Work Accident,Traffic Accident,Fire Accident,Forklift Accident,Molten Spill Incident,Property Damage Incident',
            'section_id' => 'required|exists:tb_section,id',
            'department' => 'required|string|max:100',
            'kondisi_luka' => 'nullable|string|max:255'
        ]);

        if ($request->kategori === 'Work Accident') {
            $request->validate([
                'work_accident_type' => 'required|in:Loss Day,Light'
            ]);
        }

        $insidenData = [
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'lokasi' => $request->lokasi,
            'kategori' => $request->kategori,
            'work_accident_type' => $request->work_accident_type ?? null,
            'section_id' => $request->section_id,
            'departemen' => $request->department,
            'kondisi_luka' => $request->kondisi_luka,
            'kronologi' => $request->kronologi,
            'status' => 'open',
            'created_by' => Auth::id(),
        ];

        try {
            $insiden = Insiden::create($insidenData);
            return redirect('/she/insiden')
                ->with('success', 'Laporan insiden berhasil dibuat! ID: ' . $insiden->id);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $permissions = $this->permissionService->getPermissions();

        if (!$permissions->can_edit) {
            return redirect('/dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk mengedit data.');
        }

        $insiden = Insiden::with(['user', 'section', 'creator'])->findOrFail($id);
        $userInfo = $this->getUserInfo();

        if ($insiden->foto && !is_string($insiden->foto)) {
            $insiden->foto = json_encode($insiden->foto);
        }

        $sections = Section::all();

        $currentUrl = request()->path();
        $currentUrl = '/' . ltrim($currentUrl, '/');
        $isPIC = strpos($currentUrl, '/pic/insiden/edit') !== false;

        $users = collect();

        if ($isPIC) {
            if (!empty($userInfo->section_id)) {
                $users = User::where('section_id', $userInfo->section_id)
                    ->where('is_active', 1)
                    ->orderBy('nama', 'asc')
                    ->get();
            } else {
                session()->flash('warning', 'Akun PIC Anda belum memiliki section. Anda tidak dapat memilih korban.');
            }
        } else {
            $users = User::where('is_active', 1)
                ->orderBy('nama', 'asc')
                ->get();
        }

        return view('SHE.insiden_edit', compact('insiden', 'permissions', 'userInfo', 'sections', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $permissions = $this->permissionService->getPermissions();

        if (!$permissions->can_edit) {
            return redirect('/dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk mengedit data.');
        }

        $insiden = Insiden::findOrFail($id);
        $currentUrl = request()->path();
        $currentUrl = '/' . ltrim($currentUrl, '/');

        $isPIC = strpos($currentUrl, '/pic/insiden') !== false;
        $isSHE = strpos($currentUrl, '/she/insiden') !== false;
        $isManager = strpos($currentUrl, '/manager/insiden') !== false;
        $isCreator = Auth::id() == $insiden->created_by;

        $updateData = [];

        if ($isCreator && $insiden->status === 'open') {
            // Gunakan timezone Asia/Jakarta untuk validasi tanggal
            $todayJakarta = Carbon::now('Asia/Jakarta')->format('Y-m-d');

            $validated = $request->validate([
                'tanggal' => 'required|date|before_or_equal:' . $todayJakarta,
                'jam' => 'required|date_format:H:i',
                'lokasi' => 'required|string|max:255',
                'kondisi_luka' => 'nullable|string|max:255',
                'section_id' => 'required',
                'department' => 'required|string|max:100'
            ]);

            $updateData = [
                'tanggal' => $request->tanggal,
                'jam' => $request->jam,
                'lokasi' => $request->lokasi,
                'kondisi_luka' => $request->kondisi_luka,
                'section_id' => $request->section_id,
                'departemen' => $request->department,
                'kronologi' => $request->kronologi,
            ];
        } elseif ($isPIC && $insiden->status === 'open' || $isPIC && $insiden->status === 'rejected') {
            $validated = $request->validate([
                'kronologi' => 'required|string|max:3000',
                'keterangan_lain' => 'nullable|string',
                'foto' => 'nullable|array',
                'foto.*' => 'image|mimes:jpeg,png,jpg,gif,bmp|max:10240',
            ]);

            $updateData = [
                'kronologi' => $request->kronologi,
                'keterangan_lain' => $request->keterangan_lain,
                'user_id' => $request->user_id
            ];

            $foto = $this->processFotoUpdate($insiden->foto, $request);
            if ($foto !== null) {
                $updateData['foto'] = $foto;
            }

            $updateData['status'] = 'progress';
        } elseif ($isSHE && $insiden->status === 'progress') {
            $validated = $request->validate([
                'status' => 'required|in:closed,rejected'
            ]);

            if ($request->status == 'closed') {
                $updateData = [
                    'status' => $request->status,
                    'catatan_close' => $request->catatan_she
                ];
            } else {
                $updateData = [
                    'status' => $request->status,
                    'catatan_reject' => $request->catatan_she
                ];
            }
        }

        try {
            $insiden->update($updateData);

            $redirectUrl = '';
            if ($isPIC) {
                $redirectUrl = '/pic/insiden';
            } elseif ($isSHE) {
                $redirectUrl = '/she/insiden';
            } elseif ($isManager) {
                $redirectUrl = '/manager/insiden';
            } else {
                $redirectUrl = '/insiden';
            }

            $message = 'Data insiden berhasil diperbarui!';

            if (isset($updateData['status'])) {
                if ($updateData['status'] === 'progress') {
                    $message = 'Kronologi berhasil disimpan! Status berubah menjadi In Progress dan menunggu review SHE.';
                } elseif ($updateData['status'] === 'closed') {
                    $message = 'Laporan insiden telah disetujui dan ditutup!';
                } elseif ($updateData['status'] === 'rejected') {
                    $message = 'Laporan insiden telah ditolak!';
                }
            }

            return redirect($redirectUrl)
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $permissions = $this->permissionService->getPermissions();

        if (!$permissions->can_read) {
            return redirect('/dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk melihat detail data.');
        }

        $insiden = Insiden::with('creator', 'section', 'user')->findOrFail($id);

        $userInfo = $this->getUserInfo();
        $currentUrl = request()->path();
        $sections = Section::all();
        $isCreator = Auth::id() == $insiden->created_by;
        $isPIC = strpos($currentUrl, '/pic/insiden') !== false;
        $isSHE = strpos($currentUrl, '/she/insiden') !== false;
        $isManager = strpos($currentUrl, '/manager/insiden') !== false;

        if ($insiden->foto && !is_string($insiden->foto)) {
            $insiden->foto = json_encode($insiden->foto);
        }

        $fotosArray = [];
        if (!empty($insiden->foto)) {
            if (is_string($insiden->foto)) {
                $decoded = json_decode($insiden->foto, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $fotosArray = $decoded;
                } else {
                    $fotosArray = [$insiden->foto];
                }
            } elseif (is_array($insiden->foto)) {
                $fotosArray = $insiden->foto;
            }
        }

        return view('SHE.insiden_view', compact(
            'insiden',
            'permissions',
            'userInfo',
            'sections',
            'isCreator',
            'isPIC',
            'isSHE',
            'isManager',
            'currentUrl',
            'fotosArray'
        ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $permissions = $this->permissionService->getPermissions();

        if (!$permissions->can_delete) {
            return redirect('/dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk menghapus data.');
        }

        $insiden = Insiden::findOrFail($id);

        if (Auth::id() != $insiden->created_by && !Auth::user()->hasRole('admin')) {
            return back()
                ->with('error', 'Anda hanya dapat menghapus laporan yang Anda buat.');
        }

        try {
            $fotos = $this->getFotoArray($insiden->foto);
            foreach ($fotos as $foto) {
                if (Storage::disk('public')->exists($foto)) {
                    Storage::disk('public')->delete($foto);
                }
            }

            $insiden->delete();

            $currentUrl = request()->path();
            $currentUrl = '/' . ltrim($currentUrl, '/');

            $redirectUrl = '/she/insiden';
            if (strpos($currentUrl, '/pic/insiden') !== false) {
                $redirectUrl = '/pic/insiden';
            } elseif (strpos($currentUrl, '/manager/insiden') !== false) {
                $redirectUrl = '/manager/insiden';
            }

            return redirect($redirectUrl)
                ->with('success', 'Data insiden berhasil dihapus!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}