<?php

namespace JustBetter\NovaErrorLogger\Nova\Filters;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class ShowHidden extends Filter
{
    public $name = 'Hidden';

    /** @param Builder $query */
    public function apply(NovaRequest $request, $query, $value): Builder
    {
        return $query->where('show_on_index', $value);
    }

    public function options(NovaRequest $request): array
    {
        return [
            'Show' => false
        ];
    }
}
