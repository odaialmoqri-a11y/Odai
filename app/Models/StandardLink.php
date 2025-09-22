<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */
namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class StandardLink extends Model
{
    //

    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'standards_link';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'school_id' , 'academic_year_id', 'class_teacher_id' , 'standard_id' , 'section_id' , 'no_of_students' , 'stream' , 'status'
    ];

    protected $with=['standard' , 'section'];

    public function school()
    {
        return $this->belongsTo('\App\Models\School','school_id');
    }

    public function academicYear()
    {
        return $this->belongsTo('\App\Models\AcademicYear','academic_year_id');
    }

    public function teacher()
    {
        return $this->belongsTo('\App\Models\User','class_teacher_id')->where('usergroup_id',5);
    }

    public function standard()
    {
        return $this->belongsTo('\App\Models\Standard','standard_id');
    }

    public function section()
    {
        return $this->belongsTo('\App\Models\Section','section_id');
    }

    public function studentAcademic()
    {
        return $this->hasMany('App\Models\StudentAcademic','standardLink_id','id');
    }

    public function temp_timetable()
    {
        return $this->hasMany('\Gegok12\Timetable\Models\TempTimetable','standardLink_id','id');
    }

     public function getLanguage_SubjectAttribute()
    {
        return $this->hasMany('\Gegok12\Timetable\Models\TempTimetable','standardLink_id','id')->where('subject_type','language')->get();
    }

    public function getTimeTableCountAttribute()
    {
       return $this->hasMany('\Gegok12\Timetable\Models\TempTimetable','standardLink_id','id')->count();
    
    }
    
   
  /*  public function temp_timetable_monday()
    {
        return $this->hasMany('\App\Models\TempTimetable','standardLink_id','id')->where('day','Monday')->orderBy('schedule');
    }

    public function temp_timetable_tuesday()
    {
        return $this->hasMany('\App\Models\TempTimetable','standardLink_id','id')->where('day','Tuesday')->orderBy('schedule');
    }

    public function temp_timetable_wednesday()
    {
        return $this->hasMany('\App\Models\TempTimetable','standardLink_id','id')->where('day','Wednesday')->orderBy('schedule');
    }

    public function temp_timetable_thursday()
    {
        return $this->hasMany('\App\Models\TempTimetable','standardLink_id','id')->where('day','Thursday')->orderBy('schedule');
    }

    public function temp_timetable_friday()
    {
        return $this->hasMany('\App\Models\TempTimetable','standardLink_id','id')->where('day','Friday')->orderBy('schedule');
    }
*/
    public function teacherlink()
    {
        return $this->hasMany('\App\Models\Teacherlink','standardLink_id','id');
    }


    public function events()
    {
      return $this->hasMany('App\Models\Events','standard_id','id');
    }

    public function exam()
    {
      return $this->hasMany('App\Models\Exam','standard_id','id');
    }

    public function mark()
    {
        return $this->hasMany('App\Models\Mark','standard_id','id');
    }

    public function homework()
    {
        return $this->hasMany('App\Models\Homework','standardLink_id','id');
    }

    public function attendance()
    {
        return $this->hasMany('\App\Models\Attendance','standardLink_id','id');
    }

    public function timetable()
    {
        return $this->hasMany('\Gegok12\Timetable\Models\Timetable','standardLink_id','id');
    }

    public function assignment()
    {
        return $this->hasMany('\App\Models\Assignment','standardLink_id','id');
    }

    public function fee()
    {
        return $this->hasMany('\App\Models\Fee','standardLink_id','id');
    }
   
    public function getTeacherLinkDetails()
    {
        $i = 0;
        $array = [];
        foreach ($this->teacherlink as $teacher) 
        { 

            $array['inputs'][$i]['subject_id']  = $teacher->subject_id;
            $array['inputs'][$i]['teacher_id']  = $teacher->teacher_id;
            $array['inputs'][$i]['subject_type']  = $teacher->subject_type;
            $array['inputs'][$i]['no_of_periods']  = $teacher->no_of_periods;
           
            $i++;
        }

        return $array;
    }

    public function getStandardSectionAttribute()
    { 
        if( ($this->standard->name == 'PREKG') || ($this->standard->name == 'prekg') )
        {
            $standard_name = 'PREKG';
        }
        elseif( ($this->standard->name == 'LKG') || ($this->standard->name == 'lkg') )
        {
            $standard_name = 'LKG';
        }
        elseif ( ($this->standard->name == 'UKG') || ($this->standard->name == 'ukg') ) 
        {
            $standard_name = 'UKG';
        }
        else
        {
            if($this->standard != null)
            {
                $standard_name = $this->standard->present()->integerToRoman($this->standard->name);
            }
        }
        return $standard_name.' - '.$this->section->name;
    }

    public function getStandardNameAttribute()
    { 
        if( ($this->standard->name == 'PREKG') || ($this->standard->name == 'prekg') )
        {
            $standard_name = 'PREKG';
        }
        elseif( ($this->standard->name == 'LKG') || ($this->standard->name == 'lkg') )
        {
            $standard_name = 'LKG';
        }
        elseif ( ($this->standard->name == 'UKG') || ($this->standard->name == 'ukg') ) 
        {
            $standard_name = 'UKG';
        }
        else
        {
            if($this->standard != null)
            {
                $standard_name = $this->standard->present()->integerToRoman($this->standard->name);
            }
        }

        return $standard_name;
    }

    public function getLanguageAttribute()
    {

        $mentry=$this->hasMany('\App\Models\Teacherlink','standardLink_id','id')->where('subject_type','language')->get();
        
             $language=$mentry;

             return $language;

         }

    public function getGroupSubjectAttribute()
    {
        $mentry=$this->hasMany('\App\Models\Teacherlink','standardLink_id','id')->where('subject_type','group_dedicated_subject')->get();
        
             $group_subject=$mentry;

             return $group_subject;

    }

    public function getParingSubjectAttribute()
    {
         
         $language=$this->hasMany('\App\Models\Teacherlink','standardLink_id','id')->where('subject_type','language')->get();

         //dd($language);
         $science=$this->hasMany('\App\Models\Teacherlink','standardLink_id','id')->where('subject_type','science')->get();
         $arts=$this->hasMany('\App\Models\Teacherlink','standardLink_id','id')->where('subject_type','arts')->get();
         $group_subject=$this->hasMany('\App\Models\Teacherlink','standardLink_id','id')->where('subject_type','group_dedicated_subject')->get();
        $count=$this->hasMany('\App\Models\Teacherlink','standardLink_id','id')->count();
    
       
             $paring_subject=['','',['',''],['',''],['',''],['','']];
            

       for($i=0;$i<3;$i++)
        {
            $paring_subject[$i] = $language[$i];  
            $paring_subject[$i+1] =  $language[$i+1];
            $paring_subject[$i+2] =  $language[$i+2];   

            $paring_subject[$i+3][$i] = $science[$i];    
            $paring_subject[$i+3][$i+1] = $arts[$i];

             $paring_subject[$i+4][$i] = $science[$i+1];     
            $paring_subject[$i+4][$i+1] = $arts[$i+1];

             $paring_subject[$i+5][$i] =  $science[$i+2];   
            $paring_subject[$i+5][$i+1] = $arts[$i+2];

             $paring_subject[$i+6][$i] =  $group_subject[$i];    
            $paring_subject[$i+6][$i+1] = $group_subject[$i+1];
            $paring_subject[$i+6][$i+2] = $group_subject[$i+2];

            break;
           
       }
             return $paring_subject;

         }

    public function getAll()
    {

        //return [];


      return $this->hasMany('\Gegok12\Timetable\Models\TempTimetable','standardLink_id','id')->orderBy('schedule')->get();

  }
       

