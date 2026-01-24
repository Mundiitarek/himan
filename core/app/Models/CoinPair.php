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
        'USD','EUR','GBP','JPY','AUD','CAD','NZD','CHF',
        'CNY','HKD','SGD','INR','MXN','ZAR','TRY',
    ];

    private const TRADINGVIEW_INDICES = [
        'SPX' => ['SP:SPX', 'CBOE:SPX', 'INDEX:SPX', 'TVC:SPX'],
        'VIX' => ['CBOE:VIX', 'TVC:VIX', 'INDEX:VIX'],
        'FTSE' => ['INDEX:FTSE', 'TVC:UKX'],
        'CAC' => ['INDEX:CAC40', 'TVC:CAC40'],
        'NIKKEI' => ['INDEX:NIKKEI', 'TVC:N225'],
        'N225' => ['INDEX:N225', 'TVC:N225'],
        'HSI' => ['INDEX:HSI', 'TVC:HSI'],
        'RUT' => ['INDEX:RUT', 'TVC:RUT', 'SP:RUT'],
    ];

    private const TRADINGVIEW_COMMODITIES = [
        'XAUUSD' => ['OANDA:XAUUSD', 'COMEX:GC1!', 'TVC:GOLD'],
        'XAGUSD' => ['OANDA:XAGUSD', 'COMEX:SI1!', 'TVC:SILVER'],
        'XCUUSD' => ['OANDA:XCUUSD', 'COMEX:HG1!', 'TVC:COPPER'],
        'NATGASUSD' => ['NYMEX:NATGAS', 'TVC:NATGAS', 'OANDA:NATGASUSD'],
        'NATGAS' => ['NYMEX:NATGAS', 'TVC:NATGAS', 'OANDA:NATGASUSD'],
    ];

    private const TRADINGVIEW_STOCKS = [
        'DIS' => ['NYSE:DIS', 'NASDAQ:DIS'],
    ];

    private const TRADINGVIEW_CRYPTO = [
        'BTC' => ['BINANCE:BTCUSDT'],
        'ETH' => ['BINANCE:ETHUSDT'],
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
        $override = trim((string) $this->tradingview_symbol);
        if ($override !== '') {
            return [
                'symbol' => $override,
                'error' => null,
                'candidates' => [$override],
            ];
        }

        $pair = $this->normalizedPairSymbols();
        $base = $pair['base'];
        $quote = $pair['quote'];
        $candidates = [];

        if (!$base) {
            return [
                'symbol' => null,
                'error' => 'Missing base symbol',
                'candidates' => [],
            ];
        }

        if ($quote && $this->isForexPair($base, $quote)) {
            foreach (['FX_IDC', 'OANDA', 'SAXO', 'ICE'] as $exchange) {
                $candidates[] = $exchange . ':' . $base . $quote;
            }
        }

        if (isset(self::TRADINGVIEW_INDICES[$base])) {
            $candidates = array_merge($candidates, self::TRADINGVIEW_INDICES[$base]);
        }

        $commodityKey = $base . ($quote ?? '');
        if (isset(self::TRADINGVIEW_COMMODITIES[$commodityKey])) {
            $candidates = array_merge($candidates, self::TRADINGVIEW_COMMODITIES[$commodityKey]);
        } elseif (isset(self::TRADINGVIEW_COMMODITIES[$base])) {
            $candidates = array_merge($candidates, self::TRADINGVIEW_COMMODITIES[$base]);
        }

        if (isset(self::TRADINGVIEW_STOCKS[$base])) {
            $candidates = array_merge($candidates, self::TRADINGVIEW_STOCKS[$base]);
        }

        if (isset(self::TRADINGVIEW_CRYPTO[$base])) {
            $candidates = array_merge($candidates, self::TRADINGVIEW_CRYPTO[$base]);
        }

        if (!$candidates) {
            return [
                'symbol' => null,
                'error' => 'TradingView symbol unsupported',
                'candidates' => [],
            ];
        }

        return [
            'symbol' => $candidates[0],
            'error' => null,
            'candidates' => $candidates,
        ];
    }

    private function normalizedPairSymbols(): array
    {
        $raw = strtoupper((string) $this->symbol);
        if ($raw !== '') {
            $normalized = str_replace(['-', '/'], '_', $raw);
            $parts = array_values(array_filter(explode('_', $normalized)));
            if (count($parts) >= 2) {
                return ['base' => $parts[0], 'quote' => $parts[1]];
            }

            if ($parts) {
                return ['base' => $parts[0], 'quote' => null];
            }
        }

        return [
            'base' => $this->baseSymbol(),
            'quote' => $this->quoteSymbol(),
        ];
    }

    private function isForexPair(string $base, string $quote): bool
    {
        return in_array($base, self::TRADINGVIEW_FOREX, true)
            && in_array($quote, self::TRADINGVIEW_FOREX, true);
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
