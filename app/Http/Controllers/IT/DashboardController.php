<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Section;

class DashboardController extends Controller
{
    public function index()
    {
        // Summary
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', 1)->count();
        $inactiveUsers = User::where('is_active', 0)->count();

        // Grafik user per section
        $sections = Section::all();
        $sectionLabels = $sections->pluck('section'); // nama section
        $sectionData = $sections->map(fn($section) => $section->users()->count());

        // Top section: section dengan user terbanyak
        $topSection = Section::withCount('users')->orderBy('users_count', 'desc')->first();
        $topSectionName = $topSection->section ?? null;
        $topSectionCount = $topSection->users_count ?? null;

        // Trend chart: jumlah user per bulan
        $trendDataRaw = User::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month');

        $trendLabels = [];
        $trendData = [];
        for($i = 1; $i <= 12; $i++){
            $trendLabels[] = date('M', mktime(0, 0, 0, $i, 1));
            $trendData[] = $trendDataRaw[$i] ?? 0;
        }

        // Top sections table
        $topSections = $sections->map(function($s){
            $active = $s->users()->where('is_active', 1)->count();
            $percent = $s->users()->count() > 0 ? ($active / $s->users()->count() * 100) : 0;
            return [
                'name' => $s->section,
                'users' => $s->users()->count(),
                'activePercent' => $percent
            ];
        });

        return view('IT.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'sectionLabels',
            'sectionData',
            'topSectionName',
            'topSectionCount',
            'trendLabels',
            'trendData',
            'topSections'
        ));
    }
}
