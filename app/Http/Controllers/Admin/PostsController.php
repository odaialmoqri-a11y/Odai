<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */
namespace App\Http\Controllers\Admin;

use App\Http\Resources\Classwall\Post as PostResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Traits\LogActivity;
use App\Helpers\SiteHelper;
use App\Models\PostDetail;
use App\Traits\Common;
use App\Models\Post;
use Exception;

class PostsController extends Controller
{
    use LogActivity;
    use Common;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexList(Request $request)
    {
        //
        $school_id = Auth::user()->school_id;
        $academic_year = SiteHelper::getAcademicYear($school_id);
        $posts = Post::where([['school_id',$school_id],['academic_year_id',$academic_year->id],['visibility','!=','select_class'],['is_posted',1]])->orderBy('post_created_at','DESC');
        if(count((array)\Request::getQueryString())>0)
        {
            if($request->entity_id != '')
            { 
                $posts = $posts->where('entity_id',$request->entity_id);
            }
            if($request->entity_name != '')
            { 
                $posts = $posts->where('entity_name',$request->entity_name);
            }
        }
        $posts = $posts->get();
        $posts = PostResource::collection($posts);

        return $posts;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $entity_id      = Auth::id();
        $entity_name    = 'App\Models\User';

        return view('/admin/classwall/post/index' , [ 'entity_id' => $entity_id , 'entity_name' => $entity_name ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showList($id)
    {
        //
        $post = Post::where('id',$id)->first();

        if($post->visibility == 'all_class')
        {
            $visibility = str_replace('_', ' ', ucwords($post->visibility));
        }
        elseif($post->visibility == 'select_class')
        {
            $visibility = $post->StandardLink->StandardSection;
        }
        
        $array = [];

        $array['description']       = $post->description;
        $array['visibility']        = $visibility;
        $array['post_created_at']   = $post->post_created_at->diffForHumans();
        $array['created_by']        = $post->created_by;
        $array['is_posted']         = $post->is_posted;
        $array['attachments']       = $post->AttachmentPath;
        $post_detail = PostDetail::where([['user_id',Auth::id()],['post_id',$id]])->first();
        $array['like']              = $post_detail->like;
        $array['unlike']            = $post_detail->unlike;
        $array['save']              = $post_detail->save;
        $array['unsave']            = $post_detail->unsave;
        $array['auth_id']           = Auth::id();
        $array['like_count']        = $post->postDetail== null ?null:$post->postDetail->ByLikeCount($post->id);
        $array['unlike_count']      = $post->postDetail== null ?null:$post->postDetail->ByUnlikeCount($post->id);
        $array['comment_list']['comments']          = $post->PostComments;
        $array['comment_list']['comments_count']    = count($post->PostComments);

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
        $post = Post::where('id',$id)->first();

        return view('/admin/classwall/post/show' , ['post' => $post]);
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
            $post = Post::where('id',$id)->first();
            if(Gate::allows('post',$post))
            {
                if($post->created_by == Auth::id())
                {
                    $post->status  = 'cancelled';
                    $post->save();

                    $post->delete();

                    $message=trans('messages.delete_success_msg',['module' => 'Post']);


                    $ip= $this->getRequestIP();
                    $this->doActivityLog(
                        $post,
                        Auth::user(),
                        ['ip' => $ip, 'details' => $_SERVER['HTTP_USER_AGENT'] ],
                        LOGNAME_DELETE_POST,
                        $message
                    );
                    $res['success'] = $message;
                    return $res;
                }
                else
                {
                    abort(403);
                }
            }
            else
            {
                abort(403);
            }
        }
        catch(Exception $e)
        {
            //dd($e->getMessage());
        }
    }
}