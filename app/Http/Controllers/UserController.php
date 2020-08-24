<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\User;
use Image;


class UserController extends Controller
{
    private $loggedUser;

    public function __construct(){
        $this->middleware('auth:api', ['except' => ['create']]);
        $this->loggedUser = auth()->user();
    }

    public function create(Request $request) {
        $array = ['error' => ''];
        $data = $request->only([
            'name',
            'email',
            'password',
            'birthdate',
        ]);

        $validator = Validator::make(
            $data,
            [
                'name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
                'password' => ['required', 'string', 'min:6'],
                'birthdate' => ['required', 'date'],
            ]
        );
        if ($validator->fails())
        {
            $array['error'] = $array['error'] = $validator->errors();
            return json_encode($array, JSON_UNESCAPED_UNICODE);
        }
        else
        {
            $array['data'] = $request->all();

            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $hash = password_hash($request->input('password'), PASSWORD_DEFAULT);
            $user->password = $hash;
            $user->birthdate = $request->input('birthdate');
            $user->save();

            return json_encode($array, JSON_UNESCAPED_UNICODE);

        }
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
            if($email != $user->email){
                $emailExists = User::where('email', $email)->count();
                if ($emailExists === 0) {
                    $user->email = $email;
                }else {
                    $array['error'] = 'E-mail já está em uso!';
                    return $array;
                }
            }
        }

        // Birthdate
        if($birthdate){
            if (strtotime($birthdate) === false) {
                $array['error'] = 'Data de nascimento inválida';
                return $array;
            }
            $user->birthdate = $birthdate;
        }

        // City
        if($city){
            $user->city = $city;
        }

        // Work
        if($work){
            $user->work = $work;
        }

        // Password
        if($password && $password_confirm){
            if ($password_confirm === $password_confirm) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $user->password = $hash;
            }else{
                $array['error'] = 'A senha nova deve ser igual a de confirmação!';
                return $array;
            }
        }

        $user->save();

        return $array;
    }

    public function updateAvatar(Request $request){
        $array = ['error' => ''];
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

        $image = $request->file('avatar');

        if ($image) {
            if(in_array($image->getClientMimeType(), $allowedTypes)){

                $fileName = md5(time().rand(0,9999)).'.jpg';

                $destPath = public_path('/media/avatars');

                $img = Image::make($image->path())->fit(200, 200)->save($destPath.'/'.$fileName);

                $user = User::find($this->loggedUser['id']);
                $user->avatar = $fileName;
                $user->save();

                $array['url'] = url('/media/avatars/'.$fileName);

            }else{
                $array['error'] = 'Arquivo não suportado!';
                return $array;
            }
        } else {
            $array['error'] = 'Arquivo não enviado!';
            return $array;
        }

        return $array;
    }

    public function updateCover(Request $request){
        $array = ['error' => ''];
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

        $image = $request->file('cover');

        if ($image) {
            if(in_array($image->getClientMimeType(), $allowedTypes)){

                $fileName = md5(time().rand(0,9999)).'.jpg';

                $destPath = public_path('/media/covers');

                $img = Image::make($image->path())->fit(850, 310)->save($destPath.'/'.$fileName);

                $user = User::find($this->loggedUser['id']);
                $user->cover = $fileName;
                $user->save();

                $array['url'] = url('/media/covers/'.$fileName);

            }else{
                $array['error'] = 'Arquivo não suportado!';
                return $array;
            }
        } else {
            $array['error'] = 'Arquivo não enviado!';
            return $array;
        }

        return $array;
    }
}
