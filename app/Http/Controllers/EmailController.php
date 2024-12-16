<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Mail\DemoMail;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    //
    public function sendWelcomeEmail()
    {
       
        $title = 'Welcome to the laracoding.com example email';
        $body = 'Thank you for participating!';
        
        // Hàm này sẽ gọi tới chức năng thực hiện gửi Email 
        // to(): người các đoạn tin kia tối người nhận có tên là email gì 
        Mail::to('trongtue22@gmail.com')->send(new DemoMail($title, $body));
        //   dd(1);
        dd("Email sent successfully!");
    }
}
