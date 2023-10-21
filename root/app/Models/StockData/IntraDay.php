<?php

namespace App\Models\StockData;

use Illuminate\BaseModel;

class IntraDay extends StockData
{
    protected const MD = [
        'MetaData' => 'Meta Data',
        'Information' => '1. Information',
        'Symbol' => '2. Symbol',
        'Refreshed' => '3. Last Refreshed',
        'Interval' => '4. Interval',
        'OutputSize' => '5. Output Size',
        'TimeZone' => '6. Time Zone',

    ];
    protected const TS = [
        'TimeSeries' => 'Time Series (5min)',
        'open' => '1. open',
        'high' => '2. high',
        'low' => '3. low',
        'close' => '4. close',
        'volume' => '5. volume',
    ];


    // "Meta Data": {
    //     "1. Information": "Intraday (5min) open, high, low, close prices and volume",
    //     "2. Symbol": "IBM",
    //     "3. Last Refreshed": "2023-10-20 19:55:00",
    //     "4. Interval": "5min",
    //     "5. Output Size": "Compact",
    //     "6. Time Zone": "US/Eastern"
    // },
    // "Time Series (5min)": {
    //     "2023-10-20 19:55:00": {
    //         "1. open": "137.1500",
    //         "2. high": "137.1500",
    //         "3. low": "137.1000",
    //         "4. close": "137.1000",
    //         "5. volume": "26"
    //     },
    //     "2023-10-20 19:35:00": {
    //         "1. open": "137.0700",
    //         "2. high": "137.0700",
    //         "3. low": "137.0700",
    //         "4. close": "137.0700",
    //         "5. volume": "1"
    //     },
    //     "2023-10-20 19:30:00": {
    //         "1. open": "137.1500",
    //         "2. high": "137.1500",
    //         "3. low": "137.1500",
    //         "4. close": "137.1500",
    //         "5. volume": "18"
    //     },
    //     "2023-10-20 19:25:00": {
    //         "1. open": "137.3500",
    //         "2. high": "137.3500",
    //         "3. low": "137.2800",
    //         "4. close": "137.2800",
    //         "5. volume": "11"
    //     },

    public function __construct(array $data)
    {
        parent::__construct($data);
    }
    public function getSymbol(): string
    {
        return $this->data[self::MD['MetaData']][self::MD['Symbol']];
    }
    public function getTimeZone(): string
    {
        return $this->data[self::MD['MetaData']][self::MD['TimeZone']];
    }
    public function getTimeSeries(): string
    {
        return $this->data[self::TS['TimeSeries']];
    }
    public function getTimeSeriesByDate($date): string
    {
        return $this->data[self::TS['TimeSeries'][$date]];
    }

}
