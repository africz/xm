<?php

namespace App\Models\StockData;

use Illuminate\BaseModel;

class StockData
{
    protected array $data = [];
    protected array $metaData = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

}
