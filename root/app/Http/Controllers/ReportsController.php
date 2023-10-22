<?php

namespace App\Http\Controllers;

use DateTime;
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
    private const INTERVAL = ['1min' => '1min', '5min' => '5min'];

    private array $stockData = [];
    private string $market = "";
    private array $symbols = [];
    private array $report = [];

    private DateTime $devNow ;

    public function stockreport(Request $request)
    {
        $this->devNow = new DateTime('2023-10-20 09:40:00'); //just for develop to being independent from real time        
        $validator = Validator::make($request->all(), [
            'market' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $request->all();

        $this->symbols = $input['symbols'];
        $this->market = $input['market'];

        if ($this->readCache()) {
            $this->processCache();
        } else {
            $this->readDb();
            $this->processDb();
        }

        $result = $this->processRequest($input);

        return $this->sendResponse(null, $result);
    }

    private function readCache(): bool
    {
        try {
            foreach ($this->symbols as $key => $symbol) {
                $json = Redis::get(Config::get('symbols.redis_key') . $this->market . '_' . $symbol);
                if (!empty($json)) {
                    $this->stockData[$symbol] = new IntraDayData(json_decode($json, true));
                    Log::debug(self::LID . __FUNCTION__ . ':symbol:' . $symbol . ':retrieved from cache');
                }
            }
            if (empty($this->stockData)) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function processCache(): void
    {
        try {
            $this->report['market'] = $this->market;

            foreach ($this->symbols as $key => $symbol) {
                $this->processSymbol($symbol);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    private function processSymbol(string $symbol): void
    {

        $this->report['symbols']['symbol'] = $symbol;

        $timeSeries = $this->stockData[$symbol]->getTimeSeries(Config::get('symbols.interval', self::INTERVAL['5min']));

        if (!count($timeSeries)) {
            return;
        }
        foreach ($timeSeries as $key => $value) {
            if ($this->getCurrentTime($value['time'])) {
                // $this->report['symbols']['open'] = $value[''];
                // $this->report['symbols']['high'] = $value[''];
                // $this->report['symbols']['low'] = $value[''];
                // $this->report['symbols']['close'] = $value[''];
                // $this->report['symbols']['volume'] = $value[''];

            }
        }
    }

    private function readDb(): void
    {

    }
    private function processDb(): void
    {

    }

    private function processRequest(array $input): array
    {
        if (!empty($this->stockData)) {

        }

        $retVal = [
            'market' => $this->market,
            'symbols' =>
                [
                    'symbol' => 'IBM',
                    'open' => 0,
                    'high' => 0,
                    'low' => 0,
                    'volume' => 0,
                    'open%' => 0,
                    'high%' => 0,
                    'low%' => 0,
                    'volume%' => 0

                ]
        ];

        return $retVal;
    }

    private function getCurrentTime(string $time): bool
    {
        $now= new DateTime('now');
        if ($this->devNow) {
            $now= $this->devNow;
        }
        $filter=$now->format('Y-m-d h:i:s');

        return true;
    }
    private function getCurrentEST()
    {
        
        return new DateTime('now', new \DateTimeZone('EST'));
    }
}
