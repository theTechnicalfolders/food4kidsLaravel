<?php
namespace App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Route;
use App\User;

class RegisterController extends Controller
{
    use IssueTokenTrait;
    private $client;
    public function __construct(){
        $this->client = Client::find(1);
    }
    public function register(Request $request){

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'address'=>'required',
            'city'=>'required',
            'postal_code'=>'required|min:6',
            'mobile'=>'required|min:10|numeric',
            'userType'=>'required'
        ]);

        $user = User::create([
            'name'=>request('name'),
            'email'=>request('email'),
            'password'=>bcrypt(request('password')),
            'address'=>request('address'),
            'city'=>request('city'),
            'postal_code'=>request('postal_code'),
            'mobile'=>request('mobile'),
            'userType'=>request('userType')
        
        ]);

       return  $this->issueToken($request, 'password');

         }
}


/* $params=[
     'grant_type'=>'password',
     'client_id'=>$this->client->id,
     'client_secret'=>$this->client->secret,
     'username'=>request('email'),
     'password'=>request('password'),
     'scope'=>'*' 
 ];
      $request->request->add($params);

      $proxy=Request::create('oauth/token','POST');
      return Route::dispatch($proxy);*/