<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PermissionService
{
    public function getPermissions($url = null)
    {
        $userId = Auth::id();
        if (!$userId) {
            return $this->defaultPermissions();
        }

        $currentUrl = $url ?: request()->path();
        $currentUrl = '/' . ltrim($currentUrl, '/');
        
        $menuUrl = $this->normalizeUrl($currentUrl);
        
        // Cache untuk performa (opsional)
        $cacheKey = "user_permissions_{$userId}_{$menuUrl}";
        
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($userId, $menuUrl) {
            $permission = DB::table('tb_user_permissions as up')
                ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
                ->where('up.user_id', $userId)
                ->where('m.url', $menuUrl)
                ->select('up.can_read', 'up.can_add', 'up.can_edit', 'up.can_delete')
                ->first();

            if (!$permission) {
                return $this->defaultPermissions();
            }

            return (object)[
                'can_read'   => (bool)($permission->can_read ?? false),
                'can_add'    => (bool)($permission->can_add ?? false),
                'can_edit'   => (bool)($permission->can_edit ?? false),
                'can_delete' => (bool)($permission->can_delete ?? false)
            ];
        });
    }

    public function canRead($url = null): bool
    {
        return $this->getPermissions($url)->can_read;
    }

    public function canAdd($url = null): bool
    {
        return $this->getPermissions($url)->can_add;
    }

    public function canEdit($url = null): bool
    {
        return $this->getPermissions($url)->can_edit;
    }

    public function canDelete($url = null): bool
    {
        return $this->getPermissions($url)->can_delete;
    }

    private function normalizeUrl($url): string
    {
        if (strpos($url, '/she/insiden') === 0) {
            return '/she/insiden';
        }
        if (strpos($url, '/pic/insiden') === 0) {
            return '/pic/insiden';
        }
        if (strpos($url, '/manager/insiden') === 0) {
            return '/manager/insiden';
        }
        if (strpos($url, '/she/safety-riding') === 0) {
            return '/she/safety-riding';
        }
        return $url;
    }

    private function defaultPermissions()
    {
        return (object)[
            'can_read'   => false,
            'can_add'    => false,
            'can_edit'   => false,
            'can_delete' => false
        ];
    }
}