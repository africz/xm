<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockData\IntraDayData;
use App\Http\Requests;
use App\Models\Symbols;
use App\Models\SymbolsHistory;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\BaseController as BaseController;
use Validator;
use Config;


class ReportsController extends BaseController
{
    private const LID = 'Reports::';

    private array $stockData = [];
    public function stockreport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'market' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        
        $input = $request->all();
        $this->readCache($input);
                //$stockData = new IntraDayData($data);

        if (empty($data)) {
            //read from table
        }


        return $this->sendResponse(null, $input);
    }

    private function readCache($input): void
    {
        try {
            foreach ($input['symbols'] as $key => $symbol) {
                $json = Redis::get(Config::get('symbols.redis_key') . $input['market'] . '_' . $symbol);
                if (!empty($json)) {
                    $this->stockData[$symbol] = json_decode($json, true);
                    Log::debug(self::LID . __FUNCTION__ . ':symbol:' . $symbol . ':retrieved from cache:', $this->stockData[$symbol]);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
