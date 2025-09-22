<?php
/**
 * Trait for processing RegisterUser
 */
namespace App\Traits;

use Illuminate\Support\Facades\DB;
use App\Models\Events;
use App\Models\Post;
use App\Models\PostTag;
use App\Models\TeacherProfile;
use App\Models\ParentProfile;
use App\Models\Userprofile;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Mark;
use App\Models\Scholastic;
use App\Models\Tag;
use Carbon\Carbon;
use Exception;

/**
 *
 * @class trait
 * Trait for RegisterUser Processes
 */
trait AutoPostProcess
{
    public function CreateBirthday($data,$school_id,$birth_date,$date,$argument,$image)
    {
        \DB::beginTransaction();
        try
        {       //dd($data->studentAcademic[0][academic_year_id]); 
            if($data->usergroup_id=='6')
            {
                $academic_year_id = $data->studentAcademic[0][academic_year_id];
            }
            else
            {
                $academic_year_id = $data->teacherprofile[0][academic_year_id];
            }

         $eventdata=[
          'school_id'=>$school_id,
          'academic_year_id'=>$academic_year_id,
          'select_type'=>'school',
          'title'=>$data->userprofile->firstname.$data->userprofile->lastname,
          'description'=>'BirthDay',
          'location'=>'school',
          'category'=>'birthday',
          'organised_by'=>'School Management',
          'start_date'=>$birth_date,
          'end_date'=>$birth_date,
        ];

        $event=\Calendar::createEvent($school_id,$academic_year_id,$eventdata);    
           /* $event = new Events;
            $event->school_id    = $school_id;
            if($data->usergroup_id=='6')
            {
                $event->academic_year_id = $data->studentAcademic[0][academic_year_id];
            }
            else
            {
                $event->academic_year_id = $data->teacherprofile[0][academic_year_id];
            }
           
            $event->title=$data->userprofile->firstname.$data->userprofile->lastname;
            $event->description='BirthDay';
            $event->select_type='school';
            $event->standard_id=NULL;
            $event->organised_by='School Management';
            $event->location='School';
            $event->category='birthday';
            $event->start_date=$birth_date;
            $event->end_date=$birth_date;

            $event->save();
*/
            //dump($event);
            
            $post = new Post;

            //dd($data->studentAcademic[0][standardLink_id]);

            $post->school_id     = $school_id;
            if($data->usergroup_id=='6')
            {
                $post->academic_year_id = $data->studentAcademic[0][academic_year_id];
                $post->visibility       = 'select_class';
                $post->visible_for      = $data->studentAcademic[0][standardLink_id];

            }
            else
            {
                $post->academic_year_id = $data->teacherprofile[0][academic_year_id];
                $post->visibility     = 'all_class';
                $post->visible_for     = NULL;
            }
            $post->entity_id  = '2';
            $post->entity_name     = 'App\Models\User';
            $post->description     = $data->userprofile->firstname.' '.$data->userprofile->lastname.' '.'Birthday';

            $post->attachment_file     = '["uploads/images/birthday.jpg"]';


            //dump($post->attachment_file);
            
            $post->post_created_at     = $date;
            //dd($post->post_created_at);
            $post->is_posted     = '1';
            if($argument!=NULL)
            {
                $post->posted_at     = $argument;
            }
            else
            {
                $post->posted_at     = $date;
            }
            $post->tag     = 'Birthday';
            $post->status     = 'posted';
            $post->created_by     = '2';
          
            
            $post->save();

            $tags = explode(",", $post->tag);

            $tagObjects=[];

            foreach($tags as $tag)
            {
                $tag=Tag::firstOrCreate(['tag_name' => $tag]);
                $posttag=PostTag::create(['post_id'=>$post->id,'tag_id'=>$tag->id]);
                //array_push($tagObjects,$tag);
            }

            
             //$post->tag()->saveMany($tagObjects);

          
        \DB::commit();
        return $event;
        }

        catch(Exception $e)
        {
            \DB::rollBack();
           // dd($e->getMessage());
        } 
    }


