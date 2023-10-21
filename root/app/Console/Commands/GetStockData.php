<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Symbols;
use App\Models\SymbolsHistory;
use App\Models\StockData\IntraDayData;
use Config;
use DB;
use Carbon\Carbon;

class GetStockData extends Command
{
    private $symbols = [];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-stock-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private const FUNCTION = 'TIME_SERIES_INTRADAY';
    private const LOCALTEST = 'LOCALTEST';
    private const LID = 'GetStockData::';
    private const OUTPUT = ['full' => 'full', 'compact' => 'compact'];
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::info(self::LID . __FUNCTION__ . 'start execution');
        $this->symbols = Config::get('symbols.usa');

        try {
            if (
                empty($this->symbols) &&
                !is_array($this->symbols) &&
                (count($this->symbols) < 1)
            ) {
                throw new \Exception('No symbols defined in .env!');
            }

            foreach ($this->symbols as $symbol) {
                $json = $this->getUrlContent($this->getApiUrl($symbol));
                $data = json_decode($json, true);

                Log::debug(self::LID . __FUNCTION__ . ':' . $symbol . ':received data:', $data);

                if (!empty($data)) {
                    $this->saveResult($data);
                }
            }

        } catch (\Exception $e) {
            Log::error(self::LID . __FUNCTION__ . ':' . $e->getMessage());
            exit(1);
        }
        Log::info(self::LID . __FUNCTION__ . ':' . 'end execution');
        exit(0);
    }


    private function getUrlContent($url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $tryToReconnect = Config::get('symbols.try_reconnect', 5);
        $recInterval = Config::get('symbols.reconnect_interval', 30);
        for ($i = 0; $i < $tryToReconnect; $i++) {
            $result = curl_exec($ch);
            if (!curl_errno($ch)) {
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($http_code === 200) {
                    break;
                }
            } else {
                Log::error(self::LID . __FUNCTION__ . ':' . curl_error($ch));
            }
            Log::info(self::LID . __FUNCTION__ . 'try to reconnect(' . ($i+1) . '/' . $tryToReconnect . ') after ' . $recInterval . 'sec sleep');
            sleep($recInterval);
        }
        if ($i >= $tryToReconnect) {
            throw new \Exception('Reconnections are  failed.');
        }
        return $result;
    }

    private function getApiUrl($symbol): string
    {
        $retVal = sprintf(Config::get('symbols.api_url'), self::FUNCTION , $symbol, Config::get('symbols.interval'), self::OUTPUT['compact'], Config::get('symbols.api_key'));
        if (Config::get('symbols.api_mode') === self::LOCALTEST) {
            $retVal = Config::get('symbols.api_test_url');
        }
        Log::debug(self::LID . __FUNCTION__ . ':return:' . $retVal);
        return $retVal;
    }



    private function saveResult(array $data): void
    {
        try {
            $stockData = new IntraDayData($data);
            DB::beginTransaction();
            $this->saveSymbolsHistory($stockData);
            $this->saveSymbols($stockData);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(self::LID . __FUNCTION__ . ':' . $e->getMessage());
        }
    }
    private function saveSymbols(IntraDayData $stockData): void
    {
        $symbols = new Symbols();

        $symbols->market = Config::get('symbols.market');
        $symbols->symbol = $stockData->getSymbol();
        $symbols->timezone = $stockData->getTimeZone();

        $findResult = DB::table('symbols')
            ->where('market', '=', Config::get('symbols.market'))
            ->where('symbol', '=', $stockData->getSymbol())
            ->where('timezone', '=', $stockData->getTimeZone())
            ->update(['updated_at' => Carbon::now()->toDateTimeString()]);
        if (!$findResult) {
            $symbols->save();
        }
    }

    private function saveSymbolsHistory(IntraDayData $stockData): void
    {
        $timeSeries = $stockData->getTimeSeries(Config::get('symbols.interval'));

        if (!count($timeSeries)) {
            return;
        }
        foreach ($timeSeries as $key => $value) {
            $symbolsHistory = new SymbolsHistory();
            $symbolsHistory->symbol = $stockData->getSymbol();
            $symbolsHistory->time = $value['time'];
            $symbolsHistory->open = $value['open'];
            $symbolsHistory->high = $value['high'];
            $symbolsHistory->low = $value['low'];
            $symbolsHistory->close = $value['close'];
            $symbolsHistory->volume = $value['volume'];
            $symbolsHistory->save();
        }


    }
}
