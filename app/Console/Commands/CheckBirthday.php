<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\EventProcess;
use App\Traits\AutoPostProcess;
use App\Traits\Common;
use App\Models\Userprofile;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SiteHelper;
use App\Models\User;
use App\Models\Events;
use Exception;

class CheckBirthday extends Command
{

    use EventProcess;
    use AutoPostProcess;
    use Common;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gego:checkbirthday';

    //php artisan gego:checkbirthday 16-10-2020

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Birthday';

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

      //  $argument=$this->argument('date');
    // $to= $this->argument('email');
        $argument=NULL;
        try
        {

           /* if($argument==NULL)
            {

                $birthdays=User::whereHas('userprofile', function($query) 
                { 

                    $query->WhereRaw("DATE_FORMAT(date_of_birth, '%m-%d') = DATE_FORMAT(now(),'%m-%d')")->where('status','active');
                })->where(function($query) {
                    $query->where('usergroup_id',5)
                                ->orWhere('usergroup_id',6)
                                ->orWhere('usergroup_id',8)
                                ->orWhere('usergroup_id',11);
                })->get();

            }

            else
            {
                $month_date=date('m-d',strtotime($argument));
                //dd($month_date);
                $birthdays=User::whereHas('userprofile', function($query) use ($argument)
                { 

                    $query->WhereRaw("DATE_FORMAT(date_of_birth, '%m-%d') = DATE_FORMAT($argument,'%m-%d')")->where('status','active');
                })->where(function($query) {
                    $query->where('usergroup_id',5)
                                ->orWhere('usergroup_id',6)
                                ->orWhere('usergroup_id',8)
                                ->orWhere('usergroup_id',11);
                })->get();


            }
*/
             $birthdays=User::whereHas('userprofile', function($query) use ($argument)
                { 

                    $query->WhereRaw("DATE_FORMAT(date_of_birth, '%m-%d') = DATE_FORMAT(DATE_ADD(now(), INTERVAL 1 DAY),'%m-%d')")->where('status','active');
                })->where(function($query) {
                    $query->where('usergroup_id',5)
                                ->orWhere('usergroup_id',6)
                                ->orWhere('usergroup_id',8)
                                ->orWhere('usergroup_id',11);
                })->get();

            //dump($birthdays);


            if(count($birthdays)>0)
            {
                foreach($birthdays as $birthday)
                {//dump($birthday);

                    $date_of_birth = date('Y-m-d',strtotime($birthday->userprofile->date_of_birth));
                    $month = date('m-d',strtotime($birthday->userprofile->date_of_birth));
                    $current_year=date('Y');
                    $birth_date = $current_year.'-'.$month;

                    //dd($birth_date);

                    $date=date('Y-m-d H:i:s');
                    //dd($date);

                    $school_id=$birthday->userprofile->school_id;

                    $image = $this->getFilePath('uploads/images/birthday.jpg');

                    //dump($image);

                    $event = $this->CreateBirthday($birthday,$school_id,$birth_date,$date,$argument,$image);
               
                                    
                }

          
            }
        }
        catch(Exception $e)
        {
            //dd($e->getMessage());
        }
    
    }

}
