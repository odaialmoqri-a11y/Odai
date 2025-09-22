<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */
namespace App\Http\Controllers\Teacher\Payroll;

use App\Http\Resources\Payroll\TransactionListResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PayrollTransaction;
use App\Models\Payroll;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         return view('/teacher/payroll/transaction/index');
    }

    public function list()
    {
        $transaction=PayrollTransaction::with('user')->where([['school_id',Auth::user()->school_id],['staff_id',Auth::id()]])->get();
        $transaction=TransactionListResource::collection($transaction);
        return $transaction;
    }
}