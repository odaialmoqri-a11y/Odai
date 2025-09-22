<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;
use App\Models\StandardLink;
use App\Helpers\SiteHelper;

class StandardLinkObserver
{
    /**
     * Handle the standard link "created" event.
     *
     * @param  \App\Models\StandardLink  $standardLink
     * @return void
     */
    public function created(StandardLink $standardLink)
    {
        //
        $academic_year = SiteHelper::getAcademicYear($standardLink->school_id);
        Cache::forget('standardLink'.$standardLink->school_id);
        Cache::forget('standard_subject_list_'.$standardLink->school_id.'_'.$academic_year->id);
    }

    /**
     * Handle the standard link "updated" event.
     *
     * @param  \App\Models\StandardLink  $standardLink
     * @return void
     */
    public function updated(StandardLink $standardLink)
    {
        //
        $academic_year = SiteHelper::getAcademicYear($standardLink->school_id);
        Cache::forget('standardLink'.$standardLink->school_id);
        Cache::forget('standard_subject_list_'.$standardLink->school_id.'_'.$academic_year->id);
    }

    /**
     * Handle the standard link "deleted" event.
     *
     * @param  \App\Models\StandardLink  $standardLink
     * @return void
     */
    public function deleted(StandardLink $standardLink)
    {
        //
    }

    /**
     * Handle the standard link "restored" event.
     *
     * @param  \App\Models\StandardLink  $standardLink
     * @return void
     */
    public function restored(StandardLink $standardLink)
    {
        //
    }

    /**
     * Handle the standard link "force deleted" event.
     *
     * @param  \App\Models\StandardLink  $standardLink
     * @return void
     */
    public function forceDeleted(StandardLink $standardLink)
    {
        //
    }
}