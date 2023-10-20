<?php
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'api_key'=> env('ALPHA_VANTAGE_API_KEY'),
    'api_url'=> env('ALPHA_VANTAGE_API_URL'),
    'usa' => explode(',',env('SYMBOLS_USA')),
];