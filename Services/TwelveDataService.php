<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TwelveDataService
{
    private string $key;
    private string $baseUrl;
    private int $defaultTimeout = 10;

    public function __construct()
    {
        $this->key     = config('services.twelvedata.key');
        $this->baseUrl = rtrim(config('services.twelvedata.base_url'), '/');
    }

    /**
     * Get quote for single symbol
     */
    public function quote(string $symbol): array
    {
        try {
            $resp = Http::timeout($this->defaultTimeout)->get($this->baseUrl . '/quote', [
                'symbol' => $symbol,
                'apikey' => $this->key,
            ]);

            if (!$resp->ok()) {
                return [
                    'status' => 'error',
                    'message' => "HTTP Error: " . $resp->status(),
                ];
            }

            $data = $resp->json();

            if (isset($data['status']) && $data['status'] === 'error') {
                return [
                    'status' => 'error',
                    'message' => $data['message'] ?? 'TwelveData error',
                ];
            }

            return $data ?? [];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get quotes for multiple symbols (batch request)
     */
    public function quotes(array $symbols): array
    {
        // TwelveData batch quotes: symbols=EUR/USD,GBP/USD,...
        $symbolsStr = implode(',', $symbols);

        try {
            $resp = Http::timeout($this->defaultTimeout)->get($this->baseUrl . '/quote', [
                'symbol' => $symbolsStr,
                'apikey' => $this->key,
            ]);

            if (!$resp->ok()) {
                return [
                    'status' => 'error',
                    'message' => "HTTP Error: " . $resp->status(),
                ];
            }

            $data = $resp->json();

            // TwelveData بيرجعها غالبًا بالشكل:
            // { "EUR/USD": {...}, "GBP/USD": {...} }
            // أو لو في error: { "status": "error", "message": "..." }
            if (isset($data['status']) && $data['status'] === 'error') {
                return [
                    'status' => 'error',
                    'message' => $data['message'] ?? 'TwelveData error',
                ];
            }

            return $data ?? [];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function quoteForPair(string $base, ?string $quote = null, ?string $explicitSymbol = null, int $ttl = 0): array
    {
        $base = strtoupper(trim($base));
        $quote = $quote ? strtoupper(trim($quote)) : null;

        $candidates = $this->symbolCandidates($base, $quote, $explicitSymbol);
        foreach ($candidates as $candidate) {
            $data = $this->cachedQuote($candidate, $ttl);
            if ($this->isError($data)) {
                continue;
            }

            return [
                'symbol' => $candidate,
                'data' => $data,
            ];
        }

        return [
            'symbol' => $candidates[0] ?? $explicitSymbol ?? $base,
            'data' => [
                'status' => 'error',
                'message' => 'No quote found for any symbol format.',
            ],
        ];
    }

    public function extractPrice(array $data): float
    {
        if (isset($data['close'])) {
            return (float) $data['close'];
        }

        if (isset($data['price'])) {
            return (float) $data['price'];
        }

        return 0.0;
    }

    /**
     * Helper method للـ GET requests (optional - لو عايز تعمل abstraction)
     */
    private function get(string $endpoint, array $params = []): array
    {
        try {
            $params['apikey'] = $this->key;

            $resp = Http::timeout($this->defaultTimeout)->get($this->baseUrl . $endpoint, $params);

            if (!$resp->ok()) {
                return [
                    'status' => 'error',
                    'message' => "HTTP Error: " . $resp->status(),
                ];
            }

            $data = $resp->json();

            if (isset($data['status']) && $data['status'] === 'error') {
                return [
                    'status' => 'error',
                    'message' => $data['message'] ?? 'TwelveData error',
                ];
            }

            return $data ?? [];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    private function cachedQuote(string $symbol, int $ttl): array
    {
        if ($ttl <= 0) {
            return $this->quote($symbol);
        }

        $cacheKey = 'td_quote:' . md5($symbol);
        return cache()->remember($cacheKey, $ttl, function () use ($symbol) {
            return $this->quote($symbol);
        });
    }

    private function symbolCandidates(string $base, ?string $quote, ?string $explicitSymbol): array
    {
        $candidates = [];

        if ($explicitSymbol) {
            $candidates[] = strtoupper(trim($explicitSymbol));
        }

        if ($quote) {
            $candidates[] = $base . '/' . $quote;
        }

        $candidates[] = $base;

        $mapped = $this->aliasMap();
        if (isset($mapped[$base])) {
            $candidates[] = $mapped[$base];
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    private function aliasMap(): array
    {
        return [
            'SPX' => 'SPX',
            'NDX' => 'NDX',
            'DAX' => 'DAX',
            'DJI' => 'DJI',
            'RUT' => 'RUT',
            'VIX' => 'VIX',
            'FTSE' => 'FTSE',
            'CAC' => 'CAC',
            'NIKKEI' => 'N225',
            'HSI' => 'HSI',
            'SSE' => 'SSE',
        ];
    }

    private function isError(array $data): bool
    {
        return isset($data['status']) && $data['status'] === 'error';
    }
}
