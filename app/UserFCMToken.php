<?php

namespace App;
use App\User;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserFCMToken extends Model


{

   
   protected $table = 'manage_fcm_tokens';
    protected $primaryKey = 'fcmId';

	 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'userId','fcmToken'
    ];
    //created by paramveer
   

 


    


}
