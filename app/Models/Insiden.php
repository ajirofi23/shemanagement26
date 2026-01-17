<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Insiden extends Model
{
    use SoftDeletes;

    protected $table = 'tb_insiden';
    protected $fillable = [
        'tanggal',
        'jam',
        'lokasi',
        'kategori',
        'work_accident_type',
        'departemen',
        'section_id',
        'kondisi_luka',
        'kronologi',
        'keterangan_lain',
        'foto',
        'status',
        'user_id',
        'catatan_reject',
        'catatan_close',
        'created_by'
    ];


    protected $hidden = [
        'deleted_at'
    ];

   
    protected $casts = [
        'tanggal' => 'date',
        'foto' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Nilai default
     */
    protected $attributes = [
        'status' => 'open',
        'foto' => '[]', // array kosong untuk foto
        'kronologi' => null, // nullable
        'keterangan_lain' => null // nullable
    ];

    /**
     * Append custom attributes
     */
    protected $appends = [
        'tanggal_format',
        'jam_format',
        'status_badge',
        'kategori_badge',
        'foto_count'
    ];

    /* ============================
       RELASI
    ============================ */

    /**
     * Relasi ke user yang membuat laporan
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke section
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

      public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    
}