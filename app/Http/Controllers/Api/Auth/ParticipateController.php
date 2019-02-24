<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Participate;
use App\UserFCMToken;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use App\User;

class ParticipateController extends Controller
{
    use IssueTokenTrait;
    private $client;

    public function __construct()
    {
        $this->client = Client::find(1);
    }

    public function showParticipation()
    {
        $participation = DB::table('participate_event')
            ->orderBy('participate_id', 'desc')
            ->get();

        return response()->json(['data' => $participation], 200, [], JSON_NUMERIC_CHECK);
    }
    // user participate function
    public function requestToParticipate(Request $request)
    {
        try {

            $this->validate($request, [
                'user_id'             => 'required',
                'event_id'            => 'required',
                'user_startTime'      => 'required',
                'user_endTime'        => 'required',
                'admin_approveStatus' => 'required',
            ]);

            $event_exists = Participate::where(['user_id' => Auth::user()->id, 'event_id' => request('event_id')])->get();
            if (count($event_exists) > 0) {
                return response()->json(['data' => "User can not add multiple entry for single Task"], 200, [], JSON_NUMERIC_CHECK);
            }

            $event = Participate::create([
                'user_id'             => Auth::user()->id,
                'event_id'            => request('event_id'),
                'user_startTime'      => request('user_startTime'),
                'user_endTime'        => request('user_endTime'),
                'admin_approveStatus' => request('admin_approveStatus'),
            ]);

            $users_fcm = User::where('userType', 1)->get();

            if ($users_fcm->count() > 0) {

                foreach ($users_fcm as $user) {

                    $fcmtokens          = UserFCMToken::where('userId', $user->id)->select("fcmtoken")->get();
                    $device_token_array = array();

                    if ($fcmtokens->count() > 0) {

                        foreach ($fcmtokens as $key => $token) {
                            array_push($device_token_array, $token->fcmtoken);
                        }
                        //dd('device token',$device_token_array);

                        $title   = "New Request for event Participation.";
                        $body    = "Hello admin you got a new request regarding participation in an event.";
                        $feedbck = \PushNotification::setService('fcm')
                            ->setMessage([
                                'notification' => [
                                    'title' => $title,
                                    'body'  => $body,
                                    'sound' => 'default',
                                ],
                                'data'         => [
                                    'notificationtype' => 'new_event',
                                    'dummy'            => "data",
                                ],
                            ])
                            ->setApiKey('AAAA9vKf0Ro:APA91bFpfR8CnEopIvd-r9c8oeeTvyub36rig3rtxcONMxAGptXgoLVwOCBCZr1GJ-AcESo0c5b_i8LsT79C0iJEdSpFLwIiWj7FBZJFIMHylyifYAhPzgVmPGmU54Ul6sAHLO-jnZKYucwt7YjFwMOX_MwYT02LKw')
                            ->setDevicesToken($device_token_array)
                            ->send()
                            ->getFeedback();

                        // dd($feedbck);

                    }

                }
            }

            return response()->json(['data' => $event], 200, [], JSON_NUMERIC_CHECK);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed'], 203);
        }

    }

