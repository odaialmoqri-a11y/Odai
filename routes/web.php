<?php

use App\Http\Controllers\Superadmin\DashboardController;

/*Route::get('/', function () {
  return redirect()->route('login');
    //return view('welcome');
});*/
Route::get('/', 'WelcomeController');


Auth::routes();



//Impersonate as teacher
Route::get('/teacher/{id}/impersonate', 'Auth\ImpersonateController@impersonate')->middleware('auth', 'schooladmin');
Route::get('/library/{id}/impersonate', 'Auth\ImpersonateController@librarianimpersonate')->middleware('auth', 'schooladmin');
Route::get('/student/{id}/impersonate', 'Auth\ImpersonateController@studentimpersonate')->middleware('auth', 'schooladmin');
Route::get('/teacher/impersonate/stop', 'Auth\ImpersonateController@stopImpersonate');

Route::get('/schooladmin/{id}/impersonate', 'Auth\ImpersonateController@schoolAdminimpersonate')->middleware('auth', 'superadmin');

//Reset Password for member
Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');
//Email Verification for Member
Route::get('/emailverification/{token}', 'Auth\EmailVerificationController@emailverification');
// OTP Verification
Route::get('/checksms', 'TestController@checksms');
Route::get('/verifyotp', 'OTPController@create');
Route::post('/verifyotp', 'OTPController@store');
//siteadmin
Route::group(['middleware' => ['siteadmin'], 'namespace' => 'Admin'], function () {
    Route::get('/payment/subscription', 'PaymentController@Subscription');
});

// Route::get('/superadmin/dashboard', 'Superadmin\DashboardController@index')->name( 'dashboard');


// Route::group(['middleware' => ['superadmin','auth'], 'namespace' => 'Superadmin'], function () {
//    Route::get('/superadmin/dashboard', 'DashboardController@index')->name('dashboard');
// });
//test pages hidden
  /*video chat room*/ 
  /*Route::view('/video-chat-grid', 'pages.video.grid');
  Route::view('/video-chat-collaboration', 'pages.video.collaboration');
  Route::view('/video-chat-tile', 'pages.video.tile');
  Route::view('/video-chat-presentation', 'pages.video.presentation');*/
  /*Route::view('/admission-form', 'pages.admission.admission');*/
//test pages hidden

//user list
Route::get('/demo/schoolList', 'Demo\WelcomeController@schoolList');
Route::get('/demo/list/{school_id}', 'Demo\WelcomeController@list');

Route::get('/cache-clear', function () {
    Artisan::call('cache:clear');
});

Route::get('/{slug}/standardlist','AdmissionController@list');
Route::get( '/{slug}/admission-form', 'AdmissionController@create' );
Route::post( '/{slug}/admission-form', 'AdmissionController@store' );


Route::post( '/{slug}/admission-form/validationAvatar', 'AdmissionController@validationAvatar' );
Route::post( '/{slug}/admission-form/validationFatherAvatar', 'AdmissionController@validationFatherAvatar' );
Route::post( '/{slug}/admission-form/validationMotherAvatar', 'AdmissionController@validationMotherAvatar' );
Route::post( '/{slug}/admission-form/validationStandard', 'AdmissionController@validationStandard' );
Route::post( '/{slug}/admission-form/validationStudentDetail', 'AdmissionController@validationStudentDetail' );
Route::post( '/{slug}/admission-form/validationAcademicDetail', 'AdmissionController@validationAcademicDetail' );
Route::post( '/{slug}/admission-form/validationParentDetail', 'AdmissionController@validationParentDetail' );
Route::post( '/{slug}/admission-form/validationPersonalDetail', 'AdmissionController@validationPersonalDetail' );






