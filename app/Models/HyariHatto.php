<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import Models yang digunakan untuk relasi Many-to-Many
use App\Models\Pta;
use App\Models\Kta;
use App\Models\Pb;
// Jika Anda punya Model Section: use App\Models\Section; 

class HyariHatto extends Model
{
    use HasFactory;

    // Tentukan nama tabel
    protected $table = 'tb_hyari_hatto';

    // Aktifkan timestamps (sesuai langkah migrasi sebelumnya)
    public $timestamps = true; 

    // Kolom yang aman diisi (HAPUS pta_id, kta_id, pb_id karena sudah di tabel pivot)
    protected $fillable = [
        'deskripsi', 
        'usulan', 
        'bukti', 
        'rekomendasi',
        'section_id',
        'lokasi',
        'pelapor'
    ];
    
    // Gunakan belongsToMany untuk relasi yang multi-pilihan (Many-to-Many)
    // Relasi ke Perilaku Tidak Aman (PTA)
    public function ptas()
    {
        // Parameter 2: Nama tabel pivot (tb_hyarihatto_pta)
        // Parameter 3: Foreign key model ini di tabel pivot (hyari_hatto_id)
        // Parameter 4: Foreign key model target di tabel pivot (pta_id)
        return $this->belongsToMany(Pta::class, 'tb_hyarihatto_pta', 'hyari_hatto_id', 'pta_id');
    }
    
    // Relasi ke Kondisi Tidak Aman (KTA)
    public function ktas()
    {
        return $this->belongsToMany(Kta::class, 'tb_hyarihatto_kta', 'hyari_hatto_id', 'kta_id');
    }
    
    // Relasi ke Potensi Bahaya (PB)
    public function pbs()
    {
        return $this->belongsToMany(Pb::class, 'tb_hyarihatto_pb', 'hyari_hatto_id', 'pb_id');
    }
    
    // Relasi ke Section (Jika ada master table untuk section)
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
    
    
    public function user()
    {
        return $this->belongsTo(User::class, 'pelapor'); 
    }

}