<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */
namespace App\Http\Controllers\Admin;

use App\Http\Requests\Classwall\PostRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Traits\LogActivity;
use App\Helpers\SiteHelper;
use App\Models\PostTag;
use App\Traits\Common;
use App\Models\Post;
use App\Models\Tag;
use Exception;

class PostAddController extends Controller
{
    //
    use LogActivity;
    use Common;

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createList()
    {
        //
        $standardLinkList = SiteHelper::getStandardLinkList(Auth::user()->school_id);

        return $standardLinkList;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        if(count((array)\Request::getQueryString())>0)
        {
            if($request->entity_id != '')
            { 
                $entity_id = $request->entity_id;
            }
            if($request->entity_name != '')
            { 
                $entity_name = $request->entity_name;
            }
        }
        else
        {
            $entity_id      = Auth::id();
            $entity_name    = 'App\Models\User';
        }

        return view('/admin/classwall/post/create' , [ 'entity_id' => $entity_id , 'entity_name' => $entity_name ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        //
        try
        {
            $school_id = Auth::user()->school_id;
            $academic_year = SiteHelper::getAcademicYear($school_id);

            $post = new Post;

            $post->school_id        = $school_id;
            $post->academic_year_id = $academic_year->id;
            $post->entity_id        = $request->entity_id;
            $post->entity_name      = $request->entity_name;
            $post->description      = $request->description;
            $post->visibility       = $request->visibility;
            if($request->visibility == 'select_class')
            {
                $post->visible_for      = $request->visible_for;
            }
            if($request->post_later == 'true')
            {
                $post->post_created_at = date('Y-m-d H:i:s',strtotime($request->posted_at));
                $post->is_posted = 0;
                $post->status  = 'pending';
            }
            else
            {
                $post->post_created_at = date('Y-m-d H:i:s');
                $post->posted_at = date('Y-m-d H:i:s');
                $post->is_posted = 1;
                $post->status  = 'posted';
            }

            $post->created_by = Auth::id();
            $post->save();

            if($request->tag!=''){
            $tag=Tag::firstOrCreate(['tag_name' => $request->tag]);
            $posttag=PostTag::create(['post_id'=>$post->id,'tag_id'=>$tag->id]);
            }

            $message = trans('messages.add_success_msg',['module' => 'Post']);

            $ip= $this->getRequestIP();
            $this->doActivityLog(
                $post,
                Auth::user(),
                ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                LOGNAME_ADD_POST,
                $message
            ); 

            $res['id'] = $post->id;
            $res['success'] = $message;
            return $res;
        }
        catch(Exception $e)
        {
            //dd($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function attachment(Request $request)
    {
        //
        try
        {
            $post = Post::where('id',$request->post_id)->first();
            $i =0;
            $files = $request->file;
            
            if(count($files) > 0) 
            {
                $post->attachment_file = null;
                $post->save();
                $path = [];
                foreach($files as $file) 
                {
                    $path[$i] = $this->uploadFile(Auth::user()->school->slug.'/posts/'.$request->post_id,$file); 
                    $i++;     
                }
                $post->attachment_file = $path; 
                $post->save();
            }
        }
        catch(Exception $e)
        {
            //dd($e->getMessage());
        }
    }
}