<?php

namespace App\Listeners\Notification;

use App\Events\Notification\TransportNotificationEvent;
use App\Notifications\NewMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use Notification;
use App\Traits\SendPushNotification;
use Exception;
use Log;
use App\Models\RouteStudent;

class TransportNotificationEventListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //dd('testlis');
    }

    /**
     * Handle the event.
     *
     * @param  TransportNotificationEvent  $event
     * @return void
     */
    public function handle(TransportNotificationEvent $event)
    {//dd('lis');
        try{

            $users = RouteStudent::where('route_id', $event->data['route_id'])->pluck('user_id')->toArray();
        //dd($users);
        $students = User::whereIn('id', $users)->with('parents')->get();
       // $students = StudentParentLink::whereIn('student_id', $users)->get();

        //$parentId = $students->parents[0]['parent_id'];

        //dd($students->parents[0]['parent_id']);
        //dd($students);
        foreach($students as $student)
        {
            $parentId1 = $student->parents[0]['parent_id'];
            $parentId2 = $student->parents[1]['parent_id'];
            //dd($parentId);
            $parents = User::whereIn('id', [$parentId1, $parentId2])->get();
            //dd($parents);
            foreach($parents as $parent)
            {   
                if($event->data['trip_name'] != 'others')
                {
                    Notification::send($parent, new NewMessageNotification($event->data['details']));
                }
            }
        }
        }
        catch(Exception $e){
            Log::info($e->getMessage());
        }
    }
}