<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Post;
use Image;

class FeedController extends Controller
{
    private $loggedUser;

    public function __construct(){
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function create(Request $request){
        // POST api/feed (type=text/photo, body, photo)
        $array = ['error' => ''];
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

        $type = $request->input('type');
        $body = $request->input('body');
        $photo = $request->file('photo');

        if ($type) {
            
            switch ($type) {
                case 'text':
                    if($body){
                        $array['error'] = 'Texto não enviado!';
                        return $array;
                    }
                    break;

                case 'photo':
                    if(in_array($photo->getClientMimeType(), $allowedTypes)){

                                $fileName = md5(time().rand(0,9999)).'.jpg';
                
                                $destPath = public_path('/media/uploads');
                
                                $img = Image::make($photo->path())
                                ->resize(800, null, function($constraint){
                                    $constraint->aspectRatio();
                                })
                                ->save($destPath.'/'.$fileName);
                
                                $body = $fileName;
                            }
                            else{
                                $array['error'] = 'Arquivo não suportado!';
                                return $array;
                            }
                        break;
                
                default:
                    $array['error'] = 'Tipo de postagem inexistente.';
                    return $array;
                break;
            }

            if ($body) {
                $post = new Post();
                $post->id_user = $this->loggedUser['id'];
                $post->type = $type;
                $psot->created_at = date('Y-m-d H:i:s');
                $post->body = $body;
                $post->save();
            }
        } else {
            $array['error'] = 'Dados não enviados.';
            return $array;
        }
        
        return $array;
    }
}
