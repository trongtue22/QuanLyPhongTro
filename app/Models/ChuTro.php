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
        'cccd',
        'sodienthoai',
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

    public function phongtros()
    {
    return $this->hasManyThrough(
        PhongTro::class, // Model cuối cùng
        DayTro::class,   // Model trung gian
        'chutro_id',                 // Foreign key ở DayTro
        'daytro_id',                 // Foreign key ở PhongTro
        'id',                        // Local key ở ChuTro
        'id'                         // Local key ở DayTro
    );
    }

    public function hopdongs()
{
    return $this->hasManyThrough(
        HopDong::class,   // Model cuối
        PhongTro::class,  // Model trung gian
        'daytro_id',                  // FK ở PhongTro liên kết đến DayTro
        'phongtro_id',                // FK ở HopDong liên kết đến PhongTro
        'id',                         // Local key ở ChuTro
        'id'                          // Local key ở DayTro
    )->whereHas('daytro', function ($query) {
        $query->whereColumn('daytros.chutro_id', 'chutros.id');
    });
}
}
