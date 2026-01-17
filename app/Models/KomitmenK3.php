<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomitmenK3 extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'tb_komitment_k3';

    // Sesuaikan nama kolom timestamp jika tidak menggunakan default 'created_at' / 'updated_at'
    // const CREATED_AT = 'timestamp'; 
    // const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'user_id', // Foreign key ke tb_user
        'komitmen',
        'bukti',
        'status',
    ];

    /**
     * Relasi ke User (Setiap Komitmen K3 dimiliki oleh satu User).
     */
    public function user()
    {
        // 'user_id' adalah foreign key di tabel tb_komitment_k3
        return $this->belongsTo(User::class, 'user_id');
    }
}