<?php

namespace App;
use App\User;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class WeeklyEvent extends Model


{
public $timestamps = false;
   
   protected $table = 'weeklyevents';
    protected $primaryKey = 'w_event_id';

	 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id','weekly_eventTask','noOfVol','date','start_time', 'end_time',
    ];
    //created by paramveer
   

    public function user(){
        return $this->belongsTo(User::class);
    }


}
