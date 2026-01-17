<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pb extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'tb_master_pb'; 
    
    // Menonaktifkan timestamps
    public $timestamps = false;
    
    // Melindungi kolom 'id'
    protected $guarded = ['id'];
}