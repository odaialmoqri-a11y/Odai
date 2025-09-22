<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */
namespace App\Http\Controllers\Teacher;

use App\Http\Resources\StudentAssignment as StudentAssignmentResource;
use App\Http\Requests\StudentAssignmentUpdateRequest;
use App\Events\Notification\SingleNotificationEvent;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\StudentAssignment;
use App\Events\SinglePushEvent;
use Illuminate\Http\Request;
use App\Traits\LogActivity;
use App\Helpers\SiteHelper;
use App\Models\Assignment;
use App\Traits\Common;
use Exception;

class StudentAssignmentController extends Controller
{
    use LogActivity;
    use Common;

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAssignmentList(Request $request,$id)
    {
        //
        $school_id = Auth::user()->school_id;
        $academic_year = SiteHelper::getAcademicYear($school_id);
        $teacher_id = Auth::id();
        $studentAssignment  = StudentAssignment::with('assignment')->whereHas('assignment',function ($query) use ($school_id, $academic_year, $teacher_id) {
            $query->where([
                ['school_id',$school_id],
                ['academic_year_id',$academic_year->id],
                ['teacher_id',$teacher_id]
            ]);
        })->where([['assignment_id',$id],['status','submitted']]);
        if(\Request::getQueryString() != null)
        {
            if( $request->showCompleted == 'true')
            { 
                $studentAssignment = $studentAssignment->orWhereHas('assignment',function ($query) use ($school_id, $academic_year, $teacher_id) {
                    $query->where([
                        ['school_id',$school_id],
                        ['academic_year_id',$academic_year->id],
                        ['teacher_id',$teacher_id]
                    ]);
                })->where([['assignment_id',$id],['status','completed']]);
            }
            if($request->search != null)
            {
                $studentAssignment = $studentAssignment->where('obtained_marks','LIKE','%'.$request->search.'%')->orWhere('comments','LIKE','%'.$request->search.'%')->orWhereHas('student' , function ($query) use($request)
                {
                    $query->wherehas('userprofile',function ($q) use($request)
                    {
                        $q->where('firstname','LIKE','%'.$request->search.'%')->orWhere('lastname','LIKE','%'.$request->search.'%'); 
                    })->orWhereHas('studentAcademicLatest', function($que) use($request)
                    {
                        $que->where('roll_number','LIKE','%'.$request->search.'%');
                    });
                });
            }
        }
        $studentAssignment=$studentAssignment->paginate(10);
        $list  =   StudentAssignmentResource::collection($studentAssignment);
        
        return $list;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        //
        $assignment     =   Assignment::where('id',$id)->first();
        $query = \Request::getQueryString();

        return view('/teacher/assignment/show' , ['assignment' => $assignment , 'query' => $query]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StudentAssignmentUpdateRequest $request,$id)
    {
        //
        try
        {
            $studentAssignment     =   StudentAssignment::where('id',$id)->first();

            $studentAssignment->obtained_marks  =   $request->obtained_marks;
            $studentAssignment->comments        =   $request->comments;
            $studentAssignment->marks_given_by  =   Auth::id();
            $studentAssignment->marks_given_on  =   date('Y-m-d');
            $studentAssignment->status          =   'completed';

            $studentAssignment->save();

            foreach ($studentAssignment->student->parents as $parent) 
            {
                $array=[];

                $array['school_id']  =  $school_id;
                $array['user_id']    =  $parent->userParent->id;
                $array['message']    = 'Assignment Marks Updated';
                $array['type']       = 'assignment';
                                
                event(new SinglePushEvent($array));
            }

            $data = [];

            $data['user']       =   $studentAssignment->student;
            $data['details']    =   trans('notification.assignment_mark_add_msg');

            event(new SingleNotificationEvent($data));

            $message=trans('messages.add_success_msg',['module' => 'Assignment Mark']);

            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $studentAssignment,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_UPDATE_STUDENT_ASSIGNMENT,
                $message
            );

            $res['success'] =   $message;

            return $res;
        }
        catch(Exception $e)
        {
            //dd($e->getMessage());
        }
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
        $studentAssignment     =   StudentAssignment::where('id',$id)->first();
        $array = [];

        $array['assignment_file']   =   $studentAssignment->AssignmentFilePath;
        $array['obtained_marks']    =   $studentAssignment->obtained_marks;
        $array['comments']          =   $studentAssignment->comments;
        
        return $array;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StudentAssignmentUpdateRequest $request, $id)
    {
        //
        try
        {
            $studentAssignment     =   StudentAssignment::where('id',$id)->first();

            $studentAssignment->obtained_marks  =   $request->obtained_marks;
            $studentAssignment->comments        =   $request->comments;
            $studentAssignment->marks_given_by  =   Auth::id();
            $studentAssignment->marks_given_on  =   date('Y-m-d');
            $studentAssignment->status          =   'completed';

            $studentAssignment->save();

            foreach ($studentAssignment->student->parents as $parent) 
            {
                $array=[];

                $array['school_id']  =  $school_id;
                $array['user_id']    =  $parent->userParent->id;
                $array['message']    = 'Assignment Marks Updated';
                $array['type']       = 'assignment';
                                
                event(new SinglePushEvent($array));
            }

            $data = [];

            $data['user']       =   $studentAssignment->student;
            $data['details']    =   trans('notification.assignment_mark_add_msg');

            event(new SingleNotificationEvent($data));

            $message=trans('messages.update_success_msg',['module' => 'Assignment Mark']);

            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $studentAssignment,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_UPDATE_STUDENT_ASSIGNMENT,
                $message
            );

            $res['success'] =   $message;

            return $res;
        }
        catch(Exception $e)
        {
            //dd($e->getMessage());
        }
    }
}