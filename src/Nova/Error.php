<?php

namespace JustBetter\NovaErrorLogger\Nova;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\{Code, Line, Stack, Text, Textarea};
use JustBetter\ErrorLogger\Models\Error as ErrorModel;
use Laravel\Nova\Resource;

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

            Text::make(__('Message'), 'message'),

            Text::make(__('Code'), 'code'),

            Stack::make(__('Created At'), [
                Line::make(__('Created At'), 'created_at')
                    ->displayUsing(fn(Carbon $carbon): string => $carbon->diffForHumans()),
                Line::make(__('Created At'), 'created_at')
                    ->displayUsing(fn(Carbon $carbon): string => $carbon->format('d-m-Y H:i:s'))
                    ->asSmall(),
            ]),

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
    }

    public static function authorizedToCreate(Request $request): bool
    {
        return false;
    }

    public function authorizedToUpdate(Request $request): bool
    {
        return false;
    }
}
