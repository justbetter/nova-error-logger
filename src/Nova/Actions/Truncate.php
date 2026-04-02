<?php

declare(strict_types=1);

namespace JustBetter\NovaErrorLogger\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use JustBetter\ErrorLogger\Models\Error;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Actions\DestructiveAction;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Truncate extends DestructiveAction
{
    use InteractsWithQueue;
    use Queueable;

    public $standalone = true;

    public $confirmButtonText = 'Truncate';

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        $query = Error::query();

        if ($fields->offsetExists('group')) {
            $query->where('group', $fields->group);
        }

        if ($fields->offsetExists('message_contains')) {
            $query->where('message', 'LIKE', sprintf('%%%s%%', $fields->message_contains));
        }

        if ($fields->offsetExists('older')) {
            $query->whereDate('updated_at', '<', $fields->older);
        }

        if ($fields->offsetExists('newer')) {
            $query->whereDate('updated_at', '>', $fields->newer);
        }

        $count = $query->count();

        $query->delete();

        return ActionResponse::message(sprintf('Deleted %d items', $count));
    }

    #[\Override]
    public function fields(NovaRequest $request): array
    {
        $groups = Error::query()
            ->select('group')
            ->distinct()
            ->get()
            ->mapWithKeys(fn (Error $error): array => [$error->group => $error->group]);

        return [
            Select::make('By Group', 'group')
                ->options($groups),

            Text::make('Message contains', 'message_contains'),

            DateTime::make('Older than', 'older'),

            DateTime::make('Newer than', 'newer'),
        ];
    }
}
