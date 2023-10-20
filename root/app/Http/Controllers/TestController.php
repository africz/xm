<?php

namespace App\Http\Controllers;

use Config;
use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class TestController extends Controller
{

    public function getSymbols(Request $request):string
    {
        try {
            $content=file_get_contents(base_path(Config::get('symbols.symbols_test_data')), true);
        } catch (FileNotFoundException $exception) {
            die("The file doesn't exist");
        }
        return $content;
    }
}