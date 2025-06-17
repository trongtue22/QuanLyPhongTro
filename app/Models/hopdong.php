<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class hopdong extends Model
{
    use HasFactory; 
    // SoftDeletes;
    
    protected $table = 'hopdong';     

    protected $fillable = [
        'khachthue_phongtro_id',
        'songuoithue',
        'ngaybatdau',
        'ngayketthuc',
        'tiencoc',
        'soxe',        
    ];


    public function khachthue_phongtro()
    {
        return $this->belongsTo(khachthue_phongtro::class, 'khachthue_phongtro_id');
    }

    // Define the one-to-many relationship with HoaDon
    public function hoadons()
    {
        return $this->hasMany(HoaDon::class, 'hopdong_id');
    }

}