//     public function getMondayAttribute()
//     {

//         $mdata=$this->getAll();



//         $mentry=$mdata->where('day','Monday');

//         //dd($mentry);

//         //$mentry=$this->hasMany('\App\Models\TempTimetable','standardLink_id','id')->orderBy('schedule')->where('day','Monday')->get();
        
//         // $mcount=$this->hasMany('\App\Models\TempTimetable','standardLink_id','id')->where('day','Monday')->count();
//           $mcount=$mentry->count();
       
//         if($mcount==8)
//         {
//              $monday=['free','free','free','free','free','free','free','free'];
      
//         for($i=0;$i<$mcount;$i++)
//         {
//            if($mentry[$i]->schedule==0)
//            {
//             $monday[$mentry[$i]->schedule]=$mentry[$i];
//            }
//          if($mentry[$i]->schedule==1)
//            {
//             $monday[$mentry[$i]->schedule]=$mentry[$i];
//         }
//          if($mentry[$i]->schedule==2)
//            {
//             $monday[$mentry[$i]->schedule]=$mentry[$i];
//         }
//          if($mentry[$i]->schedule==3)
//            {
//             $monday[$mentry[$i]->schedule]=$mentry[$i];
//         }
//          if($mentry[$i]->schedule==4)
//            {
//             $monday[$mentry[$i]->schedule]=$mentry[$i];
//         }
//          if($mentry[$i]->schedule==5)
//            {
//             $monday[$mentry[$i]->schedule]=$mentry[$i];
//         }
//          if($mentry[$i]->schedule==6)
//            {
//             $monday[$mentry[$i]->schedule]=$mentry[$i];
//         }
//         if($mentry[$i]->schedule==7)
//            {
//             $monday[$mentry[$i]->schedule]=$mentry[$i];
//         }
           
