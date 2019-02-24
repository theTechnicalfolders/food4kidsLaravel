<?php


namespace App\Http\Controllers\Api;
use Auth;
use DB; 
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserFCMToken;


class UserController extends Controller
{
    //
   public function index(){

    	$userdata = Auth::user()->get();

    	$user = User::where('id',Auth::user()->id)->get();
     
    	return response()->json(['data' => $user], 200, [], JSON_NUMERIC_CHECK);
    }

    public function updateUser(Request $request){
  try {

       $user_id = \Auth::user()->id;
       $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'address'=>'required',
            'city'=>'required',
            'postal_code'=>'required',
            'mobile'=>'required'
                  ]);

          $user= User::where('id',$user_id)->update([
            'name' => request('name'),
            'email' => request('email'),
            'address' => request('address'),
            'city' => request('city'),
            'postal_code' => request('postal_code'),
            'mobile' => request('mobile')
                 ]);

       return response()->json(['status' =>'success'], 200, [], JSON_NUMERIC_CHECK);
        
      } catch (Exception $e) {      
     return response()->json(['status'=>'failed'],203);
              }
    }

  public function deleteUser(Request $request)
    {
      try{

        $user = User::find(request('id'));
        $user->delete();

        //return response()->json(['status'=>'success'],200);return redirect('/')->with('success', 'Event has been deleted!!');
  
        return response()->json(['status'=>'success'],200,['data'=>'Event Deleted Successfully']);
      }
      catch (\Exception $e) {
      return response()->json(['status'=>'failed'],203);      
      }  
   }

     public function blockUser(Request $request)
    {
      try{

        $userid = request('id');

        $result = User::where('id',$userid)->update(['isBlocked','1']);   
  
        return response()->json(['status'=>'success','data'=>'User blocked'],200);
      }
      catch (\Exception $e) {

        return response()->json(['status'=>'failed'],203);      
      }  
   }


   public function storeFCM(){

    $check_if_fcmtoken_exists = UserFCMToken::where('userID', \Auth::user()->id)->where('fcmToken', request('fcmToken'))->get();
        
        if ($check_if_fcmtoken_exists->count() == 0) {
            $store           = new UserFCMToken();
            $store->fcmToken = request('fcmToken');
            $store->userId   = \Auth::user()->id;

            $store->save();

            return response()->json(['status' => 'success'], 200);

        } else {

            return response()->json(['status' => 'success'], 200);

        }

   }
}
