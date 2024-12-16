<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

// Nhớ kế thừa Class JWTSubject và Class Authenticatable
class ChuTro extends Authenticatable implements JWTSubject 
{
    use HasFactory, Notifiable;
    
    protected $guard = 'chutro'; 

    // File này trỏ đến tên bảng trong cơ sở dữ liệu
    protected $table = 'chutro'; 
    
    // Filable giúp nó có thể tạo bằng Create(), Fill() bên Controller 
    protected $fillable = 
    [
        'ho_ten',
        'email',
        'mat_khau',
        'hinh_anh'
    ];

    protected $hidden = [
        'mat_khau',
        'id',
    ];
    // Các phương thức của jwt
    // Phương thức này dùng để trả về khóa chính của người dùng cho JWT
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


    public function daytros()
    {
        // 
        return $this->hasMany(daytro::class, 'chutro_id');
    }

     // N:1 relationship with khachthue
     public function khachthues()
     {
         return $this->hasMany(khachthue::class, 'chutro_id');
     }

     // 1:1 relationship with DichVu
    public function dichvu()
    {
        return $this->hasOne(DichVu::class, 'chutro_id');
    }
}
