<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;
use App\Models\TeacherProfile;
use App\Helpers\SiteHelper;
use App\Models\User;
use Exception;

class TeacherProfileObserver
{
    /**
     * Handle the teacherprofile "created" event.
     *
     * @param  \App\Models\TeacherProfile  $teacherprofile
     * @return void
     */
    public function created(TeacherProfile $teacherprofile)
    {
        $academic_year = SiteHelper::getAcademicYear($teacherprofile->school_id);
        Cache::forget('hod_list_'.$teacherprofile->school_id.'_'.$academic_year->id);
        Cache::forget('principal_list_'.$teacherprofile->school_id.'_'.$academic_year->id);
        try
        {
            if($teacherprofile->reporting_to == null)
            {
                $user_id = User::whereHas('teacherprofile',function ($query) {
                    $query->where('designation','head_of_the_department')->orWhere('designation','principal')->orWhere('designation','vice_principal');
                })->pluck('id')->toArray();

                if($user_id!=null && count($user_id)>0){

                $teacherprofile = TeacherProfile::where('id',$teacherprofile->id)->first();

                $teacherprofile->reporting_to = $user_id[array_rand($user_id, 1)]; 

                $teacherprofile->save();
               }
            }
        }
        catch(Exception $e)
        {
            //dd($e->getMessage());
        }
    }

    /**
     * Handle the teacherprofile "updated" event.
     *
     * @param  \App\Models\TeacherProfile  $teacherprofile
     * @return void
     */
    public function updated(TeacherProfile $teacherprofile)
    {
        $academic_year = SiteHelper::getAcademicYear($teacherprofile->school_id);
        Cache::forget('hod_list_'.$teacherprofile->school_id.'_'.$academic_year->id);
        Cache::forget('principal_list_'.$teacherprofile->school_id.'_'.$academic_year->id);
    }

    /**
     * Handle the teacherprofile "deleted" event.
     *
     * @param  \App\Models\TeacherProfile  $teacherprofile
     * @return void
     */
    public function deleted(TeacherProfile $teacherprofile)
    {
        //
    }

    /**
     * Handle the teacherprofile "restored" event.
     *
     * @param  \App\Models\TeacherProfile  $teacherprofile
     * @return void
     */
    public function restored(TeacherProfile $teacherprofile)
    {
        //
    }

    /**
     * Handle the teacherprofile "force deleted" event.
     *
     * @param  \App\Models\TeacherProfile  $teacherprofile
     * @return void
     */
    public function forceDeleted(TeacherProfile $teacherprofile)
    {
        //
    }
}