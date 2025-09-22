<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
namespace Database\Factories;

use App\Models\TeacherProfile;
use App\Models\Qualification;
//use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherProfileFactory extends Factory
{
    protected $model = TeacherProfile::class;

    public function definition()
    {


    $qualification      = Qualification::where('status',1)->where('type','teacher')->pluck('id')->toArray();
    $qualification_id   = $this->faker->randomElement($qualification);

    $ug_degree          = Qualification::where('status',1)->where('type','ug')->pluck('id')->toArray();
    $ug_degree          = $this->faker->randomElement($ug_degree);

	$pg_degree 		    = Qualification::where('status',1)->where('type','pg')->pluck('id')->toArray();
	$pg_degree 	        = $this->faker->randomElement($pg_degree);

	$designation 		= $this->faker->randomElement(['assistant_teacher','co_ordinator','head_of_the_department','librarian','others','senior_teacher','vice_principal']);
    if($designation == 'others')
    {
        $sub_designation = 'clerk';
    }
	
	$employee_id 		= $this->faker->randomElement(['1','2','3','4','5']);

    return [
        //
        'qualification_id'  =>  $qualification_id,
        'ug_degree'         =>  $ug_degree,
        'pg_degree'         =>  $pg_degree,
        'designation'       =>  $designation,
    	'sub_designation'   => 	$sub_designation,
    	'employee_id'		=>	$employee_id,
    ];

}

}