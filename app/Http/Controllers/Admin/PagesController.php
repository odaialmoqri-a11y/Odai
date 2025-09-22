<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */
namespace App\Http\Controllers\Admin;

use App\Http\Resources\Classwall\Page as PageResource;
use App\Http\Requests\Classwall\PageUpdateRequest;
use App\Http\Requests\Classwall\PageAddRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\ClassRoomPageDetail;
use App\Models\ClassRoomPage;
use Illuminate\Http\Request;
use App\Traits\LogActivity;
use App\Helpers\SiteHelper;
use App\Traits\Common;
use App\Models\User;
use Exception;
use Log;

class PagesController extends Controller
{
    use LogActivity;
    use Common;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        //
        $school_id = Auth::user()->school_id;
        $academic_year = SiteHelper::getAcademicYear($school_id);
        $pages = ClassRoomPage::where([
            ['school_id',$school_id],
            ['academic_year_id',$academic_year->id],
            ['status',1],
        ])->paginate(5);
        $pages = PageResource::collection($pages);

        return $pages;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('/admin/classwall/page/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('/admin/classwall/page/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PageAddRequest $request)
    {
        //
        try
        {
            $school_id = Auth::user()->school_id;
            $academic_year = SiteHelper::getAcademicYear($school_id);
            $page = new ClassRoomPage;

            $page->school_id        = $school_id;
            $page->academic_year_id = $academic_year->id;
            $page->page_name        = $request->page_name;
            $page->category_id      = $request->category;
            $page->description      = $request->description;
            $page->created_by       = Auth::id();
            $page->status           = 1;

            $file = $request->file('cover_image');
            if($file)
            {
                $folder =   Auth::user()->school->slug.'/pages';
                $path   =   $this->uploadFile($folder,$file);
                $page->cover_image = $path; 
            }

            $page->save();

            $message = trans('messages.add_success_msg',['module' => 'Page']);

            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $page,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_ADD_PAGE,
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showList($id)
    {
        //
        $page = ClassRoomPage::where('id',$id)->first();

        $array = [];

        $array['page_name']     = $page->page_name;
        $array['category']      = $page->category_id;
        $array['description']   = $page->description;
        $array['cover_image']   = $page->CoverImagePath;
        $array['like_count']    = $page->classRoomPageDetail()->where('like',1)->count();
        $array['unlike_count']  = $page->classRoomPageDetail()->where('dislike',1)->count();
        $array['follow_count']  = $page->classRoomPageDetail()->where('is_following',1)->count();
        $pagedetail = ClassRoomPageDetail::where([['user_id',Auth::id()],['page_id',$page->id]])->first();
        if($pagedetail != null)
        {
            $array['is_following']  =  $pagedetail->is_following;
            $array['like']          =  $pagedetail->like;
            $array['dislike']       =  $pagedetail->dislike;
        }

        return $array;
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
        $page = ClassRoomPage::where('id',$id)->first();
        $entity_id      = $page->id;
        $entity_name    = 'App\Models\Page';

        return view('/admin/classwall/page/show' , [ 'page' => $page , 'entity_id' => $entity_id , 'entity_name' => $entity_name ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editList($id)
    {
        //
        $page = ClassRoomPage::where('id',$id)->first();

        $array = [];

        $array['page_name']        = $page->page_name;
        $array['category']         = $page->category_id;
        $array['description']      = $page->description;
        $array['cover_image']      = $page->CoverImagePath;

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
        //
        $page = ClassRoomPage::where('id',$id)->first();

        return view('/admin/classwall/page/edit', [ 'page' => $page ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PageUpdateRequest $request, $id)
    {
        //
        try
        {
            $page = ClassRoomPage::where('id',$id)->first();

            $page->page_name        = $request->page_name;
            $page->category_id      = $request->category;
            $page->description      = $request->description;
            $page->created_by       = Auth::id();

            $file = $request->file('cover_image');
            if($file)
            {
                $folder =   Auth::user()->school->slug.'/pages';
                $path   =   $this->uploadFile($folder,$file);
                $page->cover_image = $path; 
            }

            $page->save();

            $message = trans('messages.update_success_msg',['module' => 'Page']);

            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $page,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_EDIT_PAGE,
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        try
        {
            $page = ClassRoomPage::where('id',$id)->first();
            if(Gate::allows('page',$page))
            {
                $page->delete();

                $message=trans('messages.delete_success_msg',['module' => 'Page']);


                $ip= $this->getRequestIP();
                $this->doActivityLog(
                    $page,
                    Auth::user(),
                    ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                    LOGNAME_DELETE_PAGE,
                    $message
                );
                $res['success'] = $message;
                return $res;
                //return redirect()->back()->with('successmessage',$message);
            }
            else
            {
                abort(403);
            }
        }
        catch(Exception $e)
        {
            Log::info($e->getMessage());
            //dd($e->getMessage());
        }
    }
}