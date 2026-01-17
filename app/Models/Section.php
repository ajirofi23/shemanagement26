<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    
    // Nama tabel di database
    protected $table = 'tb_section'; 
    
    // Kolom yang dapat diisi
    protected $fillable = [
        'section',
        'department',
        'description',
        'assigned_work_order',
    ];

    /**
     * Relasi ke User (Satu Section dimiliki oleh banyak User).
     */
    public function users()
    {
        // 'section_id' adalah foreign key di tabel tb_user
        return $this->hasMany(User::class, 'section_id');
    }
}