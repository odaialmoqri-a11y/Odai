<?php

namespace App\Observers;

use App\Models\SchoolDetail;
use Illuminate\Support\Str;
use App\Models\School;
use Exception;
use Log;

class SchoolObserver
{
    /**
     * Handle the school "created" event.
     *
     * @param  \App\Models\School  $school
     * @return void
     */
    public function created(School $school)
    {
        //
        try
        {
            $slug = Str::slug($school->name,'-');
            $school = School::where('id',$school->id)->first(); 
            $school->slug = $slug;
            $school->save();

            $keys = ['about_us' , 'admission_open' , 'admission_close_message' , 'admission_close_on' , 'affiliation_no' , 'affiliated_by' , 'board' , 'date_of_establishment' , 'landline_no' , 'moto' , 'school_logo' , 'website'];
            foreach ($keys as $key) 
            {
                $detail = SchoolDetail::create([
                    'school_id' =>  $school->id,
                    'meta_key'  =>  $key,
                    'meta_value' => "-",
                ]);
            }
        }
        catch(Exception $e)
        {
            Log::info($e->getMessage());
            //dd($e->getMessage());
        }
    }

    /**
     * Handle the school "updated" event.
     *
     * @param  \App\Models\School  $school
     * @return void
     */
    public function updated(School $school)
    {
        //
    }

    /**
     * Handle the school "deleted" event.
     *
     * @param  \App\Models\School  $school
     * @return void
     */
    public function deleted(School $school)
    {
        //
    }

    /**
     * Handle the school "restored" event.
     *
     * @param  \App\Models\School  $school
     * @return void
     */
    public function restored(School $school)
    {
        //
    }

    /**
     * Handle the school "force deleted" event.
     *
     * @param  \App\Models\School  $school
     * @return void
     */
    public function forceDeleted(School $school)
    {
        //
    }
}