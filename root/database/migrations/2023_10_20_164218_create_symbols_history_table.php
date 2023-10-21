<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('symbols_history', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->length(10);
            $table->dateTime('time');
            $table->float('open');
            $table->float('high');
            $table->float('low');
            $table->float('close');
            $table->integer('volume');
            $table->timestamps();
            $table->index([
                'time',
                'symbol',
            ], 'TimeSymbol');
            $table->index([
                'symbol',
                'time',
            ], 'SymbolTime');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('symbols_history');
    }
};
