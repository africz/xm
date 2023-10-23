<?php

namespace App\Http\Controllers;

use DateTime;
use DB;
use Illuminate\Http\Request;
use App\Models\StockData\IntraDayData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\BaseController as BaseController;
use Validator;
use Config;
use Illuminate\Support\Facades\RateLimiter;


class ReportsController extends BaseController
{
    private const LID = 'Reports::';
    private const INTERVAL = ['1min' => '1min', '5min' => '5min'];

    private array $stockData = [];
    private string $market = "";
    private array $symbols = [];
    private array $report = [];

    private DateTime $devNow;

    public function stockreport(Request $request)
    {
        try {
            $this->devNow = new DateTime('2023-10-20 09:40:00', new \DateTimeZone('EST')); //just for develop to being independent from real time        

            if (RateLimiter::tooManyAttempts('stockreport', $perMinute = Config::get('api.reports_rate_limit', 10))) {
                return $this->sendError('Too many attempts,' . $perMinute . ' calls allowed per minute.');
            }

            RateLimiter::hit('stockreport');

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
            }
            return $this->sendResponse(null, $this->report);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode());
        }


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
        $this->report['symbols'][$symbol]['open'] = $timeSeries[0]['open'];
        $this->report['symbols'][$symbol]['high'] = $timeSeries[0]['high'];
        $this->report['symbols'][$symbol]['low'] = $timeSeries[0]['low'];
        $this->report['symbols'][$symbol]['close'] = $timeSeries[0]['close'];
        $this->report['symbols'][$symbol]['volume'] = $timeSeries[0]['volume'];
        $this->report['symbols'][$symbol]['open%'] = $this->calculatePercentage($timeSeries[0]['open'], $timeSeries[1]['open']);
        $this->report['symbols'][$symbol]['high%'] = $this->calculatePercentage($timeSeries[0]['high'], $timeSeries[1]['high']);
        $this->report['symbols'][$symbol]['low%'] = $this->calculatePercentage($timeSeries[0]['low'], $timeSeries[1]['low']);
        $this->report['symbols'][$symbol]['close%'] = $this->calculatePercentage($timeSeries[0]['close'], $timeSeries[1]['close']);

    }

    private function calculatePercentage(float $current, float $previous): float
    {
        return round(($current - $previous) / $previous * 100, 2);
    }

    private function readDb(): void
    {
        try {
            foreach ($this->symbols as $key => $symbol) {

                $symbolId = DB::table('symbols')
                    ->where('market', '=', $this->market)
                    ->where('symbol', '=', $symbol)
                    ->get();
                if (!empty($symbolId[0]->id)) {
                    $lastRecord = DB::table('symbols_history')
                        ->where('symbols_id', '=', $symbolId[0]->id)
                        ->orderByDesc('id')
                        ->get();
                    $this->report['symbols'][$symbol]['open'] = $lastRecord[0]->open;
                    $this->report['symbols'][$symbol]['high'] = $lastRecord[0]->high;
                    $this->report['symbols'][$symbol]['low'] = $lastRecord[0]->low;
                    $this->report['symbols'][$symbol]['close'] = $lastRecord[0]->close;
                    $this->report['symbols'][$symbol]['volume'] = $lastRecord[0]->volume;
                    $this->report['symbols'][$symbol]['open%'] = $this->calculatePercentage($lastRecord[0]->open, $lastRecord[1]->open);
                    $this->report['symbols'][$symbol]['high%'] = $this->calculatePercentage($lastRecord[0]->high, $lastRecord[1]->high);
                    $this->report['symbols'][$symbol]['low%'] = $this->calculatePercentage($lastRecord[0]->low, $lastRecord[1]->low);
                    $this->report['symbols'][$symbol]['close%'] = $this->calculatePercentage($lastRecord[0]->close, $lastRecord[1]->close);
                }
            }

            if (empty($this->stockData)) {
                throw new \Exception('Database is empty, get data first with command');
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

}
