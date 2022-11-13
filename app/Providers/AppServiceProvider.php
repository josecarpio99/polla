<?php

namespace App\Providers;

use App\Macros\CreateUpdateOrDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Model::unGuard();

        HasMany::macro('createUpdateOrDelete', function (iterable $records) {
            /** @var HasMany */
            $hasMany = $this;

            return (new CreateUpdateOrDelete($hasMany, $records))();
        });
    }
}
