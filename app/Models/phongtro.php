<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class phongtro extends Model
{
    use HasFactory;   
    protected $table = 'phongtro';     

    protected $fillable = [
    'daytro_id',
    'sophong',
    'tienphong',
    'status',
    ];

    protected $hidden = [
        'daytro_id'
    ];


    public function daytro()
    {
        return $this->belongsTo(daytro::class,'daytro_id');
    }


    public function khachthues()
    {
        return $this->belongsToMany(khachthue::class, 'khachthue_phongtro');
    }
}
