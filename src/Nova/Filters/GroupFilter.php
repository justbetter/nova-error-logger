<?php

namespace JustBetter\NovaErrorLogger\Nova\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Enumerable;
use JustBetter\ErrorLogger\Models\Error;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class GroupFilter extends Filter
{
    public $name = 'Group';

    /** @param Builder $query */
    public function apply(NovaRequest $request, $query, $value): Builder
    {
        return $query->where('group', $value);
    }

    public function options(NovaRequest $request): Enumerable
    {
        return Error::query()
            ->select('group')
            ->distinct()
            ->get()
            ->mapWithKeys(fn ($e) => [$e->group => $e->group]);
    }
}
