<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */
namespace App\Http\Controllers\Admin;

use App\Http\Resources\StudentHomework as StudentHomeworkResource;
use App\Events\Notification\SingleNotificationEvent;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Events\SinglePushEvent;
use App\Models\StudentHomework;
use Illuminate\Http\Request;
use App\Traits\LogActivity;
use App\Traits\Common;
use App\Models\User;
use Exception;
use Log;

class StudentHomeworkController extends Controller
{
    use LogActivity;
    use Common;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list($id)
    {
        //
        $studentHomeworks = StudentHomework::where('homework_id',$id)->paginate(10);

        $studentHomeworks = StudentHomeworkResource::collection($studentHomeworks);

        return $studentHomeworks;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $studentHomework = StudentHomework::where('id',$id)->first();

        $array = [];

        $array['id']            =   $studentHomework->id;
        $array['homework_id']   =   $studentHomework->homework_id;
        $array['student_id']    =   $studentHomework->user_id;
        $array['attachments']   =   $studentHomework->AttachmentPath;

        return $array;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        try
        {
            $studentHomework = StudentHomework::where('id',$id)->first();

            $studentHomework->comments      = $request->comments;
            $studentHomework->checked_by    = Auth::id();
            $studentHomework->checked_on    = date('Y-m-d');
            $studentHomework->status        = 'checked';

            $studentHomework->save();

            $student = User::where('id',$studentHomework->user_id)->first();
            foreach ($studentHomework->student->parents as $parent) 
            {
                $array=[];

                $array['school_id']  =  Auth::user()->school_id;
                $array['user_id']    =  $parent->userParent->id;
                $array['message']    =  'Homework Checked';
                $array['type']       =  'homework';

                event(new SinglePushEvent($array));
            }

            $data = [];

            $data['user']       =   $student;
            $data['details']    =   trans('notification.student_homework_check_msg');

            event(new SingleNotificationEvent($data));

            $message=trans('messages.homework_check_success_msg');

            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $studentHomework,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_CHECK_HOMEWORK,
                $message
            );

            $res['success'] = $message;
            return $res;
        }
        catch(Exception $e)
        {
            Log::info($e->getMessage());
            //dd($e->getMessage());
        }
    }
}