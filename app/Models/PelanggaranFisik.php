<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelanggaranFisik extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     * @var string
     */
    protected $table = 'tb_master_pf'; // Sesuaikan jika nama tabel master Anda berbeda

    /**
     * Atribut yang dapat diisi secara massal.
     * @var array
     */
    protected $fillable = [
        'kode_pf', 
        'nama_pf', // Nama Pelanggaran Fisik
        // ... kolom master lainnya
    ];
    
    // --- RELATIONS ---
    
    /**
     * Relasi Many-to-Many ke SafetyRiding.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function safetyRidings()
    {
        // Asumsi: pivot table bernama 'safety_riding_pelanggaran_fisik'
        return $this->belongsToMany(SafetyRiding::class, 'safety_riding_pelanggaran_fisik', 'pf_id', 'safety_riding_id');
    }
}