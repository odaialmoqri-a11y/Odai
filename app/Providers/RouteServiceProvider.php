<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';
    protected $adminNamespace = 'App\Http\Controllers\Admin';
    protected $inventoryNamespace = 'App\Http\Controllers\Staff';
    protected $stockNamespace = 'App\Http\Controllers\Stock';
    protected $librarianNamespace = 'App\Http\Controllers\Librarian';
    protected $studentNamespace = 'App\Http\Controllers\Student';
    protected $teacherNamespace = 'App\Http\Controllers\Teacher';
    protected $receptionistNamespace = 'App\Http\Controllers\Receptionist';
    protected $accountantNamespace = 'App\Http\Controllers\Accountant';
    protected $payrollNamespace = 'App\Http\Controllers\Payroll';
    protected $alumniNamespace = 'App\Http\Controllers\Alumni';
    protected $superadminNamespace = 'App\Http\Controllers\Superadmin';

    //public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        //$this->mapStaticRoutes(); //static routes hidden
        $this->mapAdminRoutes();
        $this->mapSettingsRoutes();
        $this->mapInventoryRoutes();
        $this->mapStockRoutes();
        $this->mapLibrarianRoutes();
        $this->mapStudentRoutes();
        $this->mapTeacherRoutes();
        $this->mapReceptionistRoutes();
        $this->mapAccountantRoutes();
        $this->mapPayrollRoutes();
        $this->mapAlumniRoutes();
        $this->mapSuperadminRoutes();
        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    protected function mapAdminRoutes()
    {
        Route::prefix('admin')
            ->middleware(['web','auth', 'schooladmin', 'privilegeconditions']) //, 'verifyotp'
            ->namespace($this->adminNamespace)
            ->group(base_path('routes/admin.php'));
    }

    protected function mapSettingsRoutes()
    {
        Route::prefix('admin')
            ->middleware(['web','auth', 'schooladmin'])
            ->namespace($this->adminNamespace)
            ->group(base_path('routes/setting.php'));
    }

    protected function mapInventoryRoutes()
    {
        Route::prefix('admin')
            ->middleware(['web','auth', 'schooladmin', 'privilegeconditions'])  //, 'verifyotp'
            ->namespace($this->inventoryNamespace)
            ->group(base_path('routes/inventory.php'));
    }

    protected function mapStockRoutes()
    {
        Route::prefix('stock')
            ->middleware(['web','auth', 'stock'])
            ->namespace($this->stockNamespace)
            ->group(base_path('routes/stock.php'));
    }

    protected function mapLibrarianRoutes()
    {
        Route::prefix('library')
            ->middleware(['web','auth', 'librarian'])
            ->namespace($this->librarianNamespace)
            ->group(base_path('routes/librarian.php'));
    }

    protected function mapStudentRoutes()
    {
        Route::prefix('student')
            ->middleware(['web','auth', 'student'])
            ->namespace($this->studentNamespace)
            ->group(base_path('routes/student.php'));
    }

    /*protected function mapStaticRoutes()  //static routes hidden
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/static.php'));
    }*/

    protected function mapTeacherRoutes()
    {
        Route::prefix('teacher')
            ->middleware(['web','auth', 'teacher'])
            ->namespace($this->teacherNamespace)
            ->group(base_path('routes/teacher.php'));
    }

    protected function mapReceptionistRoutes()
    {
        Route::prefix('receptionist')
            ->middleware(['web','auth', 'receptionist'])
            ->namespace($this->receptionistNamespace)
            ->group(base_path('routes/receptionist.php'));
    }

    protected function mapAccountantRoutes()
    {
        Route::prefix('accountant')
            ->middleware(['web','auth', 'accountant'])
            ->namespace($this->accountantNamespace)
            ->group(base_path('routes/accountant.php'));
    }

    protected function mapPayrollRoutes()
    {
        Route::prefix('accountant')
            ->middleware(['web','auth','adminaccountant'])
            ->namespace($this->payrollNamespace)
            ->group(base_path('routes/payroll.php'));
    }

    protected function mapAlumniRoutes()
    {
        Route::prefix('alumni')
            ->middleware(['web','auth', 'alumni'])
            ->namespace($this->alumniNamespace)
            ->group(base_path('routes/alumni.php'));
    }

    protected function mapSuperadminRoutes()
    {
        Route::prefix('superadmin')
            ->middleware(['web','auth', 'superadmin'])
            ->namespace($this->superadminNamespace)
            ->group(base_path('routes/superadmin.php'));
    }
}