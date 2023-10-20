<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('symbols', function (Blueprint $table) {
            $table->id();
            $table->string('market')->length(10);
            $table->string('symbol')->length(10);
            $table->string('timezone')->length(20);
            $table->timestamps();
            $table->unique([
                'market',
                'symbol',
                'timezone',
            ], 'MarketSymbol');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('symbols');
    }
};
