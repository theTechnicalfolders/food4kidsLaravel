<?php

namespace App;
use App\Post;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
      use HasApiTokens, Notifiable;

      protected $table = 'users';
    protected $primaryKey = 'id';

    //protected $table = 'events';

    //protected $primaryKey = ''

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','name', 'email','isBlocked','password','address','city','postal_code','mobile','userType',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    
    public function socialAccounts(){
        return $this->hasMany(SocialAccount::class);
    }
    public function posts(){
        return $this->hasMany(Post::class);
    }

    /* public function events(){
        return $this->hasMany(Event::class);
    }*/
}
