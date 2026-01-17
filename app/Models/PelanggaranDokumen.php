<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelanggaranDokumen extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     * @var string
     */
    protected $table = 'tb_master_pd'; 

    /**
     * Atribut yang dapat diisi secara massal.
     * @var array
     */
    protected $fillable = [
        'kode_pd', 
        'nama_pd',
    ];
    

    public function safetyRidings()
    {
        return $this->belongsToMany(SafetyRiding::class, 'safety_riding_pelanggaran_dokumen', 'pd_id', 'safety_riding_id');
    }
}