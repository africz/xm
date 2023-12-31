<?php
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'api_key' => env('ALPHA_VANTAGE_API_KEY'),
    'api_url' => env('ALPHA_VANTAGE_API_URL'),
    'api_mode' => env('ALPHA_VANTAGE_API_MODE'),
    'api_test_url' => env('ALPHA_VANTAGE_API_TEST_URL'),
    'symbols_test_data' => env('ALPHA_VANTAGE_API_TEST_DATA'),
    'market' => env('ALPHA_VANTAGE_API_MARKET'),
    'usa' => explode(',', env('SYMBOLS_USA')),
    'interval' => env('ALPHA_VANTAGE_API_INTERVAL'),
    'waiting' => env('ALPHA_VANTAGE_API_WAITING'),
    'try_reconnect' => env('ALPHA_VANTAGE_API_TRY_TO_RECONNECT'),
    'reconnect_interval' => env('ALPHA_VANTAGE_API_RECONNECT_INTERVAL'),
    'redis_key' => env('ALPHA_VANTAGE_API_REDIS_KEY'),
    'redis_cache_expire' => env('ALPHA_VANTAGE_API_REDIS_CACHE_EXPIRE')
];