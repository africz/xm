<?php

namespace App\Http\Controllers;

use Config;
use ErrorException;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class TestController extends Controller
{
    private const INTRADAY = 'TIME_SERIES_INTRADAY';

    public function getSymbols(Request $request): string
    {
        try {

            $validator = Validator::make($request->all(), [
                'symbol' => 'required',
                'function' => 'required',
            ]);

            if ($validator->fails()) {
                throw new ErrorException('Validator failed.');
            }

            $input = $request->all();
            $functionPath = '';
            switch ($input['function']) {
                case self::INTRADAY:
                    $functionPath = 'IntraDay';
                    break;
                default:
                    throw new ErrorException('Test file path not available.');
            }
            $testFilePath = Config::get('symbols.symbols_test_data') . DIRECTORY_SEPARATOR .
                $functionPath . DIRECTORY_SEPARATOR . strtolower($input['symbol']) . '.json';

            $content = file_get_contents(base_path($testFilePath), true);
        } catch (ErrorException $e) {
            echo ($e->getMessage());
            exit(1);
        }
        return $content;
    }
}