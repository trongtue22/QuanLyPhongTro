<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dichvu extends Model
{
    use HasFactory;

    protected $table = 'dichvu'; 
    
    // Filable giúp nó có thể tạo bằng Create(), Fill() bên Controller 
    protected $fillable = 
    [
        'chutro_id', // Foreign key for ChuTro
        'dien',
        'nuoc',
        'wifi',
        'guixe',
        'rac'
    ];


    // public function hoadons()
    // {
    //     return $this->belongsToMany(hoadon::class,'hoadon_dichvu');
    // }



    // Mối quan hệ 1:N với KhachThuePhongTro
    public function khachThuePhongTros()
    {
        return $this->hasMany(khachthue_phongtro::class, 'dichvu_id');
    }

    // Mối quan hệ 1:N với HoaDon
    public function hoadons()
    {
        return $this->hasMany(HoaDon::class, 'dichvu_id');
    }

    // 1:1 relationship with ChuTro
    public function chutro()
    {
        return $this->belongsTo(ChuTro::class, 'chutro_id');
    }



}
