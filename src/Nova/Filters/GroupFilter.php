<?php

namespace JustBetter\NovaErrorLogger\Nova\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Enumerable;
use JustBetter\ErrorLogger\Models\Error;
use Laravel\Nova\Filters\Filter;

class GroupFilter extends Filter
{
    public $name = 'Group';

    /** @param Builder $query */
    public function apply(Request $request, $query, $value): Builder
    {
        return $query->where('group', $value);
    }

    public function options(Request $request): Enumerable
    {
        return Error::query()
            ->select('group')
            ->distinct()
            ->get()
            ->mapWithKeys(fn($e) => [$e->group => $e->group]);
    }
}
