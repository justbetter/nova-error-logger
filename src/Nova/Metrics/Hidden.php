<?php

namespace JustBetter\NovaErrorLogger\Nova\Metrics;

use Illuminate\Http\Request;
use JustBetter\ErrorLogger\Models\Error;
use Laravel\Nova\Metrics\Partition;

class Hidden extends Partition
{
    public $width = '1/4';

    public function calculate(Request $request)
    {
        return $this->count($request, Error::class, 'show_on_index')
            ->label(fn($value) => $value ? 'Yes' : 'No');
    }

    public function uriKey()
    {
        return 'hidden';
    }
}