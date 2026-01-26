<?php

namespace App\Http\Controllers\PIC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\KomitmenK3;
use App\Models\Section;
use Exception;
use Carbon\Carbon; // Import Carbon untuk manipulasi tanggal/waktu

class KomitmenK3Controller extends Controller
{
    /**
     * Menampilkan daftar Komitmen K3 untuk satu Section (sesuai user yang login),
     * dilengkapi Filter Bulan & Tahun dan status Sinkronisasi.
     */
    public function index(Request $request)
    {
        $user = Auth::user()->load('section');

        if (!$user) {
            // Ini biasanya redirect ke rute login yang sudah pasti ada
            return redirect()->route('login')->with('error', 'Anda harus login untuk mengakses halaman ini.');
        }

        // --- Inisialisasi Filter Waktu ---
        $bulan = $request->input('bulan', date('n')); // default bulan saat ini (numeric)
        $tahun = $request->input('tahun', date('Y')); // default tahun saat ini

        // Cek status sinkronisasi terakhir untuk Section ini
        $canSync = true;
        $lastSyncDate = null;

        // Mencari data komitmen pertama yang disinkronkan di bulan/tahun yang difilter
        $latestKomitmen = KomitmenK3::whereHas('user', function ($q) use ($user) {
            $q->where('section_id', $user->section_id);
        })
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->latest('created_at')
            ->first();

        if ($latestKomitmen) {
            // Jika ada komitmen di bulan/tahun ini, anggap sudah disinkronisasi
            $lastSyncDate = Carbon::parse($latestKomitmen->created_at)->format('d F Y');
            // Jika bulan dan tahun yang difilter adalah bulan dan tahun sekarang, disable sync
            if ($bulan == date('n') && $tahun == date('Y')) {
                $canSync = false;
            }
        }
        // ------------------------------------

        if (!$user->section_id) {
            $komitmens = collect();
            $userKomitmen = null;
            $isUploaded = false;
            if (is_null($user->section)) {
                $user->section = (object) ['section' => 'N/A', 'department' => 'N/A'];
            }
            return view('PIC.komitmenk3', compact('komitmens', 'user', 'userKomitmen', 'isUploaded', 'canSync', 'lastSyncDate'))
                ->with('warning', 'Akun Anda tidak terasosiasi dengan Section manapun. Data tidak dapat ditampilkan.');
        }

        $sectionId = $user->section_id;

        // 2. Query Komitmen K3 (Filter berdasarkan Section, Bulan, dan Tahun)
        $query = KomitmenK3::query();

        // Filter hanya untuk user di section yang sama
        $query->whereHas('user', function ($q) use ($sectionId) {
            $q->where('section_id', $sectionId);
        });

        // Filter berdasarkan Bulan dan Tahun yang dipilih
        if ($bulan) {
            $query->whereMonth('created_at', $bulan);
        }
        if ($tahun) {
            $query->whereYear('created_at', $tahun);
        }

        // Eager load relasi yang dibutuhkan
        $query->with(['user.section']);

        // --- Handle Search ---
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search, $sectionId) {
                // 1. Cari di kolom komitmen
                $q->where('komitmen', 'like', "%{$search}%");

                // 2. Cari di kolom user (nama atau nip) di section yang sama
                $q->orWhereHas('user', function ($sub) use ($search, $sectionId) {
                    $sub->where('section_id', $sectionId)
                        ->where(function ($subsub) use ($search) {
                            $subsub->where('nama', 'like', "%{$search}%")
                                ->orWhere('nip', 'like', "%{$search}%");
                        });
                });
            });
        }

        // Dapatkan data Komitmen K3 dan paginasi
        $komitmens = $query->latest('updated_at')->paginate(10)->withQueryString();

        // 3. Dapatkan Komitmen K3 milik user yang login (untuk modal upload/edit)
        $userKomitmen = KomitmenK3::where('user_id', $user->id)
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->first();

        $isUploaded = $userKomitmen && $userKomitmen->bukti;

        return view('PIC.komitmenk3', compact('komitmens', 'user', 'userKomitmen', 'isUploaded', 'canSync', 'lastSyncDate'));
    }

    /**
     * Sinkronisasi (Tarik Data) semua user dalam section PIC ke tabel KomitmenK3
     * untuk periode Bulan & Tahun saat ini.
     */
    public function syncUsers(Request $request)
    {
        $user = Auth::user();
        $sectionId = $user->section_id;

        if (!$sectionId) {
            return redirect()->back()->with(['sync_message' => 'Gagal sinkronisasi: Anda tidak terasosiasi dengan Section manapun.', 'sync_status' => 'danger']);
        }

        $bulanSaatIni = date('n');
        $tahunSaatIni = date('Y');

        // Cek apakah sinkronisasi sudah dilakukan untuk bulan ini (jika ada data komitmen)
        $isSynced = KomitmenK3::whereHas('user', function ($q) use ($sectionId) {
            $q->where('section_id', $sectionId);
        })
            ->whereYear('created_at', $tahunSaatIni)
            ->whereMonth('created_at', $bulanSaatIni)
            ->exists();

        if ($isSynced) {
            return redirect()->back()->with(['sync_message' => 'Sinkronisasi untuk bulan ini sudah dilakukan sebelumnya.', 'sync_status' => 'warning']);
        }

        try {
            DB::beginTransaction();

            // 1. Ambil semua User dalam Section PIC yang login
            $usersToSync = User::where('section_id', $sectionId)->get();

            $syncedCount = 0;
            $now = Carbon::now();

            // 2. Masukkan (atau Update) entri KomitmenK3 untuk setiap user
            foreach ($usersToSync as $u) {
                // Cek apakah user sudah punya entri komitmen di bulan/tahun ini
                $komitmen = KomitmenK3::firstOrNew([
                    'user_id' => $u->id,
                ]);

                // Hanya proses jika belum ada di bulan ini 
                if (
                    !$komitmen->exists ||
                    Carbon::parse($komitmen->created_at)->month != $bulanSaatIni ||
                    Carbon::parse($komitmen->created_at)->year != $tahunSaatIni
                ) {
                    // Buat entri baru untuk periode ini
                    KomitmenK3::create([
                        'user_id' => $u->id,
                        'komitmen' => null,
                        'bukti' => null,
                        'status' => 'Belum Upload',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                    $syncedCount++;
                }
            }

            DB::commit();
            // ⭐ PENGGANTIAN route() ke url() ⭐
            return redirect(url('/pic/komitmenk3'))
                ->with(['sync_message' => "Berhasil menarik data. $syncedCount karyawan dari section Anda sudah dimasukkan ke daftar Komitmen K3.", 'sync_status' => 'success']);

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['sync_message' => 'Gagal melakukan sinkronisasi: ' . $e->getMessage(), 'sync_status' => 'danger']);
        }
    }

    /**
     * Menyimpan Komitmen K3 baru (Diizinkan jika belum ada entry untuk periode saat ini).
     */
    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'user_id' => 'required|exists:tb_user,id',
            'komitmen' => 'required|string|max:1000',
            'bukti' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Cek apakah sudah ada komitmen untuk user ini di bulan dan tahun saat ini
        $existingKomitmen = KomitmenK3::where('user_id', $request->user_id)
            ->whereMonth('created_at', date('n'))
            ->whereYear('created_at', date('Y'))
            ->first();

        if ($existingKomitmen) {
            // Jika sudah ada entri, alihkan ke update
            return $this->update($request, $existingKomitmen->id);
        }

        try {
            DB::beginTransaction();

            // 1. Upload File Bukti
            $path = $this->uploadBuktiK3($request->file('bukti'), $request->user_id);

            // 2. Simpan Data ke tb_komitment_k3
            KomitmenK3::create([
                'user_id' => $request->user_id,
                'komitmen' => $request->komitmen,
                'bukti' => $path,
                'status' => 'Sudah Upload',
                'created_at' => Carbon::now(), // Penting: Set created_at ke saat ini
            ]);

            DB::commit();
            // ⭐ PENGGANTIAN route() ke url() ⭐
            return redirect(url('/pic/komitmenk3'))->with('success', 'Komitmen K3 berhasil diupload!');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan komitmen K3: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Memperbarui Komitmen K3 yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $komitmen = KomitmenK3::findOrFail($id);

        $user = Auth::user();

        // Ambil user pemilik komitmen
        $targetUser = $komitmen->user;

        // Validasi: harus satu section
        if (!$targetUser || $targetUser->section_id !== $user->section_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengedit data ini.');
        }

        $rules = [
            'komitmen' => 'required|string|max:1000',
            'bukti' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $komitmen->komitmen = $request->komitmen;

            // 1. Handle Upload Bukti (jika ada file baru)
            if ($request->hasFile('bukti')) {
                // Hapus bukti lama (jika ada)
                if ($komitmen->bukti) {
                    $this->deleteBuktiK3($komitmen->bukti);
                }

                $path = $this->uploadBuktiK3($request->file('bukti'), $komitmen->user_id);
                $komitmen->bukti = $path;
                $komitmen->status = 'Sudah Upload';
            }

            $komitmen->save();
            DB::commit();

            // ⭐ PENGGANTIAN route() ke url() ⭐
            return redirect(url('/pic/komitmenk3'))->with('success', 'Komitmen K3 berhasil diperbarui!');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui komitmen K3: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->section_id) {
            return redirect()->back()->with('error', 'Gagal export: Anda tidak terasosiasi dengan Section manapun.');
        }

        $sectionId = $user->section_id;

        // Ensure month and year are valid
        $bulan = $request->input('bulan');
        if (empty($bulan))
            $bulan = date('n');

        $tahun = $request->input('tahun');
        if (empty($tahun))
            $tahun = date('Y');

        $search = $request->input('search');

        $sectionName = $user->section->section ?? 'All';
        $bulanName = date('F', mktime(0, 0, 0, $bulan ?? 1, 10)); // Safe fallback
        $filename = "komitmen_k3_{$sectionName}_{$bulanName}_{$tahun}_" . date('Ymd_His') . ".xlsx";

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\KomitmenK3Export($sectionId, $bulan, $tahun, $search),
            $filename
        );
    }

    public function exportSHE(Request $request)
    {
        // For SHE, sectionId can be null (all) or from request
        $sectionId = $request->input('section_id');
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));
        $search = $request->input('search');

        if ($request->input('monthYear')) {
            try {
                [$tahun, $bulan] = explode('-', $request->input('monthYear'));
            } catch (\Exception $e) {
                // Keep default
            }
        }

        $sectionName = "All_Sections";
        if ($sectionId) {
            $section = \App\Models\Section::find($sectionId);
            if ($section)
                $sectionName = str_replace(' ', '_', $section->section);
        }

        $bulanName = date('F', mktime(0, 0, 0, (int) $bulan, 10));
        $filename = "monitoring_komitmen_k3_{$sectionName}_{$bulanName}_{$tahun}_" . date('Ymd_His') . ".xlsx";

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\KomitmenK3Export($sectionId, $bulan, $tahun, $search),
            $filename
        );
    }


    public function getlaporank3(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $monthYear = $request->input('monthYear');
        $search = $request->input('search');

        if ($monthYear) {
            try {
                [$tahun, $bulan] = explode('-', $monthYear);
            } catch (\Exception $e) {
                $bulan = date('m');
                $tahun = date('Y');
            }
        }

        // 🎯 TARGET PERSENTASE
        $targetPersen = 70;

        $query = KomitmenK3::with(['user.section'])
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan);

        if ($search) {
            $query->whereHas('user.section', function ($q) use ($search) {
                $q->where('section', 'like', "%{$search}%");
            });
        }

        $dataK3 = $query->get();

        $summary = $dataK3
            ->groupBy(fn($item) => optional($item->user->section)->section ?? 'Unknown')
            ->map(function ($group, $sectionName) use ($bulan, $tahun, $targetPersen) {

                $sectionId = optional($group->first()->user->section)->id;

                // ✅ TARGET = USER TERDAFTAR DI tb_komitment_k3 (DISTINCT)
                $totalUserTarget = KomitmenK3::whereYear('created_at', $tahun)
                    ->whereMonth('created_at', $bulan)
                    ->whereHas('user', function ($q) use ($sectionId) {
                    $q->where('section_id', $sectionId);
                })
                    ->distinct('user_id')
                    ->count('user_id');

                // ✅ AKTUAL = USER SUDAH UPLOAD
                $totalUserAktual = $group
                    ->where('status', 'Sudah Upload')
                    ->unique('user_id')
                    ->count();

                $persentaseAktual = $totalUserTarget > 0
                    ? round(($totalUserAktual / $totalUserTarget) * 100, 1)
                    : 0;

                return [
                    'section_id' => $sectionId,
                    'section_name' => $sectionName,
                    'periode' => Carbon::create($tahun, $bulan, 1)->format('F Y'),
                    'total_user_target' => $totalUserTarget,
                    'total_user_aktual' => $totalUserAktual,
                    'persentase_aktual' => $persentaseAktual,
                    'target_persen' => $targetPersen,
                    'status_target' => $persentaseAktual >= $targetPersen ? 'Tercapai' : 'Belum Tercapai',
                    'total_records' => $group->count(),
                ];
            })
            ->sortBy('section_name');

        $totalSummary = [
            'total_target' => $summary->sum('total_user_target'),
            'total_aktual' => $summary->sum('total_user_aktual'),
            'total_persentase' => $summary->sum('total_user_target') > 0
                ? round(($summary->sum('total_user_aktual') / $summary->sum('total_user_target')) * 100, 1)
                : 0,
            'total_sections' => $summary->count(),
            'periode' => Carbon::create($tahun, $bulan, 1)->format('F Y'),
        ];

        return view('SHE.komitmenk3', compact(
            'summary',
            'totalSummary',
            'bulan',
            'tahun',
            'monthYear',
            'search',
            'targetPersen'
        ));
    }




    public function getSectionDetail($sectionId, Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $monthYear = $request->monthYear;
        $search = $request->search;

        if ($monthYear) {
            list($tahun, $bulan) = explode('-', $monthYear);
        }

        $sectionUser = User::where('section_id', $sectionId)->first();
        $sectionName = $sectionUser ? ($sectionUser->section ?? 'Section ' . $sectionId) : 'Section ' . $sectionId;

        $query = KomitmenK3::query();

        $query->whereHas('user', function ($q) use ($sectionId) {
            $q->where('section_id', $sectionId);
        });

        if ($bulan) {
            $query->whereMonth('created_at', $bulan);
        }
        if ($tahun) {
            $query->whereYear('created_at', $tahun);
        }

        $query->with(['user']);

        if ($search) {
            $query->where(function ($q) use ($search, $sectionId) {
                $q->where('komitmen', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($sub) use ($search, $sectionId) {
                        $sub->where('section_id', $sectionId)
                            ->where(function ($subsub) use ($search) {
                                $subsub->where('nama', 'like', "%{$search}%")
                                    ->orWhere('nip', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $komitmens = $query->latest('updated_at')->paginate(10)->withQueryString();

        // ✅ TARGET = SEMUA USER YANG TERDAFTAR DI PERIODE INI (SESUAI LOGIKA SUMMARY)
        $totalUserTarget = KomitmenK3::whereHas('user', function ($q) use ($sectionId, $search) {
            $q->where('section_id', $sectionId);
            if ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                });
            }
        })
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->distinct('user_id')
            ->count('user_id');

        // ✅ AKTUAL = USER YANG SUDAH UPLOAD
        $totalUserAktualQuery = KomitmenK3::whereHas('user', function ($q) use ($sectionId, $search) {
            $q->where('section_id', $sectionId);
            if ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                });
            }
        })
            ->where('status', 'Sudah Upload');

        if ($bulan) {
            $totalUserAktualQuery->whereMonth('created_at', $bulan);
        }
        if ($tahun) {
            $totalUserAktualQuery->whereYear('created_at', $tahun);
        }

        $totalUserAktual = $totalUserAktualQuery->distinct('user_id')->count('user_id');

        $persentaseAktual = $totalUserTarget > 0
            ? round(($totalUserAktual / $totalUserTarget) * 100, 2)
            : 0;

        return view('SHE.komitmenk3_detail', [
            'sectionId' => $sectionId,
            'sectionName' => $sectionName,
            'komitmens' => $komitmens,
            'totalUserTarget' => $totalUserTarget,
            'totalUserAktual' => $totalUserAktual,
            'persentaseAktual' => $persentaseAktual,
            'filter' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'monthYear' => $monthYear,
                'search' => $search
            ]
        ]);
    }

    private function uploadBuktiK3($file, $userId)
    {
        // Gunakan Storage facade agar konsisten dengan filesystems.php
        $filename = 'k3_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Simpan ke storage/app/public/komitmen_k3_bukti
        $path = $file->storeAs('komitmen_k3_bukti', $filename, 'public');

        return $path;
    }

    private function deleteBuktiK3($path)
    {
        if (!$path)
            return;

        $fullPath = public_path('storage/' . $path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }




}