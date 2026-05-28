<?php

declare(strict_types=1);

use Stancl\Tenancy\Database\Models\Domain;

return [
    'tenant_model' => \App\Models\Tenant::class,
    'id_generator' => Stancl\Tenancy\UUIDGenerator::class,

    'domain_model' => Domain::class,

    /**
     * Hosts where the CENTRAL app responds (apex + admin + dev).
     * Tenant subdomains (e.g. testschool.lvh.me) are NOT here — that's how stancl
     * knows those requests are tenant traffic.
     */
    'central_domains' => [
        // Dev
        '127.0.0.1',
        'localhost',
        'lvh.me',          // base domain — required so *.lvh.me resolves as a subdomain (D118)
        'admin.lvh.me',    // admin host (central — checked before subdomain extraction)
        // Production
        'drivecm.cm',      // base domain + apex
        'admin.drivecm.cm',
    ],

    /**
     * D114 — FilesystemTenancyBootstrapper is intentionally disabled.
     * Our upload paths are manually tenant-scoped (D54: storage/app/lessons/{tenant_id}/);
     * stancl's auto-suffix would double-scope existing data.
     */
    'bootstrappers' => [
        // Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class, // D6 single-DB
        Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
        // Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class, // D114
        Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
    ],

    'database' => [
        'central_connection' => env('DB_CONNECTION', 'central'),
        'template_tenant_connection' => null,
        'prefix' => 'tenant',
        'suffix' => '',
        'managers' => [
            'sqlite' => Stancl\Tenancy\TenantDatabaseManagers\SQLiteDatabaseManager::class,
            'mysql' => Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager::class,
            'mariadb' => Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager::class,
            'pgsql' => Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLDatabaseManager::class,
        ],
    ],

    'cache' => [
        'tag_base' => 'tenant',
    ],

    'filesystem' => [
        'suffix_base' => 'tenant',
        'disks' => ['local', 'public'],
        'root_override' => [
            'local' => '%storage_path%/app/',
            'public' => '%storage_path%/app/public/',
        ],
        'suffix_storage_path' => true,
        'asset_helper_tenancy' => true,
    ],

    'redis' => [
        'prefix_base' => 'tenant',
        'prefixed_connections' => [],
    ],

    'features' => [
        // none enabled
    ],

    'routes' => true,

    'migration_parameters' => [
        '--force' => true,
        '--path' => [database_path('migrations/tenant')],
        '--realpath' => true,
    ],

    'seeder_parameters' => [
        '--class' => 'DatabaseSeeder',
    ],
];