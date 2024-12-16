<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class daytro extends Model
{
    use HasFactory;
    
        protected $table = 'daytro'; 
        
        // Filable giúp nó có thể tạo bằng Create(), Fill() bên Controller 
        protected $fillable = 
        [
            'chutro_id',
            'tendaytro',
            'tinh',
            'huyen',
            'xa',
            'sonha',
            'quanly_id', 
        ];

    protected $hidden = [
        'chutro_id',
        'quanly_id', 
      
    ];

    public function chutro()
    {
        return $this->belongsTo(chutro::class, 'chutro_id');
    }

    
    public function phongtros()
    {
        return $this->hasMany(phongtro::class, 'daytro_id');
    }

     // Mối quan hệ với Quản Lý
     public function quanly()
     {
         return $this->belongsTo(quanly::class, 'quanly_id');
     }
}
