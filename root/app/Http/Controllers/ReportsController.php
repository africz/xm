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
use Validator;


class ReportsController  extends BaseController
{
    public function stockreport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'market' => 'required',
            'symbol' => 'required',
            'time' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();

        return $this->sendResponse(null, $input);
    }


}
