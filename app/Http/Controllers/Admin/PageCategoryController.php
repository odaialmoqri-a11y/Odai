<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */
namespace App\Http\Controllers\Admin;

use App\Http\Resources\Classwall\PageCategory as PageCategoryResource;
use App\Http\Requests\Classwall\PageCategoryRequest;
use App\Models\ClassRoomPageCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Traits\LogActivity;
use App\Helpers\SiteHelper;
use App\Traits\Common;
use App\Models\User;
use Exception;
use Log;

class PageCategoryController extends Controller
{
    //
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
        $category = ClassRoomPageCategory::where([
            ['school_id',$school_id],
            ['academic_year_id',$academic_year->id],
            ['status',1],
        ])->get();
        $category = PageCategoryResource::collection($category);

        return $category;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PageCategoryRequest $request)
    {
        //
        try
        {
            $school_id = Auth::user()->school_id;
            $academic_year = SiteHelper::getAcademicYear($school_id);
            $category = new ClassRoomPageCategory;

            $category->school_id        = $school_id;
            $category->academic_year_id = $academic_year->id;
            $category->name        		= strtolower(str_replace(' ', '_', $request->name));
            $category->status           = 1;

            $category->save();

            $message = trans('messages.add_success_msg',['module' => 'Page Category']);

            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $category,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_ADD_PAGE_CATEGORY,
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