<?php

namespace App\Listeners;

//use App\Events\PushEvent;
use App\Events\TransportNotificationPushEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
//use App\Traits\SendPushNotification;
use App\Models\User;
//use App\Models\CoordinatorIncharge;
use Carbon\Carbon;
use App\Models\StudentParentLink;
use App\Models\RouteStudent;
use Exception;
use Log;
use Auth;
use App\Notifications\SendDeviceNotification;

class TransportNotificationPushListener implements ShouldQueue
{
    //use SendPushNotification;
    
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {       
        //
    }

    /**
     * Handle the event.
     *
     * @param  TransportNotificationPushEvent $event
     * @return void
     */
    public function handle(TransportNotificationPushEvent $event)
    { 
        try{
        //dd('test');
        //dd($event->data['driver_id']);

        //$users = CoordinatorIncharge::where([['route_id', $event->data['route_id']],['user_id', $event->data['driver_id']]])->get();
        //$users = CoordinatorIncharge::where([['route_id', $event->data['route_id']], ['created_at', '>=', Carbon::today()]])->pluck('user_id')->toArray();

        //$users = RouteStudent::where('route_id', $event->data['route_id'])->pluck('user_id')->toArray();
        //dd(count($users->parents));
        //dd($users->students);
        //$students = User::whereIn('id', $users)->with('parents')->get();

       // $students = StudentParentLink::whereIn('student_id', $users)->get();

        //$parentId = $students->parents[0]['parent_id'];

        //dd($students->parents[0]['parent_id']);
        //dd($students);
        $users = RouteStudent::where([['school_id', $event->data['school_id']], ['route_id', $event->data['route_id']]])->with('students')->get();
        //dd($users);
        foreach($users as $user)
        {   //dd($user->students);
            $parents = $user->students->parents;
            //dd(count($user->students->parents));
           //dd($user->students->parents[0]['userParent']);

            foreach($parents as $parent)
            {   //dd($parent->userParent);
                if($event->data['trip_name'] != 'others')
                {   
                    if(isset($parent->userParent->platform_token))
                    {
                        
                       // $this->sendNotification($event->data,$parent->userParent->platform_token);
                        $parent->userParent->notify(new SendDeviceNotification($event->data,$parent->userParent->platform_token));
                    }
                }

                /*if($event->data['type'] == 'LiveLocation')
                {   
                    if(isset($parent->userParent->platform_token))
                    {
                        $this->sendLiveLocationNotification($event->data,$parent->userParent->platform_token);
                    }
                }*/
            }
        }

       // Mail::to($event->queue->to)->queue(new ReminderMail($event->queue));
    }
    catch(Exception $e)
    {
        Log::info($e->getMessage());
    }

    }
}