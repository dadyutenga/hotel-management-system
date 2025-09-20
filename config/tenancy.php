<?php

declare(strict_types=1);

return [
    'tenant_model' => \App\Models\Tenant::class,
    'id_generator' => \Stancl\Tenancy\UUIDGenerator::class,

    'domain_model' => \Stancl\Tenancy\Database\Models\Domain::class,

    'central_domains' => [
        '127.0.0.1',
        'localhost',
        'hotel-management-system.test', // your central domain
    ],

    'bootstrappers' => [
        \Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class,
        \Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
        \Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
        \Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
    ],

    'database' => [
        'central_connection' => env('DB_CONNECTION', 'mysql'),
        
        'template_tenant_connection' => null,

        'prefix_base' => 'tenant',
        'suffix_base' => '',

        'managers' => [
            'mysql' => \Stancl\Tenancy\Database\DatabaseManager::class,
        ],
    ],

    'cache' => [
        'tag_base' => 'tenant',
    ],

    'filesystem' => [
        'suffix_base' => 'tenant',
        'disks' => [
            'local',
            'public',
        ],
    ],

    'redis' => [
        'prefix_base' => 'tenant',
    ],

    'features' => [
        // \Stancl\Tenancy\Features\UserImpersonation::class,
        // \Stancl\Tenancy\Features\TelescopeTags::class,
        // \Stancl\Tenancy\Features\UniversalRoutes::class,
        // \Stancl\Tenancy\Features\TenantConfig::class,
        // \Stancl\Tenancy\Features\CrossDomainRedirect::class,
        // \Stancl\Tenancy\Features\ViteBundler::class,
    ],

    'storage_drivers' => [
        'db' => \Stancl\Tenancy\StorageDrivers\Database\DatabaseStorageDriver::class,
    ],

    'identification' => [
        'resolvers' => [
            \Stancl\Tenancy\Resolvers\DomainTenantResolver::class => [
                \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
                \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
            ],
        ],
    ],
];