<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hoadon extends Model
{
    use HasFactory;

    protected $table = 'hoadon'; 
    

    protected $fillable = 
    [
        'dichvu_id',
        'hopdong_id',
        'sodiencu',
        'sodienmoi',
        'sonuoccu',
        'sonuocmoi',
        'tongtien',
        'status',
        'created_at'
    ];

    // public function khachthue_phongtro()
    // {
    //     return $this->belongsTo(khachthue_phongtro::class, 'khachthue_phongtro_id');
    // }


    // public function dichvus()
    // {
    //     return $this->belongsToMany(dichvu::class,'hoadon_dichvu');
    // }

    public function dichvu()
    {
        return $this->belongsTo(DichVu::class, 'dichvu_id');
    }

    public function hopdong()
    {
        return $this->belongsTo(HopDong::class, 'hopdong_id');
    }

    
}
