<?php

namespace JustBetter\NovaErrorLogger;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JustBetter\NovaErrorLogger\Nova\Error;
use Laravel\Nova\Nova;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->bootNova();
    }

    protected function bootNova(): self
    {
        Nova::resources([
            Error::class,
        ]);

        return $this;
    }
}
