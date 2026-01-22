<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoinPair;
use App\Models\MarketData;
use App\Services\TwelveDataService;
use Illuminate\Http\Request;

class TwelveDataSyncController extends Controller
{
    public function sync(Request $request, TwelveDataService $td)
    {
        // عدد الأزواج اللي هنعملها Sync
        $limit = (int)($request->limit ?? 20);
        if ($limit <= 0 || $limit > 200) {
            $limit = 20;
        }

        // ✅ TTL للكاش بالثواني لتقليل credits (افتراضي 15 ثانية)
        // تقدر تغيره من الريكوست: /api/td/sync?limit=20&ttl=30
        $ttl = (int)($request->ttl ?? 15);
        if ($ttl < 5) $ttl = 5;
        if ($ttl > 300) $ttl = 300;

        try {
            $pairs = CoinPair::active()
                ->with('coin:id,symbol', 'market:id,currency_id', 'market.currency:id,symbol')
                ->orderBy('id', 'desc')
                ->take($limit)
                ->get();

            if ($pairs->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'updated' => 0,
                    'data' => [],
                    'errors' => [],
                    'note' => 'No active pairs found',
                ]);
            }

            $updated = 0;
            $data = [];
            $errors = [];

            foreach ($pairs as $pair) {
                $base  = $pair->coin->symbol ?? null;
                $quote = optional(optional($pair->market)->currency)->symbol ?? null;

                if (!$base || !$quote) {
                    $errors[] = [
                        'pair_id' => $pair->id,
                        'pair_symbol' => $pair->symbol,
                        'error' => 'Missing base/quote relation',
                    ];
                    continue;
                }

                // ✅ لو عندك لاحقاً عمود td_symbol في DB هيتاخد تلقائي
                $tdSymbol = $pair->td_symbol ?? null;

                try {
                    $quoteResult = $td->quoteForPair($base, $quote, $tdSymbol, $ttl);
                    $q = $quoteResult['data'] ?? [];
                    $resolvedSymbol = $quoteResult['symbol'] ?? $tdSymbol ?? $base;

                    // السعر: TwelveData ساعات يرجع close أو price
                    $price = $td->extractPrice(is_array($q) ? $q : []);

                    if ($price <= 0) {
                        $errors[] = [
                            'pair_id' => $pair->id,
                            'pair_symbol' => $pair->symbol,
                            'td_symbol' => $resolvedSymbol,
                            'error' => 'Price not found or invalid',
                            'raw' => $q,
                        ];
                        continue;
                    }

                    $currencyId = (int)(optional($pair->market)->currency_id ?? 0);

                    MarketData::updateOrCreate(
                        [
                            'pair_id' => $pair->id,
                            'currency_id' => $currencyId,
                        ],
                        [
                            'price' => $price,
                            'symbol' => $pair->symbol, // نخزن symbol الحقيقي بتاع المنصة
                        ]
                    );

                    $updated++;
                    $data[] = [
                        'pair_id' => $pair->id,
                        'pair_symbol' => $pair->symbol,
                        'td_symbol' => $resolvedSymbol,
                        'price' => $price,
                        'cached_ttl' => $ttl,
                    ];

                } catch (\Throwable $e) {
                    $errors[] = [
                        'pair_id' => $pair->id,
                        'pair_symbol' => $pair->symbol,
                        'td_symbol' => $tdSymbol ?? $base,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'updated' => $updated,
                'data' => $data,
                'errors' => $errors,
                'meta' => [
                    'limit' => $limit,
                    'ttl' => $ttl,
                ],
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
