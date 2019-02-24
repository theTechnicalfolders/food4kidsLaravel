<?php

namespace App;
use App\User;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Participate extends Model


{

   
   protected $table = 'participate_event';
    protected $primaryKey = 'participate_id';

	 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','event_id','user_startTime', 'user_endTime', 'admin_approveStatus'
    ];
    //created by paramveer
   

    public function user(){
        return $this->belongsTo(User::class);
    }

 public function events(){
        return $this->hasMany(Event::class);
    }


    


}
