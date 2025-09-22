<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */
namespace App\Http\Controllers\Api\Teacher;

use App\Http\Resources\API\Teacher\Holiday as HolidayResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Helpers\SiteHelper;
use App\Models\Events;

class HolidaysController extends Controller
{
    //

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 
        $school_id = Auth::user()->school_id;
        $academic_year = SiteHelper::getAcademicYear($school_id);

        $holidays = Events::where([
        		['school_id',$school_id],
        		['academic_year_id',$academic_year->id],
        		['category','holidays']
        	])->orderBy('start_date','ASC');
        $count=count($holidays->get());
        $holidays=$holidays->paginate(10);

        $holidays = HolidayResource::collection($holidays);

        //return $holidays;
        return response()->json([
            'success'   =>  true,
            'message'   =>  'Holiday List',
            'data'      =>  $holidays,
            'count'     =>  $count
        ],200); /*pagination not working*/
    }
}