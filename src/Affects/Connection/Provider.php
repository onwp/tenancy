<?php

declare(strict_types=1);

/*
 * This file is part of the tenancy/tenancy package.
 *
 * Copyright Laravel Tenancy & Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://tenancy.dev
 * @see https://github.com/tenancy
 */

namespace Tenancy\Affects\Connection;

use Illuminate\Database\DatabaseManager;
use Tenancy\Affects\Connection\Contracts\ResolvesConnections;
use Tenancy\Environment;
use Tenancy\Providers\Provides\ProvidesBindings;
use Tenancy\Providers\Provides\ProvidesConfigs;
use Tenancy\Providers\Provides\ProvidesListeners;
use Tenancy\Support\AffectsProvider;

class Provider extends AffectsProvider
{
    use ProvidesBindings,
        ProvidesListeners,
        ProvidesConfigs;

    protected $configs = [
        __DIR__.'/resources/config/connection.php'
    ];

    protected $affects = [ConfiguresConnection::class];

    public $singletons = [
        ResolvesConnections::class => ConnectionResolver::class
    ];

    public $listen = [
        Events\Resolved::class => [
            Listeners\SetConnection::class
        ],
    ];

    public function boot(){
        parent::boot();
        Environment::macro('getTenantConnectionName', function(){
            return config('tenancy.connection.tenant-connection-name') ?? 'tenant';
        });
        Environment::macro('getTenantConnection', function(){
            return resolve(DatabaseManager::class)->connection(static::getTenantConnectionName());
        });
    }
}
