<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use App\Observers\TeacherProfileObserver;
use App\Observers\AcademicYearObserver;
use App\Observers\StandardLinkObserver;
use Illuminate\Support\ServiceProvider;

use App\Observers\UserprofileObserver;

use Laravel\Dusk\DuskServiceProvider;
use App\Observers\HomeworkObserver;
use App\Observers\BulletinObserver;

use App\Observers\SchoolObserver;
use App\Observers\EventObserver;
use App\Observers\TaskObserver;
use App\Observers\UserObserver;

use App\Models\TeacherProfile;
use App\Models\AcademicYear;
use App\Models\StandardLink;

use App\Models\Userprofile;

use App\Models\Bulletin;

use App\Models\Homework;
use App\Models\Setting;
use App\Models\Events;
use App\Models\School;

use App\Models\User;
use App\Models\Task;
use Schema;
use Config;
use App;
use Illuminate\Pagination\Paginator;
// Importing DuskServiceProvider class

class AppServiceProvider extends ServiceProvider {
    /**
    * Bootstrap any application services.
    *
    * @return void
    */

    public function boot() { 


     Validator::extend('check_logoutdevice_id', function ($attribute, $value, $parameters, $validator) 
        {
            $inputs = $validator->getData();
            $email = $inputs['email'];
            $user = User::where('mobile_no', $email)->where('usergroup_id',7)->first();
            
            if ($user!=null) 
            {
                if ($user->device_id == null) 
                {

                    return true;
                }
                return false;
            }
        });     

        Validator::extend('teacher_logoutdevice_id', function ($attribute, $value, $parameters, $validator) 
        {
            $inputs = $validator->getData();
            $email = $inputs['email'];
            $user = User::where('mobile_no', $email)->where('usergroup_id',5)->first();
            
            if ($user!=null) 
            {
                if ($user->device_id == null) 
                {

                    return true;
                }
                return false;
            }
        });        

        Events::observe( EventObserver::class );
        Bulletin::observe( BulletinObserver::class );
        Homework::observe( HomeworkObserver::class );
        Userprofile::observe( UserprofileObserver::class );
        TeacherProfile::observe( TeacherProfileObserver::class );
        School::observe( SchoolObserver::class );
 

       
        StandardLink::observe(StandardLinkObserver::class);
        User::observe(UserObserver::class);
        Task::observe(TaskObserver::class);
        AcademicYear::observe(AcademicYearObserver::class); //new

        //hide to receive mail
        if ( version_compare( PHP_VERSION, '7.2.0', '>=' ) ) {
            // Ignores notices and reports all other kinds... and warnings
            error_reporting( E_ALL ^ E_NOTICE ^ E_WARNING );
            // error_reporting( E_ALL ^ E_WARNING );
            // Maybe this is enough
        }
        //

        if ( !\App::runningInConsole() && count( Schema::getColumnListing( 'settings' ) ) ) {

            $settings = Setting::all();

            foreach ( $settings as $key => $setting ) {
                Config::set( 'settings.'.$setting->key, $setting->value );
            }
        }

        Paginator::useBootstrap();
    }

    /**
    * Register any application services.
    *
    * @return void
    */
    public function register() {

    }
}