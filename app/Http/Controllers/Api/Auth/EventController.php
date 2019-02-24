<?php

namespace App\Http\Controllers\Api\Auth;

use App\Event;
use App\Http\Controllers\Controller;
use App\SpecialEvent;
use App\User;
use App\UserFCMToken;
use App\WeeklyEvent;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use Laravel\Passport\Client;

class EventController extends Controller
{
    use IssueTokenTrait;
    private $client;

    public function __construct()
    {
        $this->client = Client::find(1);
    }

    public function showevents()
    {
        $events = DB::table('events')
            ->orderBy('eventId', 'desc')
            ->get();

        return response()->json(['data' => $events], 200, [], JSON_NUMERIC_CHECK);
    }

    public function showWeeklyevents()
    {
        /*$event_id = request('event_id');
        $weekly=WeeklyEvent::where(['event_id'=>$event_id])->get();*/
        $weeklyevents = DB::table('weeklyevents')
            ->orderBy('w_event_id', 'desc')
            ->get();

        return response()->json(['data' => $weeklyevents], 200, [], JSON_NUMERIC_CHECK);
    }

    public function showSpecialevents()
    {
        /*$event_id = request('event_id');
        $weekly=WeeklyEvent::where(['event_id'=>$event_id])->get();*/
        $specialevents = DB::table('specialevents')
            ->orderBy('s_event_id', 'desc')
            ->get();

        return response()->json(['data' => $specialevents], 200, [], JSON_NUMERIC_CHECK);
    }