//         }
// }
// else{
//     $monday=$mentry;
// }
// return $monday;

//     }
//      public function getTuesdayAttribute()
//     {
//        //$tuentry=$this->getAll()->where('day','Tuesday')->get();

//         $tuentry=$this->getAll()->where('day','Tuesday');

//         //dd($tuentry);
//         // $tuentry=$this->hasMany('\App\Models\TempTimetable','standardLink_id','id')->where('day','Tuesday')->orderBy('schedule')->get();
//         // $tucount=$this->hasMany('\App\Models\TempTimetable','standardLink_id','id')->where('day','Tuesday')->count();

//          $tucount=$tuentry->count();
      
//         if($tucount==8)
//         {
//              $tuesday=['free','free','free','free','free','free','free','free'];

//         for($i=0;$i<$tucount;$i++)
//         {
//            if($tuentry[$i]->schedule==0)
//            {
//             $tuesday[$tuentry[$i]->schedule]=$tuentry[$i];
//         }
//          if($tuentry[$i]->schedule==1)
//            {
//             $tuesday[$tuentry[$i]->schedule]=$tuentry[$i];
//         }
//          if($tuentry[$i]->schedule==2)
//            {
//             $tuesday[$tuentry[$i]->schedule]=$tuentry[$i];
//         }
//          if($tuentry[$i]->schedule==3)
//            {
//             $tuesday[$tuentry[$i]->schedule]=$tuentry[$i];
//         }
//          if($tuentry[$i]->schedule==4)
//            {
//             $tuesday[$tuentry[$i]->schedule]=$tuentry[$i];
//         }
//          if($tuentry[$i]->schedule==5)
//            {
//             $tuesday[$tuentry[$i]->schedule]=$tuentry[$i];
//         }
//          if($tuentry[$i]->schedule==6)
//            {
//             $tuesday[$tuentry[$i]->schedule]=$tuentry[$i];
//         }
//         if($tuentry[$i]->schedule==7)
//            {
//             $tuesday[$tuentry[$i]->schedule]=$tuentry[$i];
//         }
           
//         }
// }
// else
// {
//     $tuesday=$tuentry;
// }
// return $tuesday;

//     }

//      public function getWednesdayAttribute()
//     {

//         $wentry=$this->getAll()->where('day','Wednesday');
//         $wcount=$wentry->count();

//         // $wentry=$this->hasMany('\App\Models\TempTimetable','standardLink_id','id')->where('day','Wednesday')->orderBy('schedule')->get();
//         // $wcount=$this->hasMany('\App\Models\TempTimetable','standardLink_id','id')->where('day','Wednesday')->count();

//         $wcount=$wentry->count();

        
      
//         if($wcount==8)
//         {
//             $wednesday=['free','free','free','free','free','free','free','free'];
//         for($i=0;$i<$wcount;$i++)
//         {
//            if($wentry[$i]->schedule==0)
//            {
//             $wednesday[$wentry[$i]->schedule]=$wentry[$i];
//            }
//          if($wentry[$i]->schedule==1)
//            {
//             $wednesday[$wentry[$i]->schedule]=$wentry[$i];
//         }
//          if($wentry[$i]->schedule==2)
//            {
//             $wednesday[$wentry[$i]->schedule]=$wentry[$i];
//         }
//          if($wentry[$i]->schedule==3)
//            {
//             $wednesday[$wentry[$i]->schedule]=$wentry[$i];
//         }
//          if($wentry[$i]->schedule==4)
//            {
//             $wednesday[$wentry[$i]->schedule]=$wentry[$i];
//         }
//          if($wentry[$i]->schedule==5)
//            {
//             $wednesday[$wentry[$i]->schedule]=$wentry[$i];
//         }
//          if($wentry[$i]->schedule==6)
//            {
//             $wednesday[$wentry[$i]->schedule]=$wentry[$i];
//         }
//         if($wentry[$i]->schedule==7)
//            {
//             $wednesday[$wentry[$i]->schedule]=$wentry[$i];
//         }
           