    //for updating participation Information
    public function Participateupdate(Request $request)
    {
        try {
            $send_fcm       = false;
            $user_id        = \Auth::user()->id;
            $participate_id = request('participate_id');
            $this->validate($request, [
                'user_id'             => 'required',
                'event_id'            => 'required',
                'user_startTime'      => 'required',
                'user_endTime'        => 'required',
                'admin_approveStatus' => 'required',
            ]);

            $initial_state = Participate::where('participate_id', $participate_id)->first();

            // dd($initial_state);

            $initial_approval_state = $initial_state->admin_approveStatus;

            $participate = Participate::where('participate_id', $participate_id)->update([
                'user_id'             => request('user_id'),
                'event_id'            => request('event_id'),
                'user_startTime'      => request('user_startTime'),
                'user_endTime'        => request('user_endTime'),
                'admin_approveStatus' => request('admin_approveStatus'),
            ]);

            $after_query = Participate::where('participate_id', $participate_id)->first();

            $after_query_state = $after_query->admin_approveStatus;

            if ($initial_approval_state == 0 && $after_query_state == 1) {
                $send_fcm = true;
            }

            $fcmtokens          = UserFCMToken::where('userId', request('user_id'))->select("fcmtoken")->get();
            $device_token_array = array();

            if ($fcmtokens->count() > 0 && $send_fcm == true) {

                foreach ($fcmtokens as $key => $token) {
                    array_push($device_token_array, $token->fcmtoken);
                }
                 //dd($device_token_array);

                $title   = "Event Request approved";
                $body    = "Your Request has been approved.";
                $feedbck = \PushNotification::setService('fcm')
                    ->setMessage([
                        'notification' => [
                            'title' => $title,
                            'body'  => $body,
                            'sound' => 'default',
                        ],
                        'data'         => [
                            'notificationtype' => 'schedule_approval',
                            'dummy'            => 'data',
                        ],
                    ])
                    ->setApiKey('AAAA9vKf0Ro:APA91bFpfR8CnEopIvd-r9c8oeeTvyub36rig3rtxcONMxAGptXgoLVwOCBCZr1GJ-AcESo0c5b_i8LsT79C0iJEdSpFLwIiWj7FBZJFIMHylyifYAhPzgVmPGmU54Ul6sAHLO-jnZKYucwt7YjFwMOX_MwYT02LKw')
                    ->setDevicesToken($device_token_array)
                    ->send()
                    ->getFeedback();

               dd($feedbck);

            }

            return response()->json(['status' => 'success'], 200, [], JSON_NUMERIC_CHECK);
        } catch (Exception $e) {

            // dd($e);
            return response()->json(['status' => 'failed'], 203);
        }
    }

    //function to delete event
    public function destroy(Request $request)
    {
        //todo check if user exists linked to this
        try {
            $participation = Participate::find(request('participate_id'));
            $participation->delete();
            return response()->json(['status' => 'success'], 200, ['data' => 'Participation Removed Successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed'], 203);
        }
    }

    //function for sorting schedule
    public function ViewUserWithEventTask(Request $request)
    {

        //   $weeksort=WeeklyEvent::where('weekly_eventTask','Sorting')->get();
        $userType = request('userType');
        if ($userType == 0) {
            $week = DB::table('weeklyevents')->join('participate_event', 'weeklyevents.event_id', '=', 'participate_event.event_id')
                ->join('users', 'users.id', '=', 'participate_event.user_id')
                ->where(['date' => request('date')])
                ->select('users.id',
                    'users.name',
                    'users.email',
                    'users.address',
                    'users.city',
                    'users.postal_code',
                    'users.mobile',
                    'weeklyevents.date',
                    'weeklyevents.w_event_id',
                    'participate_event.event_id',
                    'participate_event.participate_id',
                    'participate_event.admin_approveStatus',
                    'participate_event.user_startTime',
                    'participate_event.user_endTime')->get();
        } else {
            $week = DB::table('specialevents')->join('participate_event', 'specialevents.event_id', '=', 'participate_event.event_id')
                ->join('users', 'users.id', '=', 'participate_event.user_id')
                ->where(['date' => request('date')])
                ->select('users.id',
                    'users.name',
                    'users.email',
                    'users.address',
                    'users.city',
                    'users.postal_code',
                    'users.mobile',
                    'specialevents.date',
                    'specialevents.s_event_id',
                    'participate_event.event_id',
                    'participate_event.participate_id',
                    'participate_event.admin_approveStatus',
                    'participate_event.user_startTime',
                    'participate_event.user_endTime')->get();

        }

        /*  $weeksort= DB::table('weeklyevents')->where('weekly_eventTask', 'Sorting')->pluck('date'); */

        return response()->json(['data' => $week], 200, [], JSON_NUMERIC_CHECK);
    }

    public function viewUserPartVolunteer(Request $request)
    {
        $user   = request('id');
        $result = DB::table('weeklyevents')->join('participate_event',
            'weeklyevents.event_id', '=', 'participate_event.event_id')
            ->where('participate_event.user_id', $user)
            ->get();
        return response()->json(['data' => $result], 200, [], JSON_NUMERIC_CHECK);
    }

}
