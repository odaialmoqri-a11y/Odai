<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */
namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admission as AdmissionResource;
use App\Http\Requests\Admission\AdmissionFormRequest;
use App\Http\Resources\FeeGroup as FeeGroupResource;
use App\Http\Resources\Section as SectionResource;
use App\Events\AdmissionApprovalEvent;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Traits\AdmissionUser;
use App\Traits\SmsProcess;
use App\Models\StandardLink;
use Illuminate\Http\Request;
use App\Helpers\SiteHelper;
use App\Traits\LogActivity;
use App\Models\Admission;
use App\Models\FeeGroup;
use App\Traits\Common;
use App\Models\Fee;
use Exception;
use Log;

class AdmissionController extends Controller
{  
    use AdmissionUser;
    use LogActivity;
    use SmsProcess;
    use Common;

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request ) 
    {
        return view('/admin/admission/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admissionlist(Request $request)
    {
        $academic_year = SiteHelper::getAcademicYear(Auth::user()->school_id);
        $admissions = Admission::where([['school_id',Auth::user()->school_id],['academic_year_id',$academic_year->id]])->where('application_status','Draft')->orWhere('application_status','Pending')->paginate(10);
              
        $admissions = AdmissionResource::collection($admissions);
        
        return $admissions;        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $admission = Admission::where('school_id',Auth::user()->school_id)->where('id',$id)->where('application_status','Draft')->orWhere('application_status','Pending')->first();

        $sections = StandardLink::where('standard_id',$admission->standard_id)->get();

        $fee = FeeGroup::where('school_id',Auth::user()->school_id)->get();

        $array=[];
        $array['id']                    =   $admission->id;
        $array['name']                  =   $admission->name;
        $array['standard_id']           =   $admission->standard_id;              
        $array['application_no']        =   $admission->application_no;
        $array['application_status']    =   $admission->application_status;
        $array['sectionlist']           =   SectionResource::collection($sections);
        $array['feelist']               =   FeeGroupResource::collection($fee);

        return $array;  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admission = Admission::where('id',$id)->first();

        return view('/admin/admission/edit' , ['admission' => $admission]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdmissionFormRequest $request, $id)
    {
        try
        {
            $admission=Admission::where('id',$id)->first();

            $admission->application_status = $request->application_status;

            if($request->application_status == 'Approved')
            {
                $admission->section_id      =   $request->section_id;
                $admission->fee_group_id    =   $request->fee_group_id;
                $admission->payment_status  =   $request->payment_status;

                $admission->save();

                $standardLink_id=StandardLink::where([['school_id',Auth::user()->school_id],['standard_id',$admission->standard_id],['section_id',$admission->section_id]])->first();

                $fee = Fee::where([['school_id',Auth::user()->school_id],['fee_group_id',$request->fee_group_id]])->first();

                if($request->payment_status == 'paid')
                {
                    $student = $this->CreateStudent($admission,6,$standardLink_id->id,$admission->avatar,$fee,Auth::user());
                    $father  = $this->CreateStudentFather($student->id,$admission,7);
                    $father_data = [];

                    $father_data['application_no'] = $admission->application_no;
                    $father_data['school_name']    = ucwords($admission->school->name);
                    if($father->email != null)
                    {
                        $father_data['email']          = $father->email;

                        event(new AdmissionApprovalEvent($father_data));
                    }
                    if($father->mobile_no != null)
                    {
                        $father_data['mobile_no']      = $father->mobile_no;
                        
                        $this->sendAdmissionApproval($father_data);
                    }

                    $mother  = $this->CreateStudentMother($student->id,$admission,7);
                    $mother_data = [];

                    $mother_data['application_no'] = $admission->application_no;
                    $mother_data['school_name']    = ucwords($admission->school->name);
                    if($mother->email != null)
                    {
                        $mother_data['email']          = $mother->email;

                        event(new AdmissionApprovalEvent($mother_data));
                    }
                    if($mother->mobile_no != null)
                    {
                        $mother_data['mobile_no']      = $mother->mobile_no;

                        $this->sendAdmissionApproval($mother_data);
                    }
                }
            }
            else
            {
                $admission->save();
            }
           
            $message=trans('messages.update_success_msg',['module' => 'Admission']);

            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $admission,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_UPDATE_ADMISSION_FORM,
                $message
            );

            $res['success'] = $message;
            return $res;
        }
        catch(Exception $e)
        {
            Log::info($e->getMessage());
            dd($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try
        {
            $admission = Admission::where('id',$id)->first();

            $admission->delete();

            $message=trans('messages.delete_success_msg',['module' => 'Admission']);

            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $admission,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_DELETE_ADMISSION_FORM,
                $message
            ); 

            $res['success'] = $message;
            return $res;
        }
        catch(Exception $e)
        {
            Log::info($e->getMessage());
            dd($e->getMessage());
        } 
    }
}