<?php

namespace App;
use App\User;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class SpecialEvent extends Model


{
public $timestamps = false;
   
   protected $table = 'specialevents';
    protected $primaryKey = 's_event_id';

	 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id','noOfVol','date','start_time', 'end_time',
    ];
    //created by paramveer
   

    public function user(){
        return $this->belongsTo(User::class);
    }


}
