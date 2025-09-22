<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */
namespace App\Http\Controllers\Admin;

use App\Http\Resources\ShowEventGallery as ShowEventGalleryResource;
use App\Http\Resources\EditEvent as EditEventResource;
use App\Http\Resources\ShowEvent as ShowEventResource;
use App\Events\Notification\SchoolNotificationEvent;
use App\Events\Notification\ClassNotificationEvent;
use App\Http\Requests\EventUpdateRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Traits\SendPushNotification;
use App\Http\Requests\EventRequest;
use App\Events\StandardPushEvent;
use App\Traits\ReminderProcess;
use App\Events\ReminderEvent;
use App\Events\CalendarEvent;
use Illuminate\Http\Request;
use App\Traits\EventProcess;
use App\Models\StandardLink;
use App\Models\EventGallery;
use App\Models\Subscription;
use App\Models\ExamSchedule;
use App\Models\SchoolDetail;
use App\Helpers\SiteHelper;
use App\Traits\LogActivity;
use App\Events\PushEvent;
use App\Models\Subject;
use App\Traits\Common;
use App\Models\Events;
use App\Models\Exam;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Log;

class EventsController extends Controller
{
    use SendPushNotification;
    use ReminderProcess;
    use EventProcess;
    use LogActivity;
    use Common;

    function index(Request $request)
    {
        //
        $school_id      =   Auth::user()->school_id;
        $academic_year  =   SiteHelper::getAcademicYear($school_id);
        $events         =   Events::where([['school_id',$school_id],['academic_year_id',$academic_year->id],['status','active']]);
        $count          =   Events::where([['school_id',$school_id],['academic_year_id',$academic_year->id],['category','!=','holidays'],['status','active']])->count();
        $subscription   =   Subscription::where('school_id',$school_id)->first();

        if(count((array)\Request::getQueryString())>0)
        {
            if($request->standardLink_id != '')
            { 
                $events = $events->where('standard_id',$request->standardLink_id);
            }
        }

        //new 
        if(!config('gexam.enabled', false))
        {
            $events =$events->where('category','!=','exam');
        }

        $events = $events->get();

        $events = $events->map(function( $event, $key) {
            $eventData = [ 
                'id'        =>  $event->id,
                'title'     =>  $event->title, 
                'start'     =>  date('Y-m-d', strtotime($event->start_date)).'T'.date('H:i:s', strtotime($event->start_date)),
                'end'       =>  date('Y-m-d', strtotime($event->end_date)).'T'.date('H:i:s', strtotime($event->end_date)),
                'allDay'    =>  $event->allDay,
                'select_type'=>$event->select_type,
                'color'=>$event->color,
            ];
            return $eventData;
        });
        $events = json_encode($events);

        $standard=$request->standardLink_id;
       
        return view('admin.events.index',['events'=>$events , 'count'=>$count , 'subscription'=>$subscription,'standard'=>$standard]);
    }

    public function list()
    {
        $school = SchoolDetail::where('school_id',Auth::user()->school_id)->where('meta_key','date_of_establishment')->first();

       // dd($school['meta_value']);

        //$end_date = Carbon::parse($school['meta_value'])->format('Y');
        $end_date = date('Y');

        $start_date=date('Y');

        $array = [];

        $array['standardlist']  = SiteHelper::getStandardLinkList(Auth::user()->school_id);
        $array['start']         = $start_date;
        $array['end']           = $end_date;

        return $array;
    }

    /**dd
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    function store(EventRequest $request)//Event
    {
        try
        {
            $school_id      =   Auth::user()->school_id;
            $academic_year  =   SiteHelper::getAcademicYear($school_id);

            $events= new Events();

            $events->school_id        = $school_id;
            $events->academic_year_id = $academic_year->id;

            //$path = $this->imagePath($category,$image);
            /* $file = $request->file('image');
            if($file)
            {
                $name = $file->getClientOriginalName();
                $path = $this->uploadFile(Auth::user()->school->slug.'/uploads/admin/event/image',$file,'public');  
            }
            else
            {
                $path = '';
            }*/

