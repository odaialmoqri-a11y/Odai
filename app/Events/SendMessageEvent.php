<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\SendMail;

class SendMessageEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;
    public $school_id;
    public $admin_email;
    public $admin;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request,$school_id,$admin_email,$admin)
    {
        //
        $this->request = $request;
        $this->school_id = $school_id;
        $this->admin_email = $admin_email;
        $this->admin = $admin;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
