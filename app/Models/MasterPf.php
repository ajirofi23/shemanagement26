<?php

// app/Models/MasterPf.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPf extends Model
{
    use HasFactory;

    protected $table = 'tb_master_pf'; // Sesuaikan dengan nama tabel Anda
    protected $fillable = ['nama_pf']; // Pastikan nama_pf ada di fillable
}