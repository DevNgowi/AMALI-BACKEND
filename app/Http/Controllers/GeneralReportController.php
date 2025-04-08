<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralReportController extends Controller
{
    public function indexGeneralReports(){
        return view('Reports.index');
    }
}