    public function CreateWorkAnniversary($data,$image,$description,$anniversary_date,$date)
    {
        \DB::beginTransaction();
        try
        {       //dd($data->studentAcademic[0]); 
         
         $academic_year_id=$data->teacherprofile[0][academic_year_id];
         $eventdata=[
          'school_id'=>$data->school_id,
          'academic_year_id'=>$academic_year_id,
          'select_type'=>'school',
          'title'=>$data->userprofile->firstname.$data->userprofile->lastname,
          'description'=>$description,
          'location'=>'school',
          'category'=>'anniversary',
          'organised_by'=>'School Management',
          'start_date'=>$anniversary_date,
          'end_date'=>$anniversary_date,
        ];

        $event=\Calendar::createEvent($school_id,$academic_year_id,$eventdata);        
           /* $event = new Events;
            $event->school_id    = $data->school_id;
            $event->academic_year_id = $data->teacherprofile[0][academic_year_id];
            $event->title=$data->userprofile->firstname.$data->userprofile->lastname;
            $event->description=$description;
            $event->select_type='school';
            $event->standard_id=NULL;
            $event->organised_by='School Management';
            $event->location='School';
            $event->category='anniversary';
            $event->start_date=$anniversary_date;
            $event->end_date=$anniversary_date;

            $event->save();*/

            //dump($event);
            
            $post = new Post;

            $post->school_id     = $data->school_id;
            $post->academic_year_id = $data->teacherprofile[0][academic_year_id];
            $post->entity_id  = '2';
            $post->entity_name     = 'App\Models\User';
            $post->description     = $data->userprofile->firstname.$data->userprofile->lastname.$description;
            $post->attachment_file     = '['.$image.']';
            $post->visibility     = 'all_class';
            $post->visible_for     = NULL;
            $post->post_created_at     = $date;
            //dd($post->post_created_at);
            $post->is_posted     = '1';
            $post->posted_at     = $date;
            $post->tag     = 'Workanniversary';

           /*  $tagObjects=[];

             foreach($post->tag as $tag)
             {
                $tag=Tag::firstOrCreate(['tag_name' => $tag]);
                array_push($tagObjects,$tag);
            }
            
             $post->tag()->saveMany($tagObjects);*/
            //dump($tag);

            $post->status     = 'posted';
            $post->created_by     = '2';
          
            
            $post->save();

            $tags = explode(",", $post->tag);

            $tagObjects=[];

            foreach($tags as $tag)
            {
                $tag=Tag::firstOrCreate(['tag_name' => $tag]);
                $posttag=PostTag::create(['post_id'=>$post->id,'tag_id'=>$tag->id]);
                //array_push($tagObjects,$tag);
            }
            
             //$post->tag()->saveMany($tagObjects);

          
        \DB::commit();
        return $event;
        }

        catch(Exception $e)
        {
            \DB::rollBack();
           // dd($e->getMessage());
        } 
    }


    public function CreateExam($data,$date,$image)
    {
        \DB::beginTransaction();
        try
        {  
            
            $post = new Post;

            $post->school_id     = $data->exam->school_id;
            $post->academic_year_id = $data->exam->academic_year_id;
            $post->entity_id  = '2';
            $post->entity_name     = 'App\Models\Exam';
            $post->description     = $data->standardlink['standard']['name'].'-'.$data->standardlink['section']['name'].'  '. $data->subject->name.' '.'Exam';
            //dd($post->description);
            $post->attachment_file     = '['.$image.']';
            $post->visibility     = 'all_class';
            //$post->visible_for     = $data->exam->standard_id;
            $post->visible_for     = NULL;
            $post->post_created_at     = $date;
            $post->is_posted     = '1';
            $post->posted_at     = $date;
            $post->tag     = 'Exam';

            $post->status     = 'posted';
            $post->created_by     = '2';
          
            
            $post->save();

            $tags = explode(",", $post->tag);

            $tagObjects=[];

            foreach($tags as $tag)
            {
                $tag=Tag::firstOrCreate(['tag_name' => $tag]);
                $posttag=PostTag::create(['post_id'=>$post->id,'tag_id'=>$tag->id]);
                //array_push($tagObjects,$tag);
            }
            
             //$post->tag()->saveMany($tagObjects);

          
        \DB::commit();
        return $exam;
        }

        catch(Exception $e)
        {
            \DB::rollBack();
            //dd($e->getMessage());
        } 
    }

  
}