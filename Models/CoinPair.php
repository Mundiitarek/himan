<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\ApiQuery;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CoinPair extends Model
{
    use GlobalStatus, ApiQuery;

    // ✅ تصحيح: guarded مش guard
    protected $guarded = ['id'];

    private const TRADINGVIEW_FOREX = [
        'EUR','GBP','JPY','AUD','CAD','NZD','CHF',
        'CNY','HKD','SGD','INR','MXN','ZAR','TRY',
        'XAU','XAG','XPT','XPD','XCU',
    ];

    private const TRADINGVIEW_INDICES = [
        'SPX' => 'SP:SPX',
        'NDX' => 'NASDAQ:NDX',
        'DAX' => 'XETR:DAX',
        'DJI' => 'DJI',
        'RUT' => 'RUSSELL:RUT',
        'VIX' => 'CBOE:VIX',
        'FTSE' => 'FTSE:UKX',
        'CAC' => 'EURONEXT:CAC40',
        'NIKKEI' => 'TSE:N225',
        'HSI' => 'HSI:HSI',
        'SSE' => 'SSE:000001',
    ];

    private const TRADINGVIEW_COMMODITIES = [
        'USOIL' => 'TVC:USOIL',
        'UKOIL' => 'TVC:UKOIL',
        'NATGAS' => 'TVC:NATGAS',
        'XAU' => 'TVC:GOLD',
        'XAG' => 'TVC:SILVER',
    ];

    private const TRADINGVIEW_STOCKS = [
        'AAPL' => 'NASDAQ:AAPL',
        'TSLA' => 'NASDAQ:TSLA',
        'GOOGL' => 'NASDAQ:GOOGL',
        'AMZN' => 'NASDAQ:AMZN',
        'MSFT' => 'NASDAQ:MSFT',
        'META' => 'NASDAQ:META',
        'NVDA' => 'NASDAQ:NVDA',
        'AMD' => 'NASDAQ:AMD',
        'NFLX' => 'NASDAQ:NFLX',
        'DIS' => 'NYSE:DIS',
    ];

    private const TRADINGVIEW_CRYPTO = [
        'BTC' => 'BINANCE:BTCUSDT',
        'ETH' => 'BINANCE:ETHUSDT',
    ];

    public function market()
    {
        return $this->belongsTo(Market::class, 'market_id');
    }

    public function coin()
    {
        return $this->belongsTo(Currency::class, 'coin_id');
    }

    public function marketData()
    {
        return $this->hasOne(MarketData::class, 'pair_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'pair_id');
    }

    public function trade()
    {
        return $this->hasMany(Trade::class, 'pair_id');
    }

    public function scopeActiveMarket($query)
    {
        return $query->whereHas('market', function ($q) {
            $q->active()->whereHas('currency', function ($currency) {
                $currency->active();
            });
        });
    }

    public function scopeActiveCoin($query)
    {
        // ✅ كده مش بيحصر Crypto
        return $query->whereHas('coin', function ($q) {
            $q->active();
        });
    }

    public function isDefaultStatus(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->is_default == Status::YES) {
                $html = '<span class="badge badge--success">' . trans('Yes') . '</span>';
            } else {
                $html = '<span class="badge badge--dark">' . trans('No') . '</span>';
            }
            return $html;
        });
    }

    public function buyPlaceHolder(): Attribute
    {
        return new Attribute(function () {
            if ($this->maximum_buy_amount <= 0) {
                return trans('Minimum ') . showAmount($this->minimum_buy_amount, currencyFormat: false);
            } else {
                return showAmount($this->minimum_buy_amount, currencyFormat: false) . '-' . showAmount($this->maximum_buy_amount, currencyFormat: false);
            }
        });
    }

    public function sellPlaceHolder(): Attribute
    {
        return new Attribute(function () {
            if ($this->maximum_sell_amount <= 0) {
                return trans('Minimum ') . showAmount($this->minimum_sell_amount, currencyFormat: false);
            } else {
                return showAmount($this->minimum_sell_amount, currencyFormat: false) . '-' . showAmount($this->maximum_sell_amount, currencyFormat: false);
            }
        });
    }

    public function baseSymbol(): ?string
    {
        if ($this->relationLoaded('coin') && $this->coin) {
            return strtoupper($this->coin->symbol);
        }

        return $this->splitSymbol()[0] ?? null;
    }

    public function quoteSymbol(): ?string
    {
        if ($this->relationLoaded('market') && $this->market && $this->market->relationLoaded('currency') && $this->market->currency) {
            return strtoupper($this->market->currency->symbol);
        }

        return $this->splitSymbol()[1] ?? null;
    }

    public function resolveTradingViewSymbol(): array
    {
        $base = $this->baseSymbol();
        $quote = $this->quoteSymbol();

        if (!$base) {
            return [
                'symbol' => null,
                'error' => 'Missing base symbol',
            ];
        }

        if (isset(self::TRADINGVIEW_INDICES[$base])) {
            return [
                'symbol' => self::TRADINGVIEW_INDICES[$base],
                'error' => null,
            ];
        }

        if (isset(self::TRADINGVIEW_COMMODITIES[$base])) {
            return [
                'symbol' => self::TRADINGVIEW_COMMODITIES[$base],
                'error' => null,
            ];
        }

        if (isset(self::TRADINGVIEW_STOCKS[$base])) {
            return [
                'symbol' => self::TRADINGVIEW_STOCKS[$base],
                'error' => null,
            ];
        }

        if (isset(self::TRADINGVIEW_CRYPTO[$base])) {
            return [
                'symbol' => self::TRADINGVIEW_CRYPTO[$base],
                'error' => null,
            ];
        }

        if ($quote && in_array($base, self::TRADINGVIEW_FOREX, true)) {
            return [
                'symbol' => 'FX:' . $base . $quote,
                'error' => null,
            ];
        }

        return [
            'symbol' => null,
            'error' => 'TradingView symbol unsupported',
        ];
    }

    private function splitSymbol(): array
    {
        if (!$this->symbol) {
            return [];
        }

        $parts = explode('_', strtoupper($this->symbol));
        if (count($parts) >= 2) {
            return [$parts[0], $parts[1]];
        }

        return [$parts[0]];
    }
}
