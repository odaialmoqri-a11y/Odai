<?php

namespace App\Observers;

use App\Models\Events;
use Illuminate\Support\Facades\Auth;
class EventObserver
{
    /**
     * Handle the events "created" event.
     *
     * @param  \App\Models\Events  $events
     * @return void
     */
    public function created(Events $events)
    {
        //
       /* $update=[
        'created_by'=>Auth::id(),
        'updated_by'=>Auth::id(),
        ];
        //dd($update);
        Events::where('id',$events->id)->update($update);*/
    }

    /**
     * Handle the events "updated" event.
     *
     * @param  \App\Models\Events  $events
     * @return void
     */
    public function updated(Events $events)
    {
        /* $update=[
        'updated_by'=>Auth::id(),
        ];
        Events::where('id',$events->id)->update($update);*/
    }

    /**
     * Handle the events "deleted" event.
     *
     * @param  \App\Models\Events  $events
     * @return void
     */
    public function deleted(Events $events)
    {
        //
    }

    /**
     * Handle the events "restored" event.
     *
     * @param  \App\Models\Events  $events
     * @return void
     */
    public function restored(Events $events)
    {
        //
    }

    /**
     * Handle the events "force deleted" event.
     *
     * @param  \App\Models\Events  $events
     * @return void
     */
    public function forceDeleted(Events $events)
    {
        //
    }
}
