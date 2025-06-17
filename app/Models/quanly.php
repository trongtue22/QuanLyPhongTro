<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

// Nhớ kế thừa Class JWTSubject và Class Authenticatable
// Nhớ dùng class 'QuanLy' viết hoa vì model khai báo trong auth cũng viết hoa 
class QuanLy extends Authenticatable implements JWTSubject 
{
    use HasFactory, Notifiable;
    
    protected $guard = 'quanly'; 
 
    protected $table = 'quanly'; 
    
    protected $fillable = 
    [
        'chutro_id',
        'ho_ten',
        'email',
        'sodienthoai',
        'gioitinh',
        'cccd',
        'mat_khau',
    ];

    protected $hidden = [
        // 'id',
        'chutro_id',
        'mat_khau'
    ];
    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // Phương thức này trả về các claim tùy chỉnh cho JWT
    public function getJWTCustomClaims()
    {
        return [];
    }

     // Phải có hàm này để hàm attempt có thể thấy được mật khẩu 
     public function getAuthPassword() 
     {
         return $this->mat_khau;
     }

    // public function username()
    // {
    //     return 'phone_number';
    // }
 

    public function daytros()
    {
        return $this->hasMany(daytro::class, 'quanly_id');
    }

    
    public function chutro()
    {
        return $this->belongsTo(Chutro::class, 'chutro_id');
    }

}
