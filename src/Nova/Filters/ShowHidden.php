<?php

namespace JustBetter\NovaErrorLogger\Nova\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use JustBetter\ErrorLogger\Models\Error;
use Laravel\Nova\Filters\Filter;

class ShowHidden extends Filter
{
    public $name = 'Hidden';

    /** @param Builder $query */
    public function apply(Request $request, $query, $value)
    {
        ray($value);
        return $query
            ->where('show_on_index', $value);
    }

    public function options(Request $request)
    {
        return [
            'Show' => false
        ];
    }
}