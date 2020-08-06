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

    public function create(Request $request){
        // POST *api/user (name, email, password, birthdate)
        $array = ['error' => ''];

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $birthdate = $request->input('birthdate');

        if($name && $email && $password && $birthdate){
            // Validando a data de nascimento
            if(strtotime($birthdate) === false){
                $array['error'] = 'Data de nascimento inválida.';
                return $array;
            }
            // Verificar se existe e-mail
            $emailExists = User::where('email', $email)->count();
            if($emailExists === 0){
                
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $user = new User();
                $user->name = $name;
                $user->email = $email;
                $user->password = $hash;
                $user->birthdate = $birthdate;
                $user->save();

                $token = auth()->attempt([
                    'email' => $email,
                    'password' => $password
                ]);

                if(!$token){
                    $array['error'] = 'Ocorreu um erro.';
                    return $array;
                }

                $array['token'] = $token;

            }else{
                $array['error'] = 'E-mail já está em uso.';
                return $array;
            }
        }else{
            $array['error'] = 'Não enviou todos os campos.'; 
            return $array;
        }

        return $array;
    }
}