            $events->select_type  = $request->select_type;
            $events->title        = $request->title;
            $events->description  = $request->description;
            $events->repeats      = $request->repeats;
            if($request->select_type=='class')
            {
                $events->standard_id     = $request->standard_id;
            }

            if($request->select_type=='alumni')
            {
                $events->batch     = $request->batch;
            }
            $events->freq         = $request->freq;
            // if( $request->freq_term!='')
            //$events->freq_term    = $request->freq_term;
            $events->location     = $request->location;
            $events->category     = $request->category;
            $events->organised_by = $request->organised_by;
            //$events->image        = $path;
            $events->start_date   = date('Y-m-d H:i:s',strtotime($request->start_date));
            $events->end_date     = date('Y-m-d H:i:s',strtotime($request->end_date));

            if($events->select_type=='class')
            {
                $events->color='blue';
            }
            else
            {
                $events->color='green';
            }

            $events->save();
       
            $executed_at  =  date('Y-m-d', strtotime('-2 days', strtotime($events->start_date)));

            /*  $this->sendToReminderEvent($events,$executed_at,'first');
            if(env('MAIL_STATUS') == 'on')
            {
                event(new CalendarEvent($events));
            }*/

            // if($request->select_type=='class')
            // {
            //     $data=[];

            //     $data['school_id']=Auth::user()->school_id;
            //     $data['standard_id']=$request->standard_id;
            //     $data['message']='New Event created';
            //     $data['type']='event';

            //     event(new StandardPushEvent($data));

            //     $array = [];

            //     $array['school_id']         = Auth::user()->school_id;
            //     $array['standardLink_id']   = $request->standard_id;
            //     $array['details']           = trans('notification.event_add_success_msg');  

            //     event(new ClassNotificationEvent($array)); 
            // }
            // else
            // {
            //     $data=[];

            //     $data['school_id']=Auth::user()->school_id;
            //     $data['message']='New Event created';
            //     $data['type']='event';

            //     event(new PushEvent($data));

            //     $array = [];

            //     $array['school_id'] = Auth::user()->school_id;
            //     $array['details'] = trans('notification.event_add_success_msg');
            
            //     event(new SchoolNotificationEvent($array));
            // }
       