    //function to create an event
    public function events(Request $request)
    {
        //
        try {
            $this->validate($request, [
                'eventType'        => 'required',
                'eventTitle'       => 'required',
                'eventDescription' => 'required',
                'eventAddress'     => 'required',
                'postal_code'      => 'required|min:6',
                'event_Date'       => 'required',
                'event_Organizer'  => 'required',

            ]);

            $event_exists = Event::where(['event_Date' => request('event_Date')])->get();
            if (count($event_exists) > 0) {
                return response()->json(['msg' => "Event Already exists"], 200, [], JSON_NUMERIC_CHECK);
            }

            $event = Event::create([
                'userId'           => Auth::user()->id,
                'eventType'        => request('eventType'),
                'eventTitle'       => request('eventTitle'),
                'eventDescription' => request('eventDescription'),
                'eventAddress'     => request('eventAddress'),
                'postal_code'      => request('postal_code'),
                'event_Date'       => request('event_Date'),
                'event_Organizer'  => request('event_Organizer'),
            ]);

            $event_get  = $event->eventId;
            $event_type = $event->eventType;

            if ($event_type == 0) {

                $this->validate($request, [
                    'weekly_eventTask' => 'required',
                    'noOfVol'          => 'required',
                    'date'             => 'required',
                    'start_time'       => 'required',
                    'end_time'         => 'required',
                ]);

                $exist = WeeklyEvent::where(['date' => request('date'), 'weekly_eventTask' => request('weekly_eventTask')])->get();
                if (count($exist) > 0) {
                    return response()->json(['msg' => "Data Already exists"], 200, [], JSON_NUMERIC_CHECK);
                } else {
                    $weekly_event = WeeklyEvent::create([
                        'event_id'         => $event->eventId,
                        'weekly_eventTask' => request('weekly_eventTask'),
                        'noOfVol'          => request('noOfVol'),
                        'date'             => request('event_Date'),
                        'start_time'       => request('start_time'),
                        'end_time'         => request('end_time'),
                    ]);
                }
            } else if ($event_type == 1) {

                $this->validate($request, [
                    'noOfVol'    => 'required',
                    'date'       => 'required',
                    'start_time' => 'required',
                    'end_time'   => 'required',
                ]);

                $special_event = SpecialEvent::create([
                    'event_id'   => $event->eventId,
                    'noOfVol'    => request('noOfVol'),
                    'date'       => request('event_Date'),
                    'start_time' => request('start_time'),
                    'end_time'   => request('end_time'),
                ]);
            }

            $users_fcm = User::where('userType', 0)->get();

            if ($users_fcm->count() > 0) {

                foreach ($users_fcm as $user) {

                    $fcmtokens          = UserFCMToken::where('userId', $user->id)->select("fcmtoken")->get();
                    $device_token_array = array();

                    if ($fcmtokens->count() > 0) {

                        foreach ($fcmtokens as $key => $token) {
                            array_push($device_token_array, $token->fcmtoken);
                        }
                        //dd('device token',$device_token_array);

                        $title   = "New Event Created";
                        $body    = "New Event is created for you to participate.";
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

            return response()->json(['dataEvent' => $event], 200, [], JSON_NUMERIC_CHECK);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed'], 200, [], JSON_NUMERIC_CHECK);

        }
        //return response()->json(['dataEvent' => $event], 200, [], JSON_NUMERIC_CHECK);
    }
    //for updating events Information
    public function Eventupdate(Request $request)
    {
        try {
            $user_id  = \Auth::user()->id;
            $event_id = request('eventId');

            $this->validate($request, [
                'eventType'        => 'required',
                'eventTitle'       => 'required',
                'eventDescription' => 'required',
                'eventAddress'     => 'required',
                'postal_code'      => 'required',
                'event_Date'       => 'required',
                'event_Organizer'  => 'required',

            ]);

            $event = Event::where('eventId', $event_id)->update([
                'userId'           => Auth::user()->id,
                'eventType'        => request('eventType'),
                'eventTitle'       => request('eventTitle'),
                'eventDescription' => request('eventDescription'),
                'eventAddress'     => request('eventAddress'),
                'postal_code'      => request('postal_code'),
                'event_Date'       => request('event_Date'),
                'event_Organizer'  => request('event_Organizer'),

            ]);

            $weekly_eventId = request('w_event_id');

            $this->validate($request, [
                'weekly_eventTask' => 'required',
                'noOfVol'          => 'required',
                'date'             => 'required',
                'start_time'       => 'required',
                'end_time'         => 'required',
            ]);

            $weekly_event = WeeklyEvent::where('w_event_id', $weekly_eventId)->update([
                'event_id'         => $event_id,
                'weekly_eventTask' => request('weekly_eventTask'),
                'noOfVol'          => request('noOfVol'),
                'date'             => request('event_Date'),
                'start_time'       => request('start_time'),
                'end_time'         => request('end_time'),
            ]);

            return response()->json(['status' => 'success'], 200, [], JSON_NUMERIC_CHECK);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed'], 203);
        }
    }

    //for updating events Information
    public function SpecialEventupdate(Request $request)
    {
        try {
            $user_id  = \Auth::user()->id;
            $event_id = request('eventId');

            $this->validate($request, [
                'eventType'        => 'required',
                'eventTitle'       => 'required',
                'eventDescription' => 'required',
                'eventAddress'     => 'required',
                'postal_code'      => 'required',
                'event_Date'       => 'required',
                'event_Organizer'  => 'required',

            ]);

            $event = Event::where('eventId', $event_id)->update([
                'userId'           => Auth::user()->id,
                'eventType'        => request('eventType'),
                'eventTitle'       => request('eventTitle'),
                'eventDescription' => request('eventDescription'),
                'eventAddress'     => request('eventAddress'),
                'postal_code'      => request('postal_code'),
                'event_Date'       => request('event_Date'),
                'event_Organizer'  => request('event_Organizer'),

            ]);

            $Special_eventId = request('s_event_id');

            $this->validate($request, [

                'noOfVol'    => 'required',
                'date'       => 'required',
                'start_time' => 'required',
                'end_time'   => 'required',
            ]);

            $weekly_event = SpecialEvent::where('s_event_id', $Special_eventId)->update([
                'event_id'   => $event_id,
                'noOfVol'    => request('noOfVol'),
                'date'       => request('event_Date'),
                'start_time' => request('start_time'),
                'end_time'   => request('end_time'),
            ]);

            return response()->json(['status' => 'success'], 200, [], JSON_NUMERIC_CHECK);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed'], 203);
        }
    }

    //function to delete event
    public function destroy(Request $request)
    {
        //todo check if user exists linked to this
        try {
            $events = Event::find(request('eventId'));
            $events->delete();
            return response()->json(['status' => 'success'], 200, ['data' => 'Event Deleted Successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed'], 203);
        }
    }

    //function for packing schedule
    public function viewPackingCalender()
    {

        $getWeek = WeeklyEvent::where('weekly_eventTask', 'Packing')->get();
        /* $getWeek= DB::table('weeklyevents')->where('weekly_eventTask', 'Packing')->pluck('date'); */

        return response()->json(['data' => $getWeek], 200, [], JSON_NUMERIC_CHECK);
    }

    //function for sorting schedule
    public function viewSortingCalender(Request $request)
    {

        $weeksort = WeeklyEvent::where('weekly_eventTask', 'Sorting')->get();
        /*  $weeksort= DB::table('weeklyevents')->where('weekly_eventTask', 'Sorting')->pluck('date'); */

        return response()->json(['data' => $weeksort], 200, [], JSON_NUMERIC_CHECK);
    }

    //function for delivery schedule
    public function viewDeliveryCalender(Request $request)
    {
        $getDeliver = WeeklyEvent::where('weekly_eventTask', 'Delivery')->get();
        /*  $getDeliver= DB::table('weeklyevents')->where('weekly_eventTask', 'Delivery')->pluck('date');*/
        return response()->json(['data' => $getDeliver], 200, [], JSON_NUMERIC_CHECK);
    }

    public function partJoinWeekly(Request $request)
    {
        $user   = request('id');
        $result = DB::table('weeklyevents')->join('participate_event',
            'weeklyevents.event_id', '=', 'participate_event.event_id')
            ->where('participate_event.user_id', $user)
            ->get();

        return response()->json(['data' => $result], 200, [], JSON_NUMERIC_CHECK);
    }

    public function partJoinWeeklyAdmin(Request $request)
    {

        $result = DB::table('weeklyevents')->join('participate_event',
            'weeklyevents.event_id', '=', 'participate_event.event_id')
            ->get();
        return response()->json(['data' => $result], 200, [], JSON_NUMERIC_CHECK);
    }

    public function partJoinSpecial(Request $request)
    {

        $result = DB::table('specialevents')->join('participate_event',
            'specialevents.event_id', '=', 'participate_event.event_id')
            ->get();
        return response()->json(['data' => $result], 200, [], JSON_NUMERIC_CHECK);
    }

}
