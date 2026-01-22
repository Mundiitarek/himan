@php
 $meta   = (object) $meta;
 $pair   = $meta->pair;
 $widget = gs("trading_view_widget");

 $tradingView = $pair->resolveTradingViewSymbol();
 $symbol = $tradingView['symbol'] ?? null;

 if ($symbol) {
     $widget = str_replace('{{pair}}', $symbol, $widget);
     $widget = str_replace('{{pairlistingmarket}}', $pair->listed_market_name, $widget);
 }
@endphp
<div class="trading-chart  p-0 two">
    @if ($symbol && $widget)
        @php echo $widget; @endphp
    @else
        <div class="alert alert-warning m-3">
            @lang('TradingView chart is not available for this symbol right now.')
        </div>
    @endif
</div>

