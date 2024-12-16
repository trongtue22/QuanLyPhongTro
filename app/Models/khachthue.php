<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class khachthue extends Model
{
    use HasFactory;

    protected $table = 'khachthue';     

    protected $fillable = [
        'chutro_id',    // Foreign key to link with ChuTro
        'tenkhachthue',
        'sodienthoai',
        'ngaysinh',
        'cccd',
        'gioitinh',
    ];

    protected $hidden = [
        'chutro_id',
    ];

    // Liên kết N:N với bên phòng trọ 
    public function phongtros()
    {
        return $this->belongsToMany(phongtro::class, 'khachthue_phongtro');
    }

    public function chutro()
    {
        return $this->belongsTo(ChuTro::class, 'chutro_id');
    }
   
}
