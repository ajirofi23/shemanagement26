<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pta extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'tb_master_pta'; 

    // Menonaktifkan timestamps (created_at dan updated_at)
    public $timestamps = false; 
    
    // Melindungi kolom 'id' dari mass assignment
    protected $guarded = ['id']; 
}