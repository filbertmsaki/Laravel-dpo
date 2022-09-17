<?php

namespace Femlabs\Dpo;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class DpoServiceProvider extends ServiceProvider
{
    public function boot()
    {

        AboutCommand::add('Femlabs Dpo Group Online Payment Package', fn () => ['Version' => '1.0.0']);

        //publish config
        $this->publishes([
            __DIR__.'/config/laravel-dpo.php' => config_path('laravel-dpo.php'),
        ]);
        $this->publishes([
            __DIR__.'/database/migrations/2022_09_16_025954_create_dpos_table.php' => database_path(now()->format('Y_m_d_His').'_create_dpos_table.php')
        ]);

    }
    public function register()
    {
        //
    }
}
