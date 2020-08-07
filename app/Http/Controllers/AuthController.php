<?php

//eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC91c2VyIiwiaWF0IjoxNTk2NzMyNjk3LCJuYmYiOjE1OTY3MzI2OTcsImp0aSI6IjlCTnN0T2ZCTUVFeHNGb0ciLCJzdWIiOjEsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.QGxSkvw47UDPYBe3Yd5HJwS72Ri1B194gftcNdHvLtQ

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class AuthController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api', ['except' => ['login', 'create', 'unauthorized']]);
    }

    public function unauthorized(){
        return response()->json(['error'=>'Não autorizado'], 401);
    }

    public function login(Request $request){
        $array = ['error' => ''];
   
        $email = $request->input('email');
        $password = $request->input('password');

        if($email && $password){
            $token = auth()->attempt([
                'email' => $email,
                'password' => $password
            ]);
    
            if(!$token){
                $array['error'] = 'E-mail e/ou senha errados!';
                return $array;
            }
    
            $array['token'] = $token;    
            return $array;
        }
        
        $array['error'] = 'Dados não enviados!';
        return $array;
    }

    public function logout(){
        auth()->logout();
        return ['error'=>''];
    }

    public function refresh(){
        $token = auth()->refresh();
        return [
            'error'=> '',
            'token'=> $token
        ];
    }    
}
