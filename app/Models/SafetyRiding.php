<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafetyRiding extends Model
{
    use HasFactory;

    protected $table = 'safety_ridings';

    protected $fillable = [
        'user_id',
        'section_id',
        'waktu_kejadian',
        'type_kendaraan',
        'nopol',
        'keterangan_pelanggaran',
        'total_pelanggaran',
        'bukti',
        'bukti_after',
        'catatan',
        'status',
    ];

    protected $casts = [
        'bukti' => 'array',
        'bukti_after' => 'array', 
        'waktu_kejadian' => 'datetime',
    ];
    
    public function pds()
    {
        return $this->belongsToMany(PelanggaranDokumen::class, 'safety_riding_pelanggaran_dokumen', 'safety_riding_id', 'pd_id');
    }

    public function pfs()
    {
        return $this->belongsToMany(PelanggaranFisik::class, 'safety_riding_pelanggaran_fisik', 'safety_riding_id', 'pf_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id'); 
    }

    // Helper method untuk mendapatkan URL gambar before
    public function getBuktiUrlsAttribute()
    {
        if (empty($this->bukti)) {
            return [];
        }
        
        $buktiArray = is_array($this->bukti) ? $this->bukti : json_decode($this->bukti, true);
        
        return array_map(function($path) {
            return asset('storage/' . $path);
        }, $buktiArray);
    }
    
    // Helper method untuk mendapatkan URL gambar after
    public function getBuktiAfterUrlsAttribute()
    {
        if (empty($this->bukti_after)) {
            return [];
        }
        
        $buktiAfterArray = is_array($this->bukti_after) ? $this->bukti_after : json_decode($this->bukti_after, true);
        
        return array_map(function($path) {
            return asset('storage/' . $path);
        }, $buktiAfterArray);
    }
}