Route::group(['middleware' => ['superadmin','auth'],'prefix'=>'superadmin', 'namespace' => 'Superadmin'], function () {

      Route::get('/dashboard', 'DashboardSuperController@index')->name('superadmin.dashboard');
      
   //Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

   //Contact
   Route::get('reports/contact', function () {
        return view('superadmin.reports.contactlist');
    })->name('superadmin.reports.contactlist');

   //School
   Route::get('academics/school/create', function () {
        return view('superadmin.academics.createschool');
    })->name('superadmin.academics.school.create');

   Route::get('academics/schools', function () {
        return view('superadmin.academics.schoollist');
    })->name('superadmin.academics.schoollist');

   Route::get('academics/school/update/{id}', function ($id) {
        return view('superadmin.academics.createschool',compact('id'));
    })->name('superadmin.academics.school.update');

   Route::get('academics/school/detail/{id}', function ($id) {
        return view('superadmin.academics.schooldetail',compact('id'));
    })->name('superadmin.academics.school.detail');

   //Admin

   Route::get('academics/school/admin/create/{id}', function ($id) {
        return view('superadmin.academics.adminform', compact('id'));
    })->name('superadmin.academics.school.createadmin');

   Route::get('academics/school/admin/update/{id}', function ($id) {
        return view('superadmin.academics.adminform',compact('id'));
    })->name('superadmin.academics.school.updateadmin');

   Route::get('academics/school/admin/detail/{id}', function ($id) {
        return view('superadmin.academics.admindetail',compact('id'));
    })->name('superadmin.academics.school.admindetail');

   Route::get('academics/school/admin/list/{id}', function ($id) {
        return view('superadmin.academics.adminlist', compact($id));
    })->name('superadmin.academics.school.adminlist');

   Route::get('academics/school/user/list/{id}', function ($id) {
        return view('superadmin.academics.userlist', compact($id));
    })->name('superadmin.academics.school.userlist');

   Route::get('academics/school/user/detail/{id}', function ($id) {
        return view('superadmin.academics.userdetail',compact('id'));
    })->name('superadmin.academics.school.userdetail');

   Route::get('academics/school/userprofile/create/{id}', function ($id) {
        return view('superadmin.academics.userprofileform', compact('id'));
    })->name('superadmin.academics.school.createuserprofile');

   Route::get('academics/school/userprofile/detail/{id}', function ($id) {
        return view('superadmin.academics.userprofiledetail',compact('id'));
    })->name('superadmin.academics.school.userprofiledetail');

   Route::get('academics/school/userprofile/update/{id}', function ($id) {
        return view('superadmin.academics.userprofileform', compact('id'));
    })->name('superadmin.academics.school.updateuserprofile');

   //Setting

   //City
   Route::get('setting/cities', function () {
        return view('superadmin.setting.cities');
    })->name('superadmin.setting.cities');

   Route::get('setting/city/create', function () {
        return view('superadmin.setting.cityform');
    })->name('superadmin.setting.cities.create');

   Route::get('setting/city/update/{id}', function ($id) {
        return view('superadmin.setting.cityform', compact('id'));
    })->name('superadmin.setting.cities.update');

   Route::get('setting/city/detail/{id}', function ($id) {
        return view('superadmin.setting.citydetail',compact('id'));
    })->name('superadmin.setting.city.detail');

   //Country
   Route::get('setting/countries', function () {
        return view('superadmin.setting.countries');
    })->name('superadmin.setting.countries');

   /*Route::get('setting/country/create', function () {
        return view('superadmin.setting.countryform');
    })->name('superadmin.setting.countries.create');*/

    Route::get('setting/country/update/{id}', function ($id) {
        return view('superadmin.setting.countryform', compact('id'));
    })->name('superadmin.setting.countries.update');

    Route::get('setting/country/detail/{id}', function ($id) {
        return view('superadmin.setting.countrydetail',compact('id'));
    })->name('superadmin.setting.countries.detail');

    //State
    Route::get('setting/states', function () {
        return view('superadmin.setting.states');
    })->name('superadmin.setting.states');

    Route::get('setting/state/update/{id}', function ($id) {
        return view('superadmin.setting.stateform', compact('id'));
    })->name('superadmin.setting.states.update');

    Route::get('setting/state/detail/{id}', function ($id) {
        return view('superadmin.setting.statedetail',compact('id'));
    })->name('superadmin.setting.states.detail');

    //Plan
    Route::get('setting/plans', function () {
        return view('superadmin.setting.planlist');
    })->name('superadmin.setting.planlist');

    Route::get('setting/plan/create', function () {
        return view('superadmin.setting.planform');
    })->name('superadmin.setting.plan.create');

    Route::get('setting/plan/update/{id}', function ($id) {
        return view('superadmin.setting.planform', compact('id'));
    })->name('superadmin.setting.plan.update');

    Route::get('setting/plan/detail/{id}', function ($id) {
        return view('superadmin.setting.plandetail',compact('id'));
    })->name('superadmin.setting.plan.detail');

    //Reports

    //Subscription
    Route::get('reports/subscriptions', function () {
        return view('superadmin.reports.subscriptions');
    })->name('superadmin.reports.subscriptionlist');

    Route::get('reports/subscription/create', function () {
        return view('superadmin.reports.subscriptionform');
    })->name('superadmin.reports.subscription.create');

    Route::get('reports/subscription/update/{id}', function ($id) {
        return view('superadmin.reports.subscriptionform', compact('id'));
    })->name('superadmin.reports.subscription.update');

    Route::get('reports/subscription/detail/{id}', function ($id) {
        return view('superadmin.reports.subscriptiondetail',compact('id'));
    })->name('superadmin.reports.subscription.detail');

    //Change Password
    Route::get('changepassword', function () {
        return view('superadmin.changepassword');
    })->name('superadmin.changepassword');

    //Change Avatar
    Route::get('changeavatar', function () {
        return view('superadmin.changeavatar');
    })->name('superadmin.changeavatar');

});



