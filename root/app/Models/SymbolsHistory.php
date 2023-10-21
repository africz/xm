<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SymbolsHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'symbols_history';
    protected $fillable = [
        'symbol',
        'time',
        'open',
        'high',
        'low',
        'close',
        'volume'
    ];
}
