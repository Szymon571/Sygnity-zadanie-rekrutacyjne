<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NbpService
{
    public function getAvailableCurrencies(): Collection
    {
        $currencies = Cache::remember('nbp-available-currencies', now()->addHours(12), function (): array {
            $tableA = Http::acceptJson()
                ->get('https://api.nbp.pl/api/exchangerates/tables/a/')
                ->throw()
                ->json();

            $tableB = Http::acceptJson()
                ->get('https://api.nbp.pl/api/exchangerates/tables/b/')
                ->throw()
                ->json();

            return collect([
                ...collect($tableA[0]['rates'])->map(fn (array $rate) => [
                    'code' => $rate['code'],
                    'currency' => $rate['currency'],
                    'table' => 'A',
                    'rate' => $rate['mid'],
                ]),
                ...collect($tableB[0]['rates'])->map(fn (array $rate) => [
                    'code' => $rate['code'],
                    'currency' => $rate['currency'],
                    'table' => 'B',
                    'rate' => $rate['mid'],
                ]),
            ])
                ->unique('code')
                ->sortBy('code')
                ->values()
                ->all();
        });

        return collect($currencies);
    }

    public function getLastGoldPrices(int $count = 10): Collection
    {
        $count = min($count, 50);

        $goldPrices = Cache::remember("nbp.gold.last.{$count}", now()->addHours(1), function () use ($count): array {
            return Http::acceptJson()
                ->get("https://api.nbp.pl/api/cenyzlota/last/{$count}/")
                ->throw()
                ->json();
        });

        return collect($goldPrices);
    }

    public function getHistoricalRate(string $table, string $code, string $date): ?array
    {
        $normalizedTable = strtolower($table);
        $normalizedCode = strtoupper($code);
        $cacheKey = "nbp.rate.{$normalizedTable}.{$normalizedCode}.{$date}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($normalizedTable, $normalizedCode, $date): ?array {
            $lookupDate = CarbonImmutable::parse($date);

            for ($attempt = 0; $attempt < 7; $attempt++) {
                $response = Http::acceptJson()
                    ->get("https://api.nbp.pl/api/exchangerates/rates/{$normalizedTable}/".strtolower($normalizedCode)."/{$lookupDate->toDateString()}/");

                if ($response->notFound()) {
                    $lookupDate = $lookupDate->subDay();

                    continue;
                }

                $data = $response->throw()->json();
                $rate = $data['rates'][0] ?? null;

                if ($rate === null || ! isset($rate['mid'])) {
                    return null;
                }

                return [
                    'table' => strtoupper($normalizedTable),
                    'code' => $data['code'],
                    'currency' => $data['currency'],
                    'date' => $rate['effectiveDate'],
                    'rate' => $rate['mid'],
                ];
            }

            return null;
        });
    }
}
