<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */
namespace App\Http\Controllers\Admin;
use App\Http\Resources\PostalRecord as PostalRecordResource;
use App\Http\Resources\User as UserResource;;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PostalRecordRequest;
use App\Helpers\SiteHelper;
use App\Models\PostalRecord;
use App\Models\User;
use App\Models\School;
use App\Traits\Common;
use App\Traits\LogActivity;

class PostalRecordController extends Controller
{
    use Common;
    use LogActivity;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function showlist(Request $request)
    {
        $academic_year = SiteHelper::getAcademicYear(Auth::user()->school_id);

        $postalrecord = PostalRecord::where([['school_id',Auth::user()->school_id],['academic_year_id',$academic_year->id]])->get();
       
       
        $postalrecordlist = PostalRecordResource::collection($postalrecord);
        
        return $postalrecordlist;
    }
    

    public function index()
    { 
        //$postalrecord=postalrecord::where('id',$id)->first();
        return view('/admin/postalrecord/index');
    }


    public function create()
    {
         $date=date('Y-m-d');
         $school=School::where('id',Auth::user()->school_id)->first();
         $address=$school->address;
        return view('/admin/postalrecord/create',['date'=>$date,'address'=>$address]);
    }

    public function store(PostalRecordRequest $request)
    {
        try 
        {

            $school_id = Auth::user()->school_id;

            $academic_year = SiteHelper::getAcademicYear(Auth::user()->school_id);

            $postalrecord=new PostalRecord;

            $postalrecord->school_id=$school_id;
            $postalrecord->academic_year_id=$academic_year->id;
            $postalrecord->type=$request->type;
            $postalrecord->post_type=$request->post_type;
            $postalrecord->reference_number=$request->reference_number;
            $postalrecord->confidential=$request->confidential;
            $postalrecord->sender_title=$request->sender_title;
            $postalrecord->sender_address=$request->sender_address;
            $postalrecord->receiver_title=$request->receiver_title;
            $postalrecord->receiver_address=$request->receiver_address;
            $postalrecord->postal_date=$request->postal_date;

            $file = $request->file('attachment');
            if($file)
            {
                $folder=Auth::user()->school->slug.'/postalrecord';
                $path = $this->uploadFile($folder,$file);
                //dd($path);
                $postalrecord->attachment = $path; 
            }

          
            $postalrecord->description=$request->description;
            $postalrecord->entry_by=Auth::user()->name;

            $postalrecord->save();

            $message = trans('messages.add_success_msg',['module' => 'Postal Record']);

              $ip= $this->getRequestIP();
                $this->doActivityLog(
                  $postalrecord,
                  Auth::user(),
                  ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                  LOGNAME_ADD_POSTAL_RECORD,
                  $message
                ); 

            $res['success']=trans('messages.add_success_msg',['module' => 'Postal Record']);
            return $res;
        }

        catch(Exception $e)
        {
            //dd($e->getMessage());
        }

    }

     public function show($id)
    {
        $postalrecord=PostalRecord::where('id',$id)->get();

        $postalrecord=PostalRecordResource::collection($postalrecord);

        //dump($postalrecord);

        return $postalrecord;
    }


     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $postalrecord = PostalRecord::where([['id',$id],['school_id',Auth::user()->school_id]])->first();

        
        /*if(Gate::allows('book',$book))
        {*/
            return view('/admin/postalrecord/edit' , ['postalrecord' => $postalrecord]);
      /*  }
        else
        {
            abort(403);
        }*/
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

          $school_id = Auth::user()->school_id;

          $academic_year = SiteHelper::getAcademicYear(Auth::user()->school_id);
        try
        {
            $postalrecord=PostalRecord::find($id);

            $postalrecord->school_id=$school_id;
            $postalrecord->academic_year_id=$academic_year->id;
            $postalrecord->type=$request->type;
            $postalrecord->reference_number=$request->reference_number;
            $postalrecord->confidential=$request->confidential;
            $postalrecord->sender_title=$request->sender_title;
            $postalrecord->sender_address=$request->sender_address;
            $postalrecord->receiver_title=$request->receiver_title;
            $postalrecord->receiver_address=$request->receiver_address;
            $postalrecord->postal_date=$request->postal_date;
            $file = $request->file('attachment');
            if($file)
            {
                $folder=Auth::user()->school->slug.'/postalrecord';
                $path = $this->uploadFile($folder,$file);
                //dd($path);
                $postalrecord->attachment = $path; 
            }

          
            $postalrecord->description=$request->description;
            $postalrecord->entry_by=Auth::user()->name;
          

            $postalrecord->save();


            $message=trans('messages.update_success_msg',['module' => 'Postal Record']);

            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $postalrecord,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_EDIT_POSTAL_RECORD,
                $message
            );

            $res['success'] = $message;
            return $res;
        }
        catch(Exception $e)
        {
            //dd($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \DB::beginTransaction();
        try 
        {
            $postalrecord=PostalRecord::where('id',$id)->first();
            
          
                $postalrecord->delete();
            
            
         

            $message=trans('messages.delete_success_msg',['module' => 'Postal Record']);

            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $postalrecord,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_DELETE_POSTAL_RECORD,
                $message
            );

            $res['message'] = $message;

            \DB::commit();
            return $res;
        }
        catch(Exception $e) 
        {
            \DB::rollBack();
            //dd($e->getMessage());
        }
    }
}
