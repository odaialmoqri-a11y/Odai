<?php

namespace App\Listeners;

use App\Events\SendMessageEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Traits\SendMessageProcess;
use App\Traits\LogActivity;
use App\Traits\Common;
use App\Models\User;

class SendMessageEventListener  
{
    use SendMessageProcess;
    use LogActivity;
    use Common;

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
     * @param  SendMessageEvent  $event
     * @return void
     */
    public function handle(SendMessageEvent $event)
    {
        //
        foreach($event->request->selected as $user_id)
        {
            foreach($user_id as $parent_id)
            {
                foreach($event->request->selectedUsers as $student_id)
                {
                    $student = User::where([['usergroup_id',6],['id',$student_id]])->first();
                }
                $user = User::where([['usergroup_id',7],['id',$parent_id]])->first();
                $send = $this->selectSendMessage($event->request , $event->school_id , $event->admin_email , $user , $event->admin , $student);
            }
        }
    }
}