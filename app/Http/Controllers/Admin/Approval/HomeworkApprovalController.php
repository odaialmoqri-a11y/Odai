<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */
namespace App\Http\Controllers\Admin\Approval;

use App\Events\Notification\ClassNotificationEvent;
use App\Events\Notification\SingleNotificationEvent;
use App\Http\Requests\HomeworkApprovalRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\HomeworkApproval;
use App\Events\StandardPushEvent;
use App\Events\SinglePushEvent;
use Illuminate\Http\Request;
use App\Traits\EventProcess;
use App\Traits\LogActivity;
use App\Helpers\SiteHelper;
use App\Models\Homework;
use App\Models\User;
use App\Traits\Common;
use Exception;
use Log;

class HomeworkApprovalController extends Controller
{
    use EventProcess;
    use LogActivity;
    use Common;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function approve(HomeworkApprovalRequest $request,$id)
    {
        //
        \DB::beginTransaction();
        try
        {
            $homework             = Homework::where('id',$id)->first();

            $homeworkapproval = HomeworkApproval::where('homework_id',$id)->first();

            $homeworkapproval->comments           =   $request->principal_comments;
            $homeworkapproval->approved_by        =   Auth::id();
            $homeworkapproval->approved_at        =   date('Y-m-d');
            $homeworkapproval->status             =   'approved';

            $homeworkapproval->save();

            $data=[];

            $data['school_id']      =   Auth::user()->school_id;
            $data['standard_id']    =   $homework->standardLink_id;
            $data['message']        =   'New Home Work Added';
            $data['type']           =   'homework';

            event(new StandardPushEvent($data));

            $array = [];

            $array['school_id']         = Auth::user()->school_id;
            $array['standardLink_id']   = $homework->standardLink_id;
            $array['details']           = trans('notification.homework_add_success_msg');  

            event(new ClassNotificationEvent($array)); 

            $message=trans('messages.approve_success_msg',['module' => 'Homework']);

            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $homeworkapproval,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_APPROVE_HOMEWORK,
                $message
            );
            $res['success'] = $message;

            if($homework->teacher_id!=null)
            {
              $this->TeacherNotification($homework->teacher_id,$message);
            }

            \DB::commit();
            return $res;
        }
        catch(Exception $e)
        {
            \DB::rollBack();
            Log::info($e->getMessage());
            dd($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reject(HomeworkApprovalRequest $request, $id)
    {
        //
        \DB::beginTransaction();
        try
        {
            $homework             = Homework::where('id',$id)->first();

            $homeworkapproval = HomeworkApproval::where('homework_id',$id)->first();
            
            $homeworkapproval->comments           =   $request->principal_comments;
            $homeworkapproval->approved_by        =   Auth::id();
            $homeworkapproval->approved_at        =   date('Y-m-d');
            $homeworkapproval->status             =   'rejected';

            $homeworkapproval->save();

            $message=trans('messages.reject_success_msg',['module' => 'Homework']);

            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $homeworkapproval,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_REJECT_HOMEWORK,
                $message
            );
            $res['success'] = $message;
            if($homework->teacher_id!=null)
            {
              $this->TeacherNotification($homework->teacher_id,$message);
            }

            \DB::commit();
            return $res;
        }
        catch(Exception $e)
        {
            \DB::rollBack();
            Log::info($e->getMessage());
            //dd($e->getMessage());
        }
    }

    public function update(HomeworkApprovalRequest $request)
    {
       // dd($request->approvallist);
        try
        {
            foreach ($request->approvallist as  $id) 
            {

                if($request->all_status=='approve')
                {
                       $data=$this->approve($request,$id);
                }
               if($request->all_status=='reject')
                {
                       $data=$this->reject($request,$id);
                }    

            }

            return $data;

        }
        catch(Exception $e)
        {
            Log::info($e->getMessage());
            //dd($e->getMessage());
        }     

    } 

    public function TeacherNotification($teacher_id,$message)
    {
        $school_id      =   Auth::user()->school_id;
        $academic_year  =   SiteHelper::getAcademicYear($school_id);


        $teacher = User::find($teacher_id);
        
        $array=[];
        $array['school_id']  =   Auth::user()->school_id;
        $array['user_id']    =   $teacher->id;
        $array['message']    =   $message;
        $array['type']       =   'homework';

        event(new SinglePushEvent($array));
    


        $data = [];

        $data['user']       =   $teacher;
        $data['details']    =   $message;

        event(new SingleNotificationEvent($data));   

    }

   
}