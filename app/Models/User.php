<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- TAMBAH INI
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // TAMBAH: Trait HasFactory (Standar Laravel)
    use HasFactory, Notifiable; 

    protected $table = 'tb_user';
    public $timestamps = false;
    
    // TAMBAH: Kolom otentikasi
    // protected $username = 'usr';
    
    // Sesuaikan Hidden Properties (untuk keamanan)
    protected $hidden = [
        'pswd',
    ];

    protected $fillable = [
        'nama',
        'section_id',
        'usr',
        'pswd',
        'email',
        'no_hp',
        'kode_user',
        'is_active',
        'level',
        'is_user_computer',
        'image_sign'
    ];

    /**
     * Override method otentikasi untuk menggunakan kolom 'pswd'.
     */
    public function getAuthPassword()
    {
        return $this->pswd;
    }
    
    // =========================================================
    //               HUBUNGAN MODEL (RELATIONSHIPS)
    // =========================================================

    /**
     * Relasi ke Section (Many-to-One).
     * INI YANG PALING PENTING untuk mengatasi RelationNotFoundException.
     */
    public function section()
    {
        // Mencari Section berdasarkan user.section_id
        return $this->belongsTo(Section::class, 'section_id'); 
    }

    /**
     * Relasi ke KomitmenK3 (One-to-One).
     */
    public function komitmenK3()
    {
        // Mencari KomitmenK3 berdasarkan komitmenk3.user_id
        return $this->hasOne(KomitmenK3::class, 'user_id');
    }
}