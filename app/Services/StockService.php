<?php

namespace App\Services;

use App\Models\StockPrice;
use Carbon\Carbon;

class StockService
{
    public function compareBetweenDates(int $companyId, string $start, string $end): array
    {
        $startDate = Carbon::parse($start)->toDateString();
        $endDate   = Carbon::parse($end)->toDateString();

        $startPrice = $this->getClosestPriceOnOrBefore($companyId, $startDate);
        $endPrice   = $this->getClosestPriceOnOrBefore($companyId, $endDate);

        $pct = null;
        if ($startPrice !== null && $endPrice !== null && $startPrice > 0) {
            $pct = (($endPrice - $startPrice) / $startPrice) * 100;
        }

        return [
            'start_date'        => $startDate,
            'end_date'          => $endDate,
            'start_price'       => $startPrice,
            'end_price'         => $endPrice,
            'percentage_change' => is_null($pct) ? null : round($pct, 4),
        ];
    }

    public function getByPeriod(int $companyId, string $period): array
    {
        $today = Carbon::today();
        $start = null;

        switch ($period) {
            case '1D':  $start = $today->copy()->subDays(1); break;
            case '1M':  $start = $today->copy()->subMonths(1); break;
            case '3M':  $start = $today->copy()->subMonths(3); break;
            case '6M':  $start = $today->copy()->subMonths(6); break;
            case 'YTD': $start = Carbon::create($today->year, 1, 1); break;
            case '1Y':  $start = $today->copy()->subYear(1); break;
            case '3Y':  $start = $today->copy()->subYears(3); break;
            case '5Y':  $start = $today->copy()->subYears(5); break;
            case '10Y': $start = $today->copy()->subYears(10); break;
            case 'MAX':
                $first = StockPrice::where('company_id', $companyId)->orderBy('date','asc')->first();
                $start = $first ? Carbon::parse($first->date) : null;
                break;
            default:
                throw new \InvalidArgumentException('Unsupported period: ' . $period);
        }

        if (!$start) {
            return [
                'period'            => $period,
                'start_price'       => null,
                'end_price'         => null,
                'percentage_change' => null,
            ];
        }

        $startDate = $start->toDateString();
        $endDate   = $today->toDateString();

        $startPrice = $this->getClosestPriceOnOrBefore($companyId, $startDate);
        $endPrice   = $this->getClosestPriceOnOrBefore($companyId, $endDate);

        $pct = null;
        if ($startPrice !== null && $endPrice !== null && $startPrice > 0) {
            $pct = (($endPrice - $startPrice) / $startPrice) * 100;
        }

        return [
            'period'            => $period,
            'start_date'        => $startDate,
            'end_date'          => $endDate,
            'start_price'       => $startPrice,
            'end_price'         => $endPrice,
            'percentage_change' => is_null($pct) ? null : round($pct, 4),
        ];
    }

    protected function getClosestPriceOnOrBefore(int $companyId, string $date): ?float
    {
        $record = StockPrice::where('company_id', $companyId)
            ->where('date', '<=', $date)
            ->orderBy('date', 'desc')
            ->first();

        return $record ? floatval($record->price) : null;
    }
}
