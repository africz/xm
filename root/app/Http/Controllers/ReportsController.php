<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Symbols;
use App\Models\SymbolsHistory;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\BaseController as BaseController;



class ReportsController  extends BaseController
{
    public function stockreport()
    {
        //return Article::all();
        return $this->sendResponse(null, 'xx');
    }


}
