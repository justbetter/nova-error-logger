<?php

namespace JustBetter\NovaErrorLogger\Nova\Metrics;

use Illuminate\Http\Request;
use JustBetter\ErrorLogger\Models\Error;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class UpdatedErrorsPerHour extends Trend
{
    public $name = 'Errors per Hour';
    public $width = '1/4';

    public function calculate(Request $request): TrendResult
    {
        return $this->countByHours($request, Error::class, 'updated_at');
    }

    public function ranges(): array
    {
        return [
            7 => '7 Days',
            30 => '30 Days'
        ];
    }

    public function uriKey(): string
    {
        return 'updated-errors-per-hour';
    }
}
