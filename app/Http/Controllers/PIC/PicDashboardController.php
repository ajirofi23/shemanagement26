<?php

namespace App\Http\Controllers\PIC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Insiden;
use App\Models\Section;
use App\Models\HyariHatto;
use App\Models\KomitmenK3;
use App\Models\SafetyPatrol;
use App\Models\SafetyRiding;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PicDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $sectionId = $user->section_id;

        // ❌ PIC TIDAK PERLU DROPDOWN SEMUA SECTION
        $sections = Section::where('id', $sectionId)->get();

        // Inject section ke request
        $request->merge([
            'section' => $sectionId
        ]);

        $dashboardData = $this->getDashboardDataWithFilters($request);

        return view('PIC.dashboard', compact('dashboardData', 'sections'));


    }

    /**
     * Mendapatkan data dashboard dengan filter
     */
    private function getDashboardDataWithFilters(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $user = auth()->user();

        // 🔒 FORCE section dari session (ANTI RESET)
        $request->merge([
            'section' => auth()->user()->section_id
        ]);

        // 🔒 PAKSA SECTION DARI USER LOGIN
        $sectionId = $user->section_id;

        $status = $request->input('status');

        // Hitung data dengan filter
        $totalSafetyDays = $this->calculateSafetyWorkDaysWithResetLogic($startDate, $endDate, $sectionId, $status);
        $incidentCounts = $this->getIncidentCountsByCategory($startDate, $endDate, $sectionId, $status);

        // Get summary data for additional modules
        $hyariHattoSummary = $this->getHyariHattoSummary($startDate, $endDate, $sectionId);
        $komitmenK3Summary = $this->getKomitmenK3Summary($sectionId);
        $safetyPatrolSummary = $this->getSafetyPatrolSummary($startDate, $endDate, $sectionId);
        $safetyRidingSummary = $this->getSafetyRidingSummary($startDate, $endDate, $sectionId);
        $programSafetySummary = $this->getProgramSafetySummary($startDate, $endDate, $sectionId);

        return [
            'total_safety_days' => $totalSafetyDays,
            'incident_counts' => $incidentCounts,
            'last_reset_date' => $this->getLastResetDate($startDate, $endDate, $sectionId, $status),
            'current_streak_days' => $this->getCurrentStreakDays($startDate, $endDate, $sectionId, $status),
            'incident_details' => $this->getRecentIncidents($startDate, $endDate, $sectionId, $status),
            'today_has_loss_day' => $this->checkTodayHasLossDay($startDate, $endDate, $sectionId, $status),
            'hyari_hatto' => $hyariHattoSummary,
            'komitmen_k3' => $komitmenK3Summary,
            'safety_patrol' => $safetyPatrolSummary,
            'safety_riding' => $safetyRidingSummary,
            'program_safety' => $programSafetySummary,
            'filter_info' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'section_id' => $sectionId,
                'status' => $status
            ]
        ];
    }

    /**
     * Menghitung total safety work day dengan logika reset jika ada Work Accident (Loss day)
     * Rumus: Tanggal Hari Ini - Tanggal Terakhir Loss Day (sebelum hari ini) - 1
     * Jika hari ini ada loss day, reset baru terjadi BESOK (bukan hari ini)
     * Contoh: Jika hari ini 29 Des dan loss day terakhir sebelum hari ini adalah 19 Des:
     *         diffInDays = 10, dikurangi 1 = 9 hari
     */
    private function calculateSafetyWorkDaysWithResetLogic($startDate = null, $endDate = null, $sectionId = null, $status = null)
    {
        try {
            // Gunakan timezone Asia/Jakarta untuk memastikan tanggal hari ini sesuai waktu lokal
            $today = Carbon::now('Asia/Jakarta')->startOfDay();

            // Query langsung ke database untuk menghindari issue SoftDeletes
            // PENTING: Hanya ambil loss day SEBELUM hari ini (tidak termasuk hari ini)
            // Reset baru terjadi keesokan harinya
            $query = DB::table('tb_insiden')
                ->where('kategori', 'Work Accident')
                ->where('work_accident_type', 'Loss Day')
                ->whereNull('deleted_at')
                ->where('tanggal', '<', $today->format('Y-m-d')); // Hanya sebelum hari ini

            // Filter section jika ada
            if ($sectionId) {
                $query->where('section_id', $sectionId);
            }

            // Ambil tanggal loss day terakhir (sebelum hari ini)
            $lastLossDay = $query->orderBy('tanggal', 'desc')->first();

            // Jika tidak ada loss day sebelum hari ini, hitung dari awal
            if (!$lastLossDay) {
                return $this->calculateDaysWithoutLossDay($startDate, $endDate, $sectionId, $status);
            }

            // Parse tanggal loss day terakhir dengan timezone Asia/Jakarta
            $lastLossDayDate = Carbon::parse($lastLossDay->tanggal, 'Asia/Jakarta')->startOfDay();

            // Jika loss day di masa depan (seharusnya tidak terjadi karena sudah difilter)
            if ($lastLossDayDate->isAfter($today)) {
                return 0;
            }

            // Rumus: diffInDays - 1
            // Contoh: 19 Des -> 29 Des = 10 hari, dikurangi 1 = 9 hari
            // Gunakan intval() untuk memastikan hasil integer
            $daysDiff = intval($lastLossDayDate->diffInDays($today));
            $safetyDays = $daysDiff - 1;

            \Log::info('Safety Work Day Calculation:', [
                'today' => $today->format('Y-m-d'),
                'last_loss_day_before_today' => $lastLossDayDate->format('Y-m-d'),
                'days_diff' => $daysDiff,
                'safety_days' => $safetyDays
            ]);

            // Pastikan tidak negatif
            return max(0, $safetyDays);

        } catch (\Exception $e) {
            \Log::error('Error calculating safety work days: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mendapatkan jumlah insiden per kategori dengan detail type
     * dengan filter tambahan
     */
    private function getIncidentCountsByCategory($startDate = null, $endDate = null, $sectionId = null, $status = null)
    {
        // Query dasar
        $query = Insiden::query();

        // Terapkan filter tanggal jika ada
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } else {
            // Default 30 hari terakhir jika tidak ada filter
            $thirtyDaysAgo = Carbon::now()->subDays(30)->format('Y-m-d');
            $query->whereDate('tanggal', '>=', $thirtyDaysAgo);
        }

        // Filter section
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        // Filter status
        if ($status) {
            $query->where('status', $status);
        } else {
            // Default: ambil semua status kecuali yang ditolak/dihapus (jika ada logika reject)
            // Jika ingin semua termasuk open, progress, closed:
            // $query->whereIn('status', ['open', 'progress', 'closed']);
            // Atau cukup ambil semua yang aktif
            $query->where('status', '!=', 'rejected');
        }

        // Ambil semua data sesuai filter
        $allIncidents = $query->get();

        // Inisialisasi array untuk hasil
        $counts = [
            'Work Accident (Loss day)' => 0,
            'Work Accident (Light)' => 0,
            'Traffic Accident' => 0,
            'Fire Accident' => 0,
            'Forklift Accident' => 0,
            'Molten Spill Incident' => 0,
            'Property Damage Incident' => 0
        ];

        foreach ($allIncidents as $incident) {
            if ($incident->kategori === 'Work Accident') {
                if ($incident->work_accident_type === 'Loss Day') {
                    $counts['Work Accident (Loss day)']++;
                } else {
                    $counts['Work Accident (Light)']++;
                }
            } elseif (isset($counts[$incident->kategori])) {
                $counts[$incident->kategori]++;
            }
        }

        return $counts;
    }

    /**
     * Mendapatkan tanggal terakhir reset (tanggal loss day terakhir)
     * Tanpa batasan filter tanggal (global search) agar selalu akurat
     */
    private function getLastResetDate($startDate = null, $endDate = null, $sectionId = null, $status = null)
    {
        // Query global untuk loss day terakhir
        // Gunakan DB::table untuk menghindari soft delete issue
        $query = DB::table('tb_insiden')
            ->where('kategori', 'Work Accident')
            ->where('work_accident_type', 'Loss Day')
            ->whereNull('deleted_at');

        // Filter section TETAP diproses karena reset day spesifik per section jika difilter
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        // Filter status dihapus atau disesuaikan
        // Kita ingin loss day terakhir yang VALID, jadi mungkin exclude rejected
        if ($status) {
            $query->where('status', $status);
        } else {
            $query->where('status', '!=', 'rejected');
        }

        // Ambil yang paling baru secara historis (TANPA filter tanggal dashboard)
        $lastLossDay = $query->orderBy('tanggal', 'desc')->first();

        return $lastLossDay ? Carbon::parse($lastLossDay->tanggal)->format('d M Y') : 'No reset yet';
    }

    /**
     * Mendapatkan streak hari berjalan saat ini
     * dengan filter tambahan
     */
    private function getCurrentStreakDays($startDate = null, $endDate = null, $sectionId = null, $status = null)
    {
        $query = Insiden::where('kategori', 'Work Accident')
            ->where('work_accident_type', 'Loss Day');

        // Terapkan filter tanggal jika ada
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // Filter section
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        // Filter status
        if ($status) {
            $query->where('status', $status);
        } else {
            $query->whereIn('status', ['progress', 'closed']);
        }

        $lastLossDay = $query->orderBy('tanggal', 'desc')
            ->first();

        if (!$lastLossDay) {
            return $this->calculateDaysWithoutLossDay($startDate, $endDate, $sectionId, $status);
        }

        $lastLossDate = Carbon::parse($lastLossDay->tanggal);

        // Gunakan tanggal akhir filter sebagai batas atas
        $maxDate = $endDate ? Carbon::parse($endDate)->startOfDay() : Carbon::now()->startOfDay();

        if ($lastLossDate->format('Y-m-d') === $maxDate->format('Y-m-d')) {
            return 0;
        }

        $streakDays = 0;
        $currentDate = $lastLossDate->copy()->addDay();

        while ($currentDate->lte($maxDate)) {
            if ($this->isWorkDay($currentDate)) {
                $streakDays++;
            }
            $currentDate->addDay();
        }

        return $streakDays;
    }

    /**
     * Mendapatkan insiden terbaru untuk detail
     * dengan filter tambahan
     */
    private function getRecentIncidents($startDate = null, $endDate = null, $sectionId = null, $status = null)
    {
        $query = Insiden::with('section');

        // Terapkan filter tanggal jika ada
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // Filter section
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        // Filter status
        if ($status) {
            $query->where('status', $status);
        } else {
            $query->whereIn('status', ['progress', 'closed']);
        }

        $recentIncidents = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($incident) {
                $displayCategory = $incident->kategori;
                if ($incident->kategori === 'Work Accident') {
                    if ($incident->work_accident_type === 'Loss Day') {
                        $displayCategory = 'Work Accident (Loss day)';
                    } else {
                        $displayCategory = 'Work Accident (Light)';
                    }
                }

                return [
                    'tanggal' => Carbon::parse($incident->tanggal)->format('d/m/Y'),
                    'jam' => $incident->jam ? date('H:i', strtotime($incident->jam)) : '-',
                    'lokasi' => $incident->lokasi,
                    'kategori' => $displayCategory,
                    'kategori_asli' => $incident->kategori,
                    'work_accident_type' => $incident->work_accident_type,
                    'departemen' => $incident->departemen,
                    'kondisi_luka' => $incident->kondisi_luka,
                    'status' => $incident->status,
                    'section_name' => $incident->section->section ?? '-'
                ];
            });

        return $recentIncidents;
    }

    /**
     * Menghitung hari tanpa loss day (jika belum pernah ada loss day)
     * dengan filter tambahan
     */
    private function calculateDaysWithoutLossDay($startDate = null, $endDate = null, $sectionId = null, $status = null)
    {
        try {
            // Cari tanggal pertama insiden dalam filter
            $query = Insiden::query();

            // Terapkan filter tanggal jika ada
            if ($startDate) {
                $query->where('tanggal', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('tanggal', '<=', $endDate);
            }

            // Filter section
            if ($sectionId) {
                $query->where('section_id', $sectionId);
            }

            // Filter status
            if ($status) {
                $query->where('status', $status);
            } else {
                $query->whereIn('status', ['progress', 'closed']);
            }

            $firstIncidentInFilter = $query->orderBy('tanggal', 'asc')->first();

            if ($firstIncidentInFilter) {
                $startDate = Carbon::parse($firstIncidentInFilter->tanggal);
            } else {
                // Jika tidak ada data dalam filter, gunakan tanggal filter atau default
                $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subYear();
            }

            $maxDate = $endDate ? Carbon::parse($endDate)->startOfDay() : Carbon::now()->startOfDay();
            $safetyDays = 0;
            $currentDate = $startDate->copy()->startOfDay();

            // Hitung semua hari kerja dalam periode
            while ($currentDate->lte($maxDate)) {
                if ($this->isWorkDay($currentDate)) {
                    $safetyDays++;
                }
                $currentDate->addDay();
            }

            return $safetyDays;

        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Cek apakah hari ini ada loss day
     * dengan filter tambahan
     */
    private function checkTodayHasLossDay($startDate = null, $endDate = null, $sectionId = null, $status = null)
    {
        $today = Carbon::now()->format('Y-m-d');

        $query = Insiden::whereDate('tanggal', $today)
            ->where('kategori', 'Work Accident')
            ->where('work_accident_type', 'Loss Day');

        // Filter section
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        // Filter status
        if ($status) {
            $query->where('status', $status);
        } else {
            $query->whereIn('status', ['progress', 'closed']);
        }

        return $query->exists();
    }

    /**
     * Cek apakah hari kerja (Senin-Jumat)
     */
    private function isWorkDay(Carbon $date)
    {
        return $date->dayOfWeek >= 1 && $date->dayOfWeek <= 5;
    }

    /**
     * API untuk mendapatkan data dashboard dengan filter
     */
    public function getDashboardData(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $sectionId = auth()->user()->section_id;
        $status = $request->input('status');

        $data = [
            'total_safety_days' => $this->calculateSafetyWorkDaysWithResetLogic($startDate, $endDate, $sectionId, $status),
            'incident_counts' => $this->getIncidentCountsByCategory($startDate, $endDate, $sectionId, $status),
            'last_reset_date' => $this->getLastResetDate($startDate, $endDate, $sectionId, $status),
            'current_streak_days' => $this->getCurrentStreakDays($startDate, $endDate, $sectionId, $status),
            'incident_details' => $this->getRecentIncidents($startDate, $endDate, $sectionId, $status),
            'today_has_loss_day' => $this->checkTodayHasLossDay($startDate, $endDate, $sectionId, $status),
            'hyari_hatto' => $this->getHyariHattoSummary($startDate, $endDate, $sectionId),
            'komitmen_k3' => $this->getKomitmenK3Summary($sectionId),
            'safety_patrol' => $this->getSafetyPatrolSummary($startDate, $endDate, $sectionId),
            'safety_riding' => $this->getSafetyRidingSummary($startDate, $endDate, $sectionId),
            'program_safety' => $this->getProgramSafetySummary($startDate, $endDate, $sectionId),
            'filter_info' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'section_id' => $sectionId,
                'status' => $status
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'updated_at' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Mendapatkan detail insiden berdasarkan kategori dengan filter
     */
    public function getIncidentDetails(Request $request, $category)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $status = $request->input('status');

        // 🔒 PAKSA SECTION DARI USER LOGIN
        $sectionId = auth()->user()->section_id;

        $query = Insiden::query();

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $query->where('section_id', $sectionId);

        if ($status) {
            $query->where('status', $status);
        } else {
            $query->whereIn('status', ['progress', 'closed']);
        }

        // Filter berdasarkan kategori yang dipilih
        switch ($category) {
            case 'work-accident-loss-day':
                $query->where('kategori', 'Work Accident')
                    ->where('work_accident_type', 'Loss Day');
                break;

            case 'work-accident-light':
                $query->where('kategori', 'Work Accident')
                    ->where('work_accident_type', '!=', 'Loss Day')
                    ->whereNotNull('work_accident_type');
                break;

            case 'traffic-accident':
                $query->where('kategori', 'Traffic Accident');
                break;

            case 'fire-accident':
                $query->where('kategori', 'Fire Accident');
                break;

            case 'forklift-accident':
                $query->where('kategori', 'Forklift Accident');
                break;

            case 'molten-spill-incident':
                $query->where('kategori', 'Molten Spill Incident');
                break;

            case 'property-damage-incident':
                $query->where('kategori', 'Property Damage Incident');
                break;
        }

        $incidents = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->get()
            ->map(function ($incident) {
                return [
                    'id' => $incident->id,
                    'tanggal' => Carbon::parse($incident->tanggal)->format('d/m/Y'),
                    'jam' => $incident->jam ? date('H:i', strtotime($incident->jam)) : '-',
                    'lokasi' => $incident->lokasi,
                    'kategori' => $incident->kategori,
                    'work_accident_type' => $incident->work_accident_type,
                    'departemen' => $incident->departemen,
                    'kondisi_luka' => $incident->kondisi_luka,
                    'kronologi' => $incident->kronologi,
                    'status' => $incident->status,
                    'section_name' => $incident->section->section ?? '-'
                ];
            });

        return response()->json([
            'success' => true,
            'category' => $category,
            'incidents' => $incidents,
            'count' => $incidents->count(),
            'filter_info' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'section_id' => $sectionId,
                'status' => $status
            ]
        ]);
    }

    /**
     * Method untuk debug data Work Accident dengan filter
     */
    public function debugWorkAccidentData(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $sectionId = auth()->user()->section_id;
        $status = $request->input('status');

        $query = Insiden::query();

        // Terapkan filter tanggal
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // Filter section
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        // Filter status
        if ($status) {
            $query->where('status', $status);
        }

        $workAccidents = $query->where('kategori', 'Work Accident')
            ->select('id', 'tanggal', 'kategori', 'work_accident_type', 'status', 'section_id')
            ->orderBy('tanggal', 'desc')
            ->get();

        // Kelompokkan berdasarkan type
        $groupedByType = $workAccidents->groupBy('work_accident_type');

        $result = [
            'success' => true,
            'total_work_accident' => $workAccidents->count(),
            'types_found' => $groupedByType->map(function ($items, $type) {
                return [
                    'type' => $type ?? '(NULL)',
                    'count' => $items->count(),
                    'items' => $items->take(5)->toArray()
                ];
            })->toArray(),
            'all_data' => $workAccidents->take(20)->toArray(),
            'filter_info' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'section_id' => $sectionId,
                'status' => $status
            ]
        ];

        return response()->json($result);
    }

    /**
     * Export data dashboard ke Excel
     */
    public function exportDashboardData(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $sectionId = auth()->user()->section_id;
        $status = $request->input('status');

        $data = $this->getDashboardDataWithFilters($request);

        // Kode untuk export Excel (gunakan package seperti Maatwebsite/Laravel-Excel)
        // return Excel::download(new DashboardExport($data), 'dashboard-data.xlsx');

        return response()->json([
            'success' => true,
            'message' => 'Export feature to be implemented',
            'data' => $data
        ]);
    }

    /**
     * Get Hyari Hatto Summary (Open & Closed based on rekomendasi field)
     * If rekomendasi is filled, it's considered "closed", otherwise "open"
     */
    private function getHyariHattoSummary($startDate = null, $endDate = null, $sectionId = null)
    {
        $query = HyariHatto::query();

        // Filter by date range (using created_at since HyariHatto doesn't have tanggal field)
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        // Filter by section
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $total = $query->count();

        // Closed = has rekomendasi filled
        $closed = (clone $query)->whereNotNull('rekomendasi')->where('rekomendasi', '!=', '')->count();

        // Open = rekomendasi is null or empty
        $open = $total - $closed;

        return [
            'total' => $total,
            'open' => $open,
            'closed' => $closed,
            'percentage_closed' => $total > 0 ? round(($closed / $total) * 100, 1) : 0
        ];
    }

    /**
     * Get Komitmen K3 Summary (Percentage of users who have uploaded)
     */
    private function getKomitmenK3Summary($sectionId)
    {
        $bulan = date('m');
        $tahun = date('Y');

        $usersQuery = User::where('level', '!=', 'Admin')
            ->where('is_active', 1)
            ->where('section_id', $sectionId);

        $totalUsers = $usersQuery->count();

        $sudahUpload = KomitmenK3::whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->whereHas('user', fn($q) => $q->where('section_id', $sectionId))
            ->where('status', 'Sudah Upload')
            ->distinct('user_id')
            ->count('user_id');

        $belumUpload = max($totalUsers - $sudahUpload, 0);

        return [
            'total_users' => $totalUsers,
            'sudah_upload' => $sudahUpload,
            'belum_upload' => $belumUpload,
            'percentage' => $totalUsers > 0
                ? round(($sudahUpload / $totalUsers) * 100, 1)
                : 0
        ];
    }







    /**
     * Get Komitmen K3 Summary (Percentage of users who have uploaded)
     */
    // private function getKomitmenK3Summary($sectionId = null)
    // {
    //     // Get total users (target) - exclude admin users using 'level' column
    //     $userQuery = User::where('level', '!=', 'Admin')
    //         ->where('is_active', 1); // Only active users

    //     if ($sectionId) {
    //         $userQuery->where('section_id', $sectionId);
    //     }

    //     $totalUsers = $userQuery->count();

    //     // Get users who have already submitted Komitmen K3
    //     $komitmenQuery = KomitmenK3::query();

    //     if ($sectionId) {
    //         $komitmenQuery->whereHas('user', function ($q) use ($sectionId) {
    //             $q->where('section_id', $sectionId);
    //         });
    //     }

    //     $totalKomitmen = $komitmenQuery->count();
    //     $sudahUpload = (clone $komitmenQuery)->where('status', 'Sudah Upload')->count();
    //     $belumUpload = $totalUsers - $sudahUpload;

    //     return [
    //         'total_users' => $totalUsers,
    //         'total_komitmen' => $totalKomitmen,
    //         'sudah_upload' => $sudahUpload,
    //         'belum_upload' => $belumUpload > 0 ? $belumUpload : 0,
    //         'percentage' => $totalUsers > 0 ? round(($sudahUpload / $totalUsers) * 100, 1) : 0
    //     ];
    // }

    /**
     * Get Safety Patrol Summary
     */
    private function getSafetyPatrolSummary($startDate = null, $endDate = null, $sectionId = null)
    {
        $query = SafetyPatrol::query();

        // Filter by date range
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // Filter by section
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $total = $query->count();
        $open = (clone $query)->where('status', 'Open')->count();
        $progress = (clone $query)->where('status', 'Progress')->count();
        $closed = (clone $query)->where('status', 'Close')->count();
        $rejected = (clone $query)->where('status', 'Rejected')->count();

        return [
            'total' => $total,
            'open' => $open,
            'progress' => $progress,
            'closed' => $closed,
            'rejected' => $rejected,
            'percentage_closed' => $total > 0 ? round(($closed / $total) * 100, 1) : 0
        ];
    }

    /**
     * Get Safety Riding Summary
     */
    private function getSafetyRidingSummary($startDate = null, $endDate = null, $sectionId = null)
    {
        $query = SafetyRiding::query();

        // Filter by date range
        if ($startDate && $endDate) {
            $query->whereBetween('waktu_kejadian', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        // Filter by section
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $total = $query->count();
        $open = (clone $query)->where('status', 'Open')->count();
        $progress = (clone $query)->where('status', 'Progress')->count();
        $closed = (clone $query)->where('status', 'Close')->count();

        // Count total violations
        $totalViolations = (clone $query)->sum('total_pelanggaran');

        return [
            'total' => $total,
            'open' => $open,
            'progress' => $progress,
            'closed' => $closed,
            'total_violations' => $totalViolations,
            'percentage_closed' => $total > 0 ? round(($closed / $total) * 100, 1) : 0
        ];
    }

    /**
     * Get Program Safety Summary (combines all safety-related activities)
     */
    private function getProgramSafetySummary($startDate = null, $endDate = null, $sectionId = null)
    {
        $baseQuery = DB::table('tb_programsafety');

        if ($sectionId) {
            $baseQuery->where('section_id', $sectionId);
        }

        if ($startDate && $endDate) {
            $baseQuery->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        }

        $totalActivities = (clone $baseQuery)->count();

        $totalOpen = (clone $baseQuery)
            ->whereRaw('LOWER(status) = ?', ['open'])
            ->count();

        $totalOnProgress = (clone $baseQuery)
            ->whereRaw('LOWER(status) = ?', ['on progress'])
            ->count();

        $totalClosed = (clone $baseQuery)
            ->whereRaw('LOWER(status) = ?', ['closed'])
            ->count();

        return [
            'total_activities' => $totalActivities,
            'total_completed' => $totalClosed,
            'total_pending' => $totalOpen + $totalOnProgress,
            'completion_rate' => $totalActivities > 0
                ? round(($totalClosed / $totalActivities) * 100, 1)
                : 0
        ];
    }
}