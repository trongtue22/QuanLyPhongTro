<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class khachthue_phongtro extends Model
{

    use HasFactory;
    
    protected $table = 'khachthue_phongtro';    
    
    protected $fillable = [
        'khachthue_id',
        'phongtro_id',
             
    ];
    
    
    // Các mối quan hệ 1:1
    public function hopdong()
    {
        return $this->hasOne(hopdong::class, 'khachthue_phongtro_id');
    }

    public function hoadon()
    {
        return $this->hasOne(hoadon::class, 'khachthue_phongtro_id');
    }


    // Thiết lập các hàm để nối từ bảng trung gian -> 2 bảng cha 
    public function khachthue()
    {
        return $this->belongsTo(khachthue::class,'khachthue_id');
    }

    
    public function phongtro()
    {
        return $this->belongsTo(phongtro::class,'phongtro_id');
    }

    // Thêm với MQH dịch vụ
    public function dichvu()
    {
        return $this->belongsTo(DichVu::class, 'dichvu_id');
    }
  
}
