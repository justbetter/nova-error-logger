<?php

namespace JustBetter\NovaErrorLogger\Nova;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Illuminate\Support\Str;
use JustBetter\NovaErrorLogger\Nova\Actions\Truncate;
use JustBetter\NovaErrorLogger\Nova\Filters\GroupFilter;
use JustBetter\NovaErrorLogger\Nova\Filters\ShowHidden;
use JustBetter\NovaErrorLogger\Nova\Metrics\Groups;
use JustBetter\NovaErrorLogger\Nova\Metrics\Hidden;
use JustBetter\NovaErrorLogger\Nova\Metrics\UpdatedErrorsPerDay;
use JustBetter\NovaErrorLogger\Nova\Metrics\UpdatedErrorsPerHour;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

class Error extends Resource
{
    public static $model = \JustBetter\ErrorLogger\Models\Error::class;

    public static $title = 'message';

    public static $search = [
        'group',
        'message',
        'details',
        'trace',
    ];

    public function fields(NovaRequest $request): array
    {
        $fields = [
            Text::make(__('Group'), 'group'),

            Text::make(__('Message'), 'message')
                ->onlyOnDetail(),

            Line::make(__('Message'), 'message')
                ->displayUsing(fn($msg) => Str::limit($msg, 80)),

            Text::make(__('Code'), 'code'),

            DateTime::make(__('First appeared'), 'created_at')
                ->filterable(),

            DateTime::make(__('Last appeared'), 'updated_at')
                ->filterable(),

            Number::make(__('Count'), 'count')
                ->sortable(),

            Textarea::make(__('Details'), 'details'),
        ];

        if (optional($this->model())->hasTrace()) {
            $fields[] = Code::make(__('Trace'), 'trace')
                ->language('application/json');
        }

        return $fields;
    }

    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        if ($request->viaResource() !== null) {
            return $query;
        }

        $query->when(empty($request->get('orderBy')), function (Builder $q) {
            $q->getQuery()->orders = [];

            return $q->orderByDesc('updated_at');
        });

        $query->when(collect($query->getQuery()->wheres)->where('column', 'show_on_index')->isEmpty(), function (Builder $q) {
            return $q->where('show_on_index', true);
        });

        return $query;
    }

    public static function authorizedToCreate(Request $request): bool
    {
        return false;
    }

    public function authorizedToUpdate(Request $request): bool
    {
        return false;
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new Truncate
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [
            new UpdatedErrorsPerDay,
            new UpdatedErrorsPerHour,
            new Groups,
            new Hidden
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new GroupFilter,
            new ShowHidden,
        ];
    }
}
