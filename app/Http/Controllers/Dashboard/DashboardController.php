<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /*
     * another way to control dashboard authentication
        public function __construct(){
        $this->middleware(['auth'])->except('');
    }
    */

    public  function index(){
        return view('dashboard.index');
    }
}
