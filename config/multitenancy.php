<?php

return [
    /*
     * This key will be used to bind the current tenant in the container.
     */
    'current_tenant_container_key' => 'currentTenant',

    /*
     * You can customize some of the behavior of this package by using your own custom tenant model.
     * Your custom tenant model should extend `Spatie\Multitenancy\Models\Tenant::class`.
     */
    'tenant_model' => \App\Models\Tenant::class,

    /*
     * When switching tenants, the package will call the `makeCurrent` method on the specified
     * tenant finder. You can change this class to your own implementation.
     */
    'tenant_finder' => \Spatie\Multitenancy\TenantFinder\DomainTenantFinder::class,

    /*
     * This class is responsible for determining which tenant should be the current tenant.
     * By default, it will use the `DomainTenantFinder` which will use the domain
     * of the request to determine the current tenant.
     */
    'switch_tenant_tasks' => [
        // \Spatie\Multitenancy\Tasks\SwitchTenantDatabase::class,
        \Spatie\Multitenancy\Tasks\PrefixCacheTask::class,
    ],

    /*
     * This class is responsible for handling tenant-aware queues.
     * Jobs will automatically receive the correct tenant context.
     */
    'queues_are_tenant_aware_by_default' => true,

    /*
     * The connection name to reach the tenant database.
     * When using `SwitchTenantDatabase` task, the
     * `default` connection will be overridden.
     */
    'tenant_database_connection_name' => null,

    /*
     * When set to true, the package will automatically set the tenant context
     * for new Eloquent model instances. This means that when you create a new
     * model instance, it will automatically be associated with the current tenant.
     */
    'automatically_set_tenant_on_new_models' => true,

    /*
     * When set to true, global scopes will be registered to automatically
     * scope all queries to the current tenant. This is useful when you
     * want to ensure that all data is automatically scoped to the current tenant.
     */
    'automatically_scope_models_to_tenant' => true,
];