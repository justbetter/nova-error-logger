<?php

declare(strict_types=1);

namespace JustBetter\NovaErrorLogger\Nova\Metrics;

use JustBetter\ErrorLogger\Models\Error;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class UpdatedErrorsPerHour extends Trend
{
    public $name = 'Errors per Hour';

    public $width = '1/4';

    public function calculate(NovaRequest $request): TrendResult
    {
        return $this->countByHours($request, Error::class, 'updated_at');
    }

    #[\Override]
    public function ranges(): array
    {
        return [
            7 => '7 Days',
            30 => '30 Days',
        ];
    }

    #[\Override]
    public function uriKey(): string
    {
        return 'updated-errors-per-hour';
    }
}
