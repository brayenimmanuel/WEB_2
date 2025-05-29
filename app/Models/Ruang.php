<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Ruang extends Model
{
    // Menentukan nama tabel
    protected $table = 'ruang';
 
    // Menentukan kolom yang akan diisi
    protected $fillable = [
        'kode',
        'nama',
        'status',
    ];
 
    // menentukan relasi dengan model Peminjaman
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'ruang_id');
    }
}