//         }
// }
// else
// {
//     $wednesday=$wentry;
// }
// return $wednesday;

//     }
//  public function getThursdayAttribute()
//     {
//         $thentry=$this->getAll()->where('day','Thursday');
//         $thcount=$thentry->count();

//         // $thentry=$this->hasMany('\App\Models\TempTimetable','standardLink_id','id')->where('day','Thursday')->orderBy('schedule')->get();
//         // $thcount=$this->hasMany('\App\Models\TempTimetable','standardLink_id','id')->where('day','Thursday')->count();

        
      
//         if($thcount==8)
//         {
//             $thursday=['free','free','free','free','free','free','free','free'];
//         for($i=0;$i<$thcount;$i++)
//         {
//            if($thentry[$i]->schedule==0)
//            {
//             $thursday[$thentry[$i]->schedule]=$thentry[$i];
//         }
//          if($thentry[$i]->schedule==1)
//            {
//             $thursday[$thentry[$i]->schedule]=$thentry[$i];
//         }
//          if($thentry[$i]->schedule==2)
//            {
//             $thursday[$thentry[$i]->schedule]=$thentry[$i];
//         }
//          if($thentry[$i]->schedule==3)
//            {
//             $thursday[$thentry[$i]->schedule]=$thentry[$i];
//         }
//          if($thentry[$i]->schedule==4)
//            {
//             $thursday[$thentry[$i]->schedule]=$thentry[$i];
//         }
//          if($thentry[$i]->schedule==5)
//            {
//             $thursday[$thentry[$i]->schedule]=$thentry[$i];
//         }
//          if($thentry[$i]->schedule==6)
//            {
//             $thursday[$thentry[$i]->schedule]=$thentry[$i];
//         }
//         if($thentry[$i]->schedule==7)
//            {
//             $thursday[$thentry[$i]->schedule]=$thentry[$i];
//         }
           
//         }
// }
// else
// {
//     $thursday=$thentry;
// }
// return $thursday;

//     }
//      public function getFridayAttribute()
//     {
//        $fentry=$this->getAll()->where('day','Thursday');
//         $fcount=$fentry->count();

//         // $fentry=$this->hasMany('\App\Models\TempTimetable','standardLink_id','id')->where('day','Friday')->orderBy('schedule')->get();
//         // $fcount=$this->hasMany('\App\Models\TempTimetable','standardLink_id','id')->where('day','Friday')->count();

      
      
//         if($fcount==8)
//         {
//              $friday=['free','free','free','free','free','free','free','free'];
//         for($i=0;$i<$fcount;$i++)
//         {
//            if($fentry[$i]->schedule==0)
//            {
//             $friday[$fentry[$i]->schedule]=$fentry[$i];
//         }
//          if($fentry[$i]->schedule==1)
//            {
//             $friday[$fentry[$i]->schedule]=$fentry[$i];
//         }
//          if($fentry[$i]->schedule==2)
//            {
//             $friday[$fentry[$i]->schedule]=$fentry[$i];
//         }
//          if($fentry[$i]->schedule==3)
//            {
//             $friday[$fentry[$i]->schedule]=$fentry[$i];
//         }
//          if($fentry[$i]->schedule==4)
//            {
//             $friday[$fentry[$i]->schedule]=$fentry[$i];
//         }
//          if($fentry[$i]->schedule==5)
//            {
//             $friday[$fentry[$i]->schedule]=$fentry[$i];
//         }
//          if($fentry[$i]->schedule==6)
//            {
//             $friday[$fentry[$i]->schedule]=$fentry[$i];
//         }
//         if($fentry[$i]->schedule==7)
//            {
//             $friday[$fentry[$i]->schedule]=$fentry[$i];
//         }
           
//         }
// }
// else
// {
//     $friday=$fentry;
// }
// return $friday;

//     }
} 


