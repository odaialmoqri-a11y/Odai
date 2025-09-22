<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */
namespace App\Http\Controllers\Admin;

use App\Http\Resources\AttendanceUser as AttendanceUserResource;
use App\Http\Resources\API\BookLending as BookLendingResource;
use App\Http\Resources\UserRelation as UserRelationResource;
use App\Http\Resources\UserSibling as UserSiblingResource;
use App\Http\Resources\ActivityLog as ActivityLogResource;
use App\Http\Resources\UserDetail as UserDetailResource;
use App\Http\Resources\Discipline as DisciplineResource;
use App\Http\Resources\UserFees as UserFeesResource;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\SiblingListResource;
use App\Http\Requests\MedicalHistoryRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentParentLink;
use App\Models\StudentAcademic;
use Illuminate\Http\Request;
use App\Schoolplus\Student;
use App\Helpers\SiteHelper;
use App\Traits\LogActivity;
use App\Models\ActivityLog;
use App\Models\FeePayment;
use App\Models\StandardLink;
use App\Models\Teacherlink;
use App\Models\Subject;
use App\Traits\Common;
use App\Models\User;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Fee;
use Exception;
use Log;

class StudentDetailsController extends Controller
{
    //
    use LogActivity;
    use Common;

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showDetails($name)
    {
        //
        $users = User::with('userprofile')->where('name', $name)->get();

        $users = UserDetailResource::collection($users);

        return $users;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showRelations($name)
    {
        //
        $student = User::with('userprofile')->where('name', $name)->first();
      
        $parents = UserRelationResource::collection($student->parents);
         
        return $parents;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showSiblings($name)
    {
        //

        $student = User::with('userprofile')->where('name', $name)->first();
        $parents=StudentParentLink::where('student_id',$student->id)->pluck('parent_id')->toArray();
       $siblings=StudentParentLink::where('student_id','!=',$student->id)->whereIn('parent_id',$parents)->get()->unique('student_id');
      
        $siblings = SiblingListResource::collection($siblings);
        return $siblings;
    }

    public function showActivity($name)
    {
        //
        $user = User::with('userprofile')->where('name', $name)->first();
        if(Gate::allows('member',$user))
        {
            $activitylog = ActivityLog::where('subject_id',$user->userprofile->id)->orWhere('subject_id',$user->members[0]['id'])->paginate(5);

            $activitylog = ActivityLogResource::collection($activitylog);
         
            return $activitylog;
        }
        else
        {
            abort(403);
        } 
    }

    public function showActivityLog($name)
    {
        //
        $user = User::with('userprofile')->where('name', $name)->first();
        if(Gate::allows('member',$user))
        {
            $activitylog = ActivityLog::where('causer_id',$user->userprofile->id)->orWhere('causer_id',$user->members[0]['id'])->paginate(5);

            $activitylog = ActivityLogResource::collection($activitylog);
         
            return $activitylog;
        }
        else
        {
            abort(403);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showDisciplines($name)
    {
        //
        $student = User::with('disciplineUser','disciplineTeacher')->where('name', $name)->first();
      
        $discipline = DisciplineResource::collection($student->disciplineUser);
         
        return $discipline;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAttendance($name)
    {
        //
        $student = User::where('name', $name)->first();
      
        $attendances = AttendanceUserResource::collection($student->AttendanceUserAbsent);
         
        return $attendances;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showMedicalHistory($name)
    {
        //
        $student = User::where('name', $name)->first();

        $studentacademic = StudentAcademic::where('user_id',$student->id)->latest()->first();

        $medicals = [];
        
        $medicals['height']                     = number_format($studentacademic->height?? 0,2);
        $medicals['weight']                     = number_format($studentacademic->weight?? 0,2);
        $medicals['medication_problems']        = $studentacademic->medication_problems == 'null' ? null:$studentacademic->medication_problems;
        $medicals['medication_needs']           = $studentacademic->medication_needs == 'null' ? null:$studentacademic->medication_needs;
        $medicals['medication_allergies']       = $studentacademic->medication_allergies == 'null' ? null:$student->studentAcademicLatest->medication_allergies;
        $medicals['food_allergies']             = $studentacademic->food_allergies == 'null' ? null:$studentacademic->food_allergies;
        $medicals['other_allergies']            = $studentacademic->other_allergies == 'null' ? null:$studentacademic->other_allergies;
        $medicals['other_medical_information']  = $studentacademic->other_medical_information == 'null' ? null:$studentacademic->other_medical_information;
         
        return $medicals;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showFees($name)
    {
        //
        $student = User::where('name', $name)->first();

        $school_id = Auth::user()->school_id;
        $academic_year = SiteHelper::getAcademicYear($school_id);

        //new
        if(class_exists('Gegok12\Fee\Models\Fee'))
        {

            $fees = \Gegok12\Fee\Models\Fee::where([['school_id',$school_id],['academic_year_id',$academic_year->id]])->where('standardLink_id',$student->studentAcademicLatest->standardLink_id)->orWhere('standardLink_id',null)->orderBy('start_date','DESC')->paginate(5);
        }
        else{
            $fees = Fee::where([['school_id',$school_id],['academic_year_id',$academic_year->id]])->where('standardLink_id',$student->studentAcademicLatest->standardLink_id)->orWhere('standardLink_id',null)->orderBy('start_date','DESC')->paginate(5);
        }
        
        if(class_exists('Gegok12\Fee\Http\Resources\UserFees'))
        {
            $feepayments = \Gegok12\Fee\Http\Resources\UserFees::collection($fees);
        }
        else
        {
            $feepayments = UserFeesResource::collection($fees);
        }

         
        return $feepayments;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createMedicalHistory($name)
    {
        //
        $user = User::where('name', $name)->first(); 

        return view('/admin/member/create_medical_history' , ['user' => $user]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addMedicalHistory(MedicalHistoryRequest $request,$name)
    {
        //
        try
        {
            $user = User::where('name', $name)->first();

            $studentacademic = StudentAcademic::where('id',$user->studentAcademicLatest->id)->orderBy('id','DESC')->first();

            $studentacademic->height                     = number_format($request->height, 2);
            $studentacademic->weight                     = number_format($request->weight, 2);
            $studentacademic->medication_problems        = $request->medication_problems == 'null' ? null:$request->medication_problems;
            $studentacademic->medication_needs           = $request->medication_needs == 'null' ? null:$request->medication_needs;
            $studentacademic->medication_allergies       = $request->medication_allergies == 'null' ? null:$request->medication_allergies;
            $studentacademic->food_allergies             = $request->food_allergies == 'null' ? null:$request->food_allergies;
            $studentacademic->other_allergies            = $request->other_allergies == 'null' ? null:$request->other_allergies;
            $studentacademic->other_medical_information  = $request->other_medical_information == 'null' ? null:$request->other_medical_information;
                 
            $studentacademic->save();

            $message = trans('messages.update_success_msg',['module' => 'Student Medical History']);

            $ip= $this->getRequestIP();
            $this->doActivityLog(
              $studentacademic,
              Auth::user(),
              ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
              LOGNAME_EDIT_STUDENT_MEDICAL_HISTORY,
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

    public function showBookLent($name)
    {
        //
        $student = User::with('lending')->where('name', $name)->first();

        $lent = BookLendingResource::collection($student->lending);

        return $lent;
    }

    public function show($name)
    {
        // 
        $user = User::with('studentAcademicLatest')->where('name',$name)->first(); 
        $parents = $user->parent;
        if(Gate::allows('member',$user))
        {
            if($_SERVER['HTTP_REFERER'] != null)
            {
                $prev_url = $_SERVER['HTTP_REFERER'];
            }
            else
            {
                $prev_url = url('/admin/students');
            }
            
            return view('/admin/member/show',['user' => $user , 'parents' => $parents , 'prev_url' => $prev_url]);
        }
        else
        {
            abort(403);
        } 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showmark($name)
    {
       $users = User::with('marks')->where('name', $name)->first();
       $studentId=$users->id;
       $examId=$users->marks[0]['exam_id'];

       return  Student::getStudentMark($studentId,$examId);
    }

    public function showAllMark($name)
    {
        $users = User::where('name', $name)->first();

        $studentId=$users->id;

        return  Student::getAllMarks($studentId);
    }

    public function compareMarks($name)
    {
        try
        {
        $users=User::with('studentAcademic')->where('name',$name)->get();
        $studentId=$users[0]['id'];
        $standardId=$users[0]['studentAcademicLatest']['standardLink_id'];

        $exam=Mark::where('school_id',Auth::user()->school_id)->where('standard_id',$standardId)->take(2)->orderBy('exam_id',DESC)->groupBy('exam_id')->pluck('exam_id')->toArray();
        //dd($exam);
        $examIdOne=$exam[0];
        $examIdTwo=$exam[1];

        //return  Student::CompareMarks($studentId,$examIdOne,$examIdTwo,$standardId);


         $standard=StandardLink::where('id',$standardId)->first();

        $standard_id=$standard->standard_id;
        $section_id=$standard->section_id;
        //dd($standard_id);
        //$subjects=$subjects['name'];
    
        //dd($classCount);
        $subjects=Subject::where('standard_id',$standard_id)->where('section_id',$section_id)->pluck('name')->toArray();
        $marksone=Mark::where('user_id',$studentId)->where('exam_id',$examIdOne)->pluck('obtained_marks')->toArray();
        $markstwo=Mark::where('user_id',$studentId)->where('exam_id',$examIdTwo)->pluck('obtained_marks')->toArray();

        $examone=Exam::where('standard_id',$standardId)->where('id',$examIdOne)->pluck('name')->toArray();

        $examtwo=Exam::where('standard_id',$standardId)->where('id',$examIdTwo)->pluck('name')->toArray();
        //dd($examIdTwo);
        $examOneAverage=Mark::where([['standard_id',$standardId],['exam_id',$examIdOne]])->groupBy('subject_id')->selectRaw('round(avg(obtained_marks)) as avg')->pluck('avg');

        $examTwoAverage=Mark::where([['standard_id',$standardId],['exam_id',$examIdTwo]])->groupBy('subject_id')->selectRaw('round(avg(obtained_marks)) as avg')->pluck('avg');
        
         return view('/admin/exammark/process' , ['subjects'=>$subjects,'marksone'=>$marksone,'markstwo'=>$markstwo,'examone'=>$examone,'examtwo'=>$examtwo,'examOneAverage'=>$examOneAverage,'examTwoAverage'=>$examTwoAverage]);

        }
        catch(Exception $e)
        {
            Log::info($e->getMessage());
            dd($e->getMessage());
        }
    } 


    public function marksGraph($name)
    {
        $school_id      =   Auth::user()->school_id;
        $academic_year  =   SiteHelper::getAcademicYear($school_id);
        $users=User::with('studentAcademic')->where('name',$name)->get();
        $studentId=$users[0]['id'];
        $standardId=$users[0]['studentAcademicLatest']['standardLink_id'];


        $subjects_list=Teacherlink::where([['school_id',$school_id],['academic_year_id',$academic_year->id],['standardLink_id',$standardId]]);
        $subjects=$subjects_list->get()->pluck('subject.name')->toArray();
        if(class_exists('Gegok12\Exam\Models\Exam'))
        {
            $examss=\Gegok12\Exam\Models\Exam::where('standard_id',$standardId)->get();

        }
        else
        {
            $examss=Exam::where('standard_id',$standardId)->get();

        }
         
          //dd($subjects);
        $subjects_array[]=array_merge(['Subjects'],$subjects,['average']);
        
         $data=[];
         foreach ($examss as $key => $exam) {

            if(class_exists('Gegok12\Exam\Models\Mark'))
            {
                $exam_result=\Gegok12\Exam\Models\Mark::where('user_id',$studentId)->where('exam_id',$exam->id);
            }
            else{
                $exam_result=Mark::where('user_id',$studentId)->where('exam_id',$exam->id);
            }
           
           $exam_marks=$exam_result->pluck('obtained_marks')->toArray();
           $exam_avg=$exam_result->avg('obtained_marks');

           $sellers = [];

           $records= $subjects_list->get()->map(function ($seller) use ($school_id,$academic_year,$standardId,$exam) {

            if(class_exists('Gegok12\Exam\Models\Mark'))
            {
                $markings=\Gegok12\Exam\Models\Mark::where([['school_id',$school_id],['academic_year_id',$academic_year->id],['standard_id',$standardId],['exam_id',$exam->id],['subject_id',$seller->subject_id]])->first();
            }
            else
            {
                $markings=Mark::where([['school_id',$school_id],['academic_year_id',$academic_year->id],['standard_id',$standardId],['exam_id',$exam->id],['subject_id',$seller->subject_id]])->first();
            }
            

                if($markings!=null){
                return $markings->obtained_marks;
                }
                else
                {
                    return "0";
                }
            })->toArray();
            //dd($records);
           //if(count($exam->schedule)==count($exam_marks)){
            $data[]=array_merge([$exam->name],array_map('intval',$records),[$exam_avg]);
           //}
         }
         $finas=array_merge($subjects_array,$data);

          return view('/admin/exammark/markgraph' , ['subjects'=>$finas,'user'=>$users[0]]);
    }
}