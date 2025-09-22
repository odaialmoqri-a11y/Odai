<?php
namespace App\Console\Commands\Test;
use Illuminate\Console\Command;
use App\Models\User;
use Log;
use App\Traits\SendPushNotification;
use App\Notifications\SendDeviceNotification;
class CheckPushNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   use SendPushNotification;
    protected $signature = 'gego:pushnotification';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a  Test Notification';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      try{
        $user_id = $this->ask('Enter user id');
        $user_id = intval($user_id);
        $user = User::where('id',$user_id)->first();
            $data=[];
            $data['message']='Test Notification';
            $data['type']       =   'homework';
           // $data['type']       =   'notice';
           // $data['platform_token']=$user->platform_token;
           //  $this->sendNotification($data,$user->platform_token);
            dump($user->platform_token);
             $user->notify(new SendDeviceNotification($data,$user->platform_token));
        $this->info("Notification Send");
    }
    catch(Exception $e){
        Log::info($e->getMesage());
    }
    }
}