<?php

namespace JustBetter\NovaErrorLogger\Nova;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\{Badge, Code, Line, Number, Stack, Text, Textarea};
use Illuminate\Support\Str;
use JustBetter\ErrorLogger\Models\Error as ErrorModel;
use JustBetter\NovaErrorLogger\Nova\Actions\Truncate;
use JustBetter\NovaErrorLogger\Nova\Filters\GroupFilter;
use JustBetter\NovaErrorLogger\Nova\Filters\DateRange;
use JustBetter\NovaErrorLogger\Nova\Filters\ShowHidden;
use JustBetter\NovaErrorLogger\Nova\Metrics\Groups;
use JustBetter\NovaErrorLogger\Nova\Metrics\Hidden;
use JustBetter\NovaErrorLogger\Nova\Metrics\NewErrorsPerDay;
use JustBetter\NovaErrorLogger\Nova\Metrics\NewErrorsPerHour;
use JustBetter\NovaErrorLogger\Nova\Metrics\NewErrorsPerMinute;
use JustBetter\NovaErrorLogger\Nova\Metrics\UpdatedErrorsPerDay;
use JustBetter\NovaErrorLogger\Nova\Metrics\UpdatedErrorsPerHour;
use JustBetter\NovaErrorLogger\Nova\Metrics\UpdatedErrorsPerMinute;
use Laravel\Nova\Element;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Marshmallow\Filters\DateRangeFilter;

class Error extends Resource
{
    public static $model = ErrorModel::class;

    public static $title = 'message';

    public static $search = [
        'group',
        'message',
        'details',
        'trace',
    ];

    public function fields(Request $request): array
    {
        $fields = [
            Text::make(__('Group'), 'group'),

            Text::make(__('Message'), 'message')->onlyOnDetail(),

            Line::make(__('Message'), 'message')
                ->displayUsing(fn($msg) => Str::limit($msg, 80)),

            Text::make(__('Code'), 'code'),

            Stack::make(__('First appeared'), [
                Line::make(__('Created At'), 'created_at')
                    ->displayUsing(fn(Carbon $carbon): string => $carbon->diffForHumans()),
                Line::make(__('Created At'), 'created_at')
                    ->displayUsing(fn(Carbon $carbon): string => $carbon->format('d-m-Y H:i:s'))
                    ->asSmall()
            ]),

            Stack::make(__('Last appeared'), [
                Line::make(__('Updated At'), 'updated_at')
                    ->displayUsing(fn(Carbon $carbon): string => $carbon->diffForHumans()),
                Line::make(__('Updated At'), 'updated_at')
                    ->displayUsing(fn(Carbon $carbon): string => $carbon->format('d-m-Y H:i:s'))
                    ->asSmall()
            ]),

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

    public static function indexQuery(NovaRequest $request, $query)
    {
        $query->when(empty($request->get('orderBy')), function (Builder $q) {
            $q->getQuery()->orders = [];

            return $q->orderByDesc('updated_at');
        });

        $query->when(collect($query->getQuery()->wheres)->where('column', 'show_on_index')->isEmpty(), function (Builder $q) {
            return $q->where('show_on_index', true);
        });
    }

    public static function authorizedToCreate(Request $request): bool
    {
        return false;
    }

    public function authorizedToUpdate(Request $request): bool
    {
        return false;
    }

    public function actions(Request $request): array
    {
        return [
            new Truncate
        ];
    }

    public function cards(Request $request): array
    {
        return [
            new UpdatedErrorsPerDay,
            new UpdatedErrorsPerHour,
            new Groups,
            new Hidden
        ];
    }

    public function filters(Request $request): array
    {
        return [
            new GroupFilter,
            new ShowHidden,
            (new DateRangeFilter('updated_at', 'Updated date'))->enableTime(),
            (new DateRangeFilter('created_at', 'Created date'))->enableTime(),
        ];
    }
}
