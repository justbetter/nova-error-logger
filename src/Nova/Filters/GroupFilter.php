<?php

declare(strict_types=1);

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

    #[\Override]
    public function options(NovaRequest $request): Enumerable
    {
        return Error::query()
            ->select('group')
            ->distinct()
            ->get()
            ->mapWithKeys(fn (Error $error): array => [$error->group => $error->group]);
    }
}
