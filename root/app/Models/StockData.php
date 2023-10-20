<?php

namespace App\Models;

use Illuminate\BaseModel;

class StockData
{
    protected array $data = [];
    protected array $metaData = [];
    protected const MD = [
        'MetaData' => 'Meta Data',
        'Information' => '1. Information',
        'Symbol' => '2. Symbol',
        'Refreshed' => '3. Last Refreshed',
        'TimeZone' => '5. Time Zone',
    ];
    //  "Meta Data": {
    //     "1. Information": "Daily Prices (open, high, low, close) and Volumes",
    //     "2. Symbol": "SEDG",
    //     "3. Last Refreshed": "2023-10-19",
    //     "4. Output Size": "Compact",
    //     "5. Time Zone": "US/Eastern"
    // },
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    public function getSymbol(): string
    {
        return $this->data[self::MD['MetaData']][self::MD['Symbol']];
    }
    public function getTimeZone(): string
    {
        return $this->data[self::MD['MetaData']][self::MD['TimeZone']];
    }

}
