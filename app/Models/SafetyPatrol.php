<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafetyPatrol extends Model
{
    use HasFactory;

    protected $table = 'safety_patrol';

    protected $fillable = [
        'tanggal',
        'eporte',
        'area',
        'problem',
        'counter_measure',
        'section_id',
        'due_date',
        'foto_before',
        'foto_after',
        'created_by',
        'status',
        'catatan'
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'due_date' => 'datetime',
    ];
    // Hapus casting untuk foto_before dan foto_after karena bukan array

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    /**
     * Scope untuk filtering berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk mencari berdasarkan E-PORTE atau Area
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('eporte', 'like', "%{$search}%")
                    ->orWhere('area', 'like', "%{$search}%")
                    ->orWhere('problem', 'like', "%{$search}%");
    }

    /**
     * Scope untuk laporan yang masih Open atau Progress
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Open', 'Progress']);
    }

    /**
     * Scope untuk laporan yang sudah selesai
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['Close', 'Rejected']);
    }

    /**
     * Accessor untuk format tanggal yang lebih mudah dibaca
     */
    public function getTanggalFormattedAttribute()
    {
        return $this->tanggal ? $this->tanggal->format('d-m-Y') : null;
    }

    /**
     * Accessor untuk format due date yang lebih mudah dibaca
     */
    public function getDueDateFormattedAttribute()
    {
        return $this->due_date ? $this->due_date->format('d-m-Y') : null;
    }

    /**
     * Check jika ada foto before
     */
    public function getHasFotoBeforeAttribute()
    {
        return !empty($this->foto_before);
    }

    /**
     * Check jika ada foto after
     */
    public function getHasFotoAfterAttribute()
    {
        return !empty($this->foto_after);
    }

    /**
     * Check jika laporan sudah lewat due date
     */
    public function getIsOverdueAttribute()
    {
        if (!$this->due_date || $this->status === 'Close' || $this->status === 'Rejected') {
            return false;
        }
        
        return now()->greaterThan($this->due_date);
    }

    /**
     * Hitung selisih hari dengan due date
     */
    public function getDaysUntilDueAttribute()
    {
        if (!$this->due_date) {
            return null;
        }
        
        return now()->diffInDays($this->due_date, false);
    }

    /**
     * Get URL foto before
     */
    public function getFotoBeforeUrlAttribute()
    {
        if (empty($this->foto_before)) {
            return null;
        }
        return asset('storage/' . $this->foto_before);
    }

    /**
     * Get URL foto after
     */
    public function getFotoAfterUrlAttribute()
    {
        if (empty($this->foto_after)) {
            return null;
        }
        return asset('storage/' . $this->foto_after);
    }
}