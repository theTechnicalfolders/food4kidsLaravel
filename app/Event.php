<?php

namespace App;
use App\User;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model


{

   
   protected $table = 'events';
    protected $primaryKey = 'eventId';

	 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'userId','eventType','eventTitle', 'eventDescription', 'eventAddress','postal_code','event_Date','event_Organizer'
    ];
    //created by paramveer
   

    public function user(){
        return $this->belongsTo(User::class);
    }

 public function events(){
        return $this->hasMany(Event::class);
    }


    


}
