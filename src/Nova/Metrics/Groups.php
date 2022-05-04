<?php

namespace JustBetter\NovaErrorLogger\Nova\Metrics;

use Illuminate\Http\Request;
use JustBetter\ErrorLogger\Models\Error;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class Groups extends Partition
{
    public $width = '1/4';

    public function calculate(Request $request): PartitionResult
    {
        return $this->count($request, Error::class, 'group');
    }

    public function uriKey(): string
    {
        return 'groups';
    }
}
