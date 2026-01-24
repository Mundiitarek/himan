<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coin_pairs', function (Blueprint $table) {
            $table->string('tradingview_symbol', 64)->nullable()->after('listed_market_name');
            $table->string('twelvedata_symbol', 64)->nullable()->after('tradingview_symbol');
        });
    }

    public function down(): void
    {
        Schema::table('coin_pairs', function (Blueprint $table) {
            $table->dropColumn(['tradingview_symbol', 'twelvedata_symbol']);
        });
    }
};