            $message=trans('messages.add_success_msg',['module' => 'Event']);

            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $events,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_ADD_EVENT,
                $message
            ); 

            $res['success']=$message;
            return $res;  
        }
        catch(Exception $e)
        {
            Log::info($e->getMessage());
            //dd($e->getMessage());
        }        
    }

    /**
     * @param $id
     */
    function edit($id)
    {
        $event = Events::where([['id',$id],['category','!=','holidays']])->get();
        $event = EditEventResource::collection($event);

        return $event;
    }

    function validateedit(EventUpdateRequest $request)
    {
        //
    }

    /**
     * @param Request $request
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    function update(Request $request, $id)
    {
        try
        {
            $events = Events::where('id',$id)->first();

            /*if(Input::hasFile('image'))
            {
                $file = $request->file('image');
                //$name = $file->getClientOriginalName();
                $path = $this->uploadFile(Auth::user()->school->slug.'/uploads/admin/event/image',$file);
                $events->image = $path;  
            }
            else
            {
                $events->image = $events->image;
            }*/

            $events->title       = $request->title;
            $events->description = $request->description;
            $events->repeats     = $request->repeats;
            if($request->select_type=='class')
            {
                $events->standard_id     = $request->standard_id;
            }

            if($request->select_type=='alumni')
            {
                $events->batch     = $request->batch;
            }
      
            $events->freq        = $request->freq;
            $events->freq_term   = $request->freq_term;
            $events->location    = $request->location;
            $events->category    = $request->category;
            $events->organised_by= $request->organised_by;
            $events->start_date  = date('Y-m-d H:i:s',strtotime($request->start_date));
            $events->end_date    = date('Y-m-d H:i:s',strtotime($request->end_date));

            if($events->select_type=='class')
            {
                $events->color='blue';
            }
            else
            {
                $events->color='green';
            }
               
            $events->save();

            if($request->select_type=='class')
            {
                $data=[];

                $data['school_id']=Auth::user()->school_id;
                $data['standard_id']=$request->standard_id;
                $data['message']='Event updated';
                $data['type']='event';

                event(new StandardPushEvent($data));

                $array = [];

                $array['school_id']         = Auth::user()->school_id;
                $array['standardLink_id']   = $request->standard_id;
                $array['details']           = trans('notification.event_update_success_msg');  

                event(new ClassNotificationEvent($array)); 
            }
            else
            {
                $data=[];

                $data['school_id']=Auth::user()->school_id;
                $data['message']='Event updated';
                $data['type']='event';

                event(new PushEvent($data));

                $array = [];

                $array['school_id'] = Auth::user()->school_id;
                $array['details'] = trans('notification.event_update_success_msg');
            
                event(new SchoolNotificationEvent($array));
            }  

            $message=trans('messages.update_success_msg',['module' => 'Event']);
           
            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $events,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_EDIT_EVENT,
                $message
            );

            $res['success']=$message;
            return $res;
        }
        catch(Exception $e)
        {
            Log::info($e->getMessage());
            //dd($e->getMessage());
        }
    }

    public function eventapprove($id){


         $event = Events::findOrFail($id);

        $event->status='active';

       if($event->save()){
           if($event->select_type=='class')
            {
                $data=[];

                $data['school_id']=$event->school_id;
                $data['standard_id']=$event->standard_id;
                $data['message']='New Event created';
                $data['type']='event';

                event(new StandardPushEvent($data));

                $array = [];

                $array['school_id']         = $event->school_id;
                $array['standardLink_id']   = $event->standard_id;
                $array['details']           = trans('notification.event_add_success_msg');  

                event(new ClassNotificationEvent($array)); 
            }
            else
            {
                $data=[];

                $data['school_id']=$event->school_id;
                $data['message']='New Event created';
                $data['type']='event';

                event(new PushEvent($data));

                $array = [];

                $array['school_id'] = $event->school_id;
                $array['details'] = trans('notification.event_add_success_msg');
            
                event(new SchoolNotificationEvent($array));
            }


            return redirect('/admin/dashboard')->with('successmessage',"Event has been approved successfully");

        }

    }

    function changeevent(Request $request, $id)
    {
        $event = Events::findOrFail($id);

        if ($request->end_date == 'undefined')
            $request['end_date'] = date('Y-m-d H:i:s', strtotime($request->start_date));

        if($request->start_date == $request->end_date)
            $request['allDay']=1;

        $event->fill($request->all());
        $event->save();
        echo json_encode(['status' => 'Event has been update']);
    }

    public function destroy($id)
    {
        try
        {
            $event = Events::where('id',$id)->first();
       
            $event->delete();

            $message=trans('messages.delete_success_msg',['module' => 'Event']);
       
            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $event,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_DELETE_EVENT,
                $message
            );
            return redirect('/admin/events')->with('successmessage',$message);
        }
        catch(Exception $e)
        {
            Log::info($e->getMessage());
            //dd($e->getMessage());
        } 
    }

    /**
     * @param $facility
     * @param $asset
     * @return string
     */
    function events()
    {
        //
        $school_id      =   Auth::user()->school_id;
        $academic_year  =   SiteHelper::getAcademicYear($school_id);

        $events = Events::where([['school_id',$school_id],['academic_year_id',$academic_year->id]])->get();

        $items = array();

        foreach ($events as $event) 
        {
            if ($event->repeats == 1) 
            {
                //create multiple entries for repeating events
                //count days from start to end and repeat
                if ($event->freq_term == 'day') 
                {
                    foreach ($this->getDailyTasks($event) as $s) 
                    {
                        array_push($items, $s);
                    }
                }

                if ($event->freq_term == 'week') 
                {
                    foreach ($this->getWeeklyTasks($event) as $s) 
                    {
                        array_push($items, $s);
                    }
                }

                if ($event->freq_term == 'month') 
                {
                    foreach ($this->getMonthlyTasks($event) as $s) 
                    {
                        array_push($items, $s);
                    }
                }

                if ($event->freq_term == 'year') 
                {
                    foreach ($this->getYearlyTasks($event) as $s) 
                    {
                        array_push($items, $s);
                    }
                }
            } 
            else 
            {
                foreach ($this->getDayTask($event) as $s) 
                {
                    array_push($items, $s);
                }
            }
        }
        return $items;
    }

    /**
     * @param $event
     * @param $start
     * @param $end
     * @return array
     */
    function getEvent($event,$start,$end)
    {
        $repeats_class='repeatsclass';
        if($event->repeats==1)
        {
            $repeats_class='repeats_class';
        }

        return array(
            'id'           => (int)$event->id,
            'school_id'    => $event->school_id,
            'academic_year_id'    => $event->academic_year_id,
            'select_type'  => $event->select_type,
            'title'        => $event->title,
            'description'  => $event->description,
            'repeats'      => $event->repeats,
            'standard_id'  => $event->standard_id,
            'freq'         => $event->freq,
            'freq_term'    => $event->freq_term,
            'location'     => $event->location,
            'category'     => $event->category,
            'organised_by' => $event->organised_by,
            'image'        => $event->image,
            'start'        => $start->format('Y-m-d H:i:s'),
            'end'          => $end->format('Y-m-d H:i:s'),
            'color'          => $event->color,
            'repeats_class'=> $repeats_class,  
        );
    }

    /**
     * single day task
     * @param $event
     * @return array
     */
    function getDayTask($event)
    {
        $end   = Carbon::parse($event->end_date);
        $start = Carbon::parse($event->start_date);

        $events[] =$this->getEvent($event,$start,$end);
        return $events;
    }

    /**
     * repeating tasks even (n) days. Note if you can even put 7 days to make them weekly.
     *
     * @param $event
     * @return array
     */
    function getDailyTasks($event)
    {
        $end   = Carbon::parse($event->end_date);
        $start = Carbon::parse($event->start_date);

        $days  = $end->diffInDays($start);

        $events = array();
        $date   = $start;
        for ($i = 1; $i <= $days + 1; $i++) {
            if ($event->status == 'completed')
                continue;

            $events[] = $this->getEvent($event,$date,$date);
            $date     = Carbon::parse($date)->addDays($event->freq);

        }
        return $events;
    }

    /**
     * Weekly events repeating every (n) weeks
     * @param $event
     * @return array
     */
    function getWeeklyTasks($event)
    {
        $end   = Carbon::parse($event->end_date);
        $start = Carbon::parse($event->start_date);

        $weeks = $end->diffInWeeks($start);

        $events = array();
        $date   = $start;
        for ($i = 1; $i <= $weeks + 1; $i++) {
            //skip completed.
            if ($event->status == 'completed')
                continue;

            $events[] = $this->getEvent($event,$date,$date);
            $date     = Carbon::parse($date)->addWeeks($event->freq);
        }
        return $events;

    }

    /**
     * Monthly events repeating every (n) months
     * @param $event
     * @return array
     */
    function getMonthlyTasks($event)
    {
        $end   = Carbon::parse($event->end_date);
        $start = Carbon::parse($event->start_date);

        $months = $end->diffInWeeks($start);

        $events = array();
        $date   = $start;
        //daily tasks
        for ($i = 1; $i <= $months + 1; $i++) {
            //skip completed.
            if ($event->status == 'completed')
                continue;

            $events[] = $this->getEvent($event,$date,$date);
            $date     = Carbon::parse($date)->addMonths($event->freq);
        }
        return $events;
    }

    /**
     * yearly repeating events repeats every (n) years
     *
     * @param $event
     * @return array
     */
    function getYearlyTasks($event)
    {
        $end   = Carbon::parse($event->end_date);
        $start = Carbon::parse($event->start_date);

        $years = $end->diffInYears($start);

        $events = array();
        $date   = $start;
        //daily tasks
        for ($i = 1; $i <= $years + 1; $i++) {
            //skip completed.
            if ($event->status == 'completed')
                continue;

            $events[] =$this->getEvent($event,$date,$date);
            $date     = Carbon::parse($date)->addYears($event->freq);
        }
        return $events;
    }

    function show($id)
    {
        $event = Events::where('id',$id)->first();

        $now=date('Y-m-d H:i:s');

        // if($event->category != 'holidays')
        // {
        //     $exam=Exam::where('name',$event->title)->where('standard_id',$event->standard_id)->first();

        //     $schedule=ExamSchedule::where('exam_id',$exam->id)->first();
        //     $subject=Subject::where('id',$schedule->subject_id)->first();
        //     //$start=$event->start_date;
        //     $subject_name=$subject->name;
        //     $start=\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$event->start_date);

        //     $end=\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$event->end_date);
        //     $diff_in_hours = $end->diffInHours($start);

        //     $duration=$diff_in_hours*60;
        //     if($event->category=='exam')
        //     {
        //         return view('admin.events.detail',['event'=>$event,'duration'=>$duration,'subject_name'=>$subject_name,'now'=>$now]);
        //     }
        //     else
        //     {
        //         return view('admin.events.show',['event'=>$event,'now'=>$now]);
        //     }
        // }
        if($event->category != 'holidays')
        {
            
            if($event->category=='exam')
            {
                //Added in if condition
                if(class_exists('Gegok12\Exam\Models\Exam'))
                {
                    $exam=\Gegok12\Exam\Models\Exam::where('name',$event->title)->where('standard_id',$event->standard_id)->first();
                    $schedule=\Gegok12\Exam\Models\ExamSchedule::where('exam_id',$exam->id)->first();
                }
                else
                {
                    $exam=Exam::where('name',$event->title)->where('standard_id',$event->standard_id)->first();
                    $schedule=ExamSchedule::where('exam_id',$exam->id)->first();
                }

                
                $subject=Subject::where('id',$schedule->subject_id)->first();
                //$start=$event->start_date;
                $subject_name=$subject->name;
                $start=\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$event->start_date);

                $end=\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$event->end_date);
                $diff_in_hours = $end->diffInHours($start);

                $duration=$diff_in_hours*60;
                //end
                return view('admin.events.detail',['event'=>$event,'duration'=>$duration,'subject_name'=>$subject_name,'now'=>$now]);
            }
            else
            {
                return view('admin.events.show',['event'=>$event,'now'=>$now]);
            }
        }
        else
        {
            abort(403);
        } 
    }

    function showdetails($id)
    {
        $event = Events::where([['id',$id],['school_id',Auth::user()->school_id]])->get();
        $event = ShowEventResource::collection($event);
        return $event;
    }

    public function showimage($event_id)
    { 
        $event = EventGallery::where([['event_id',$event_id],['school_id',Auth::user()->school_id]])->get();
        
        $event = ShowEventGalleryResource::collection($event);
        return $event;
    }

    public function details($id)
    {
        $event=Events::where('id',$id)->first();

        if(Gate::allows('event',$event))
        {
            $array=[];
            if($event->category == 'holidays')
            {
                $array['id']=$event->id;
                $array['title'] = $event->title;
                $array['start_date']=date('d-F-Y',strtotime($event->start_date));
                $array['end_date']=$event->end_date;
                $array['category']=$event->category;
            }
            else
            {
                $array['id']=$event->id;
                $array['select_type']=$event->select_type;
                $array['title']=$event->title;
                $array['description']=$event->description;
                $array['repeats']=$event->repeats;
                if($array['repeats']=='yes')
                {
                    $array['freq']=$event->freq; 
                    $array['freq_term']=$event->freq_term;
                }
                $array['standard_id']=$event->standardlink->StandardSection;
                $array['location']=$event->location;
                $array['category']=$event->category;
                $array['organised_by']=$event->organised_by;
                $array['image']=$event->ImagePath;
                $array['start_date']=date('d-F-Y',strtotime($event->start_date));
                $array['end_date']=$event->end_date;
            }

            return $array;
        }
        else
        {
            abort(403);
        }
    }
}