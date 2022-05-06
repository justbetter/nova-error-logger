<?php

namespace JustBetter\NovaErrorLogger\Nova\Metrics;

use JustBetter\ErrorLogger\Models\Error;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class Groups extends Partition
{
    public $width = '1/4';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Error::class, 'group');
    }

    public function uriKey(): string
    {
        return 'groups';
    }
}
