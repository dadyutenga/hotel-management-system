<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * ReportingHelpers Trait
 * 
 * Provides common reporting functionality for hotel management models.
 * Includes methods for calculating statistics, revenue, and performance metrics.
 */
trait ReportingHelpers
{
    /**
     * Get daily statistics for a date range
     */
    public function getDailyStats($startDate, $endDate, $groupBy = 'created_at')
    {
        return $this->selectRaw("DATE({$groupBy}) as date, COUNT(*) as count")
                    ->whereBetween($groupBy, [$startDate, $endDate])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
    }

    /**
     * Get monthly statistics for a date range
     */
    public function getMonthlyStats($startDate, $endDate, $groupBy = 'created_at')
    {
        return $this->selectRaw("DATE_FORMAT({$groupBy}, '%Y-%m') as month, COUNT(*) as count")
                    ->whereBetween($groupBy, [$startDate, $endDate])
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
    }

    /**
     * Get revenue statistics (for models with amount fields)
     */
    public function getRevenueStats($startDate, $endDate, $amountField = 'total_amount', $groupBy = 'created_at')
    {
        return $this->selectRaw("DATE({$groupBy}) as date, SUM({$amountField}) as revenue, COUNT(*) as transactions")
                    ->whereBetween($groupBy, [$startDate, $endDate])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
    }

    /**
     * Get top performers by count
     */
    public function getTopPerformers($limit = 10, $groupBy = 'id', $orderBy = 'count')
    {
        return $this->selectRaw("{$groupBy}, COUNT(*) as count")
                    ->groupBy($groupBy)
                    ->orderBy($orderBy, 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get performance comparison between two periods
     */
    public function getPerformanceComparison($period1Start, $period1End, $period2Start, $period2End, $metric = 'count')
    {
        $period1 = $this->whereBetween('created_at', [$period1Start, $period1End]);
        $period2 = $this->whereBetween('created_at', [$period2Start, $period2End]);

        $period1Value = $metric === 'count' ? $period1->count() : $period1->sum($metric);
        $period2Value = $metric === 'count' ? $period2->count() : $period2->sum($metric);

        $change = $period2Value - $period1Value;
        $percentChange = $period1Value > 0 ? ($change / $period1Value) * 100 : 0;

        return [
            'period1' => $period1Value,
            'period2' => $period2Value,
            'change' => $change,
            'percent_change' => round($percentChange, 2),
            'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable')
        ];
    }

    /**
     * Get growth rate for a specific period
     */
    public function getGrowthRate($startDate, $endDate, $metric = 'count', $interval = 'month')
    {
        $format = $interval === 'month' ? '%Y-%m' : '%Y-%m-%d';
        $groupSelect = "DATE_FORMAT(created_at, '{$format}') as period";
        
        $data = $this->selectRaw("{$groupSelect}, " . ($metric === 'count' ? 'COUNT(*) as value' : "SUM({$metric}) as value"))
                     ->whereBetween('created_at', [$startDate, $endDate])
                     ->groupBy('period')
                     ->orderBy('period')
                     ->get();

        $growthRates = [];
        $previousValue = null;

        foreach ($data as $item) {
            if ($previousValue !== null && $previousValue > 0) {
                $growthRate = (($item->value - $previousValue) / $previousValue) * 100;
                $growthRates[] = [
                    'period' => $item->period,
                    'value' => $item->value,
                    'growth_rate' => round($growthRate, 2)
                ];
            } else {
                $growthRates[] = [
                    'period' => $item->period,
                    'value' => $item->value,
                    'growth_rate' => 0
                ];
            }
            $previousValue = $item->value;
        }

        return $growthRates;
    }

    /**
     * Get average values for a metric
     */
    public function getAverageMetrics($startDate, $endDate, $metrics = ['total_amount'])
    {
        $query = $this->whereBetween('created_at', [$startDate, $endDate]);
        
        $results = [];
        foreach ($metrics as $metric) {
            $results[$metric] = $query->avg($metric);
        }
        
        return $results;
    }

    /**
     * Get distribution statistics
     */
    public function getDistribution($field, $startDate = null, $endDate = null)
    {
        $query = $this->selectRaw("{$field}, COUNT(*) as count")
                      ->groupBy($field)
                      ->orderBy('count', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->get();
    }

    /**
     * Get summary statistics
     */
    public function getSummaryStats($startDate, $endDate, $amountField = 'total_amount')
    {
        $query = $this->whereBetween('created_at', [$startDate, $endDate]);
        
        return [
            'count' => $query->count(),
            'total' => $query->sum($amountField),
            'average' => $query->avg($amountField),
            'min' => $query->min($amountField),
            'max' => $query->max($amountField),
        ];
    }

    /**
     * Get time-based analytics
     */
    public function getTimeAnalytics($startDate, $endDate)
    {
        return [
            'hourly' => $this->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->groupBy('hour')
                            ->orderBy('hour')
                            ->get(),
            'daily' => $this->selectRaw('DAYNAME(created_at) as day, COUNT(*) as count')
                           ->whereBetween('created_at', [$startDate, $endDate])
                           ->groupBy('day')
                           ->get(),
            'monthly' => $this->selectRaw('MONTHNAME(created_at) as month, COUNT(*) as count')
                             ->whereBetween('created_at', [$startDate, $endDate])
                             ->groupBy('month')
                             ->get(),
        ];
    }
}