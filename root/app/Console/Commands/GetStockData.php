<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Config;


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

    private const FUNCTION = 'TIME_SERIES_DAILY';
    private const LID = 'GetStockData::';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info(self::LID . __FUNCTION__ . 'start execution');
        $this->symbols = Config::get('symbols.usa');
        try {
            if (
                !empty($this->symbols) &&
                is_array($this->symbols) &&
                (count($this->symbols) > 0)
            ) {
                foreach ($this->symbols as $symbol) {
                    $tmp = Config::get('symbols.api_url');

                    $api_url = sprintf(Config::get('symbols.api_url'), self::FUNCTION , $symbol, Config::get('symbols.api_key'));
                    $json = file_get_contents($api_url);

                    $data = json_decode($json, true);
                    Log::debug(self::LID . __FUNCTION__ . ':' . $symbol . ':received data:', $data);
                }

            }
        } catch (\Exception $e) {
            Log::error(self::LID . __FUNCTION__ . ':' . $e->getMessage());
            exit(1);
        }
        Log::info(self::LID . __FUNCTION__ . ':'.'end execution');
        exit(0);
    }
}
