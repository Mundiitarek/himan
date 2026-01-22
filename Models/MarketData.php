<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketData extends Model
{
    // لو اسم الجدول market_data زي ما عندك، سيبه كده.
    // لو اسم الجدول مختلف احذف السطر ده.
    protected $table = 'market_data';

    protected $fillable = [
        'pair_id',
        'currency_id',
        'symbol',
        'price',
        'html_classes',
        'percent_change_1h',
    ];

    protected $casts = [
        'html_classes' => 'object',
    ];
}
