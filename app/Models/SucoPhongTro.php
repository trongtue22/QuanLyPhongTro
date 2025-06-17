<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SucoPhongTro extends Model
{
    use HasFactory;
    protected $table = 'sucophongtro';

    protected $fillable = [
        'phongtro_id',
        'loai_su_co',
        'mo_ta',
        'trang_thai',
        'goi_y_dich_vu',
    ];

    protected $casts = [
        'goi_y_dich_vu' => 'array',
    ];

    
    public function phongtro()
    {
        return $this->belongsTo(phongtro::class, 'phongtro_id');
    }
}
