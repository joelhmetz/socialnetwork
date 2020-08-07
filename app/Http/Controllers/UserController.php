<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class UserController extends Controller
{
    private $loggedUser;

    public function __construct(){
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
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

    public function update(Request $request){
        // PUT api/user (name, email, birthdate, city, work, password, password_confirm)
        $array = ['error' => ''];

        $name = $request->input('name');
        $email = $request->input('email');
        $birthdate = $request->input('birthdate');
        $city = $request->input('city');
        $work = $request->input('work');
        $password = $request->input('password');
        $password_confirm = $request->input('password_confirm');

        $user = User::find($this->loggedUser['id']);

        // Name
        if($name){
            $user->name = $name;
        }

        // E-mail
        if($email){
            
        }

        $user->save();

        return $array;
    }
}
