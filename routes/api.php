<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register','Api\Auth\RegisterController@register');
Route::post('login','Api\Auth\LoginController@login');
Route::post('refresh','Api\Auth\LoginController@refresh');
Route::post('social_auth','Api\Auth\SocialAuthController@socialAuth');

Route::get('showevents','Api\Auth\EventController@showevents');
Route::get('showparticipation','Api\Auth\ParticipateController@showParticipation');
Route::get('showWeeklyevents','Api\Auth\EventController@showWeeklyevents');
Route::get('showSpecialevents','Api\Auth\EventController@showSpecialevents');

//routes to get the calender dates 
Route::get('sorting_calender','Api\Auth\EventController@viewSortingCalender');
Route::get('packing_calender','Api\Auth\EventController@viewPackingCalender');
Route::get('delivery_calender','Api\Auth\EventController@viewDeliveryCalender');
Route::get('partJoinWeeklyAdmin','Api\Auth\EventController@partJoinWeeklyAdmin');
Route::get('partJoinSpecial','Api\Auth\EventController@partJoinSpecial');

Route::middleware('auth:api')->group(function () {
Route::post('logout','Api\Auth\LoginController@logout');
// Route::post('showWeeklyevents','Api\Auth\EventController@showWeeklyevents');
Route::get('posts','Api\PostController@index');
Route::get('/user','Api\UserController@index');
Route::post('update_user','Api\UserController@updateUser');
Route::post('delete_user','Api\UserController@deleteUser');
Route::post('blockuser','Api\UserController@blockUser')->name('blockuser');



    /**********request related to events*********************/
  	Route::post('events','Api\Auth\EventController@events');
    Route::post('delete_event','Api\Auth\EventController@destroy')->name('destroy');
  	Route::post('update_event','Api\Auth\EventController@Eventupdate');
    Route::post('update_special_event','Api\Auth\EventController@SpecialEventupdate');

     /**********request related to Participate***************/
    Route::post('vol_Participate','Api\Auth\ParticipateController@requestToParticipate');
    Route::post('update_Participate','Api\Auth\ParticipateController@Participateupdate');
    Route::post('delete_participation','Api\Auth\ParticipateController@destroy');
   
    /************ api to get the weekly and participation join data*******/
    Route::post('partJoinWeekly','Api\Auth\EventController@partJoinWeekly');

 /************ api to get the weekly and participation and user join data*******/
    Route::post('ViewUserWithEventTask','Api\Auth\ParticipateController@ViewUserWithEventTask');
   
   /* Route::get('user',function(Request $request){
    	return $request->user();
    });*/


   Route::post('storefcm', 'Api\UserController@storeFCM')->name('storefcm'); // store fcm token 

});




/*Route::middleware('auth:api')->get('/user', function (Request $request) {
      
	//dd("salut");
});*/

 
