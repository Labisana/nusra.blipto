import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Inventory\CustomerController::index
 * @see app/Http/Controllers/Inventory/CustomerController.php:21
 * @route '/pelanggan'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/pelanggan',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Inventory\CustomerController::index
 * @see app/Http/Controllers/Inventory/CustomerController.php:21
 * @route '/pelanggan'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\CustomerController::index
 * @see app/Http/Controllers/Inventory/CustomerController.php:21
 * @route '/pelanggan'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Inventory\CustomerController::index
 * @see app/Http/Controllers/Inventory/CustomerController.php:21
 * @route '/pelanggan'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Inventory\CustomerController::index
 * @see app/Http/Controllers/Inventory/CustomerController.php:21
 * @route '/pelanggan'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Inventory\CustomerController::index
 * @see app/Http/Controllers/Inventory/CustomerController.php:21
 * @route '/pelanggan'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Inventory\CustomerController::index
 * @see app/Http/Controllers/Inventory/CustomerController.php:21
 * @route '/pelanggan'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\Inventory\CustomerController::store
 * @see app/Http/Controllers/Inventory/CustomerController.php:44
 * @route '/pelanggan'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/pelanggan',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Inventory\CustomerController::store
 * @see app/Http/Controllers/Inventory/CustomerController.php:44
 * @route '/pelanggan'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\CustomerController::store
 * @see app/Http/Controllers/Inventory/CustomerController.php:44
 * @route '/pelanggan'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Inventory\CustomerController::store
 * @see app/Http/Controllers/Inventory/CustomerController.php:44
 * @route '/pelanggan'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Inventory\CustomerController::store
 * @see app/Http/Controllers/Inventory/CustomerController.php:44
 * @route '/pelanggan'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Inventory\CustomerController::update
 * @see app/Http/Controllers/Inventory/CustomerController.php:54
 * @route '/pelanggan/{customer}'
 */
export const update = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/pelanggan/{customer}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Inventory\CustomerController::update
 * @see app/Http/Controllers/Inventory/CustomerController.php:54
 * @route '/pelanggan/{customer}'
 */
update.url = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { customer: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { customer: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    customer: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        customer: typeof args.customer === 'object'
                ? args.customer.id
                : args.customer,
                }

    return update.definition.url
            .replace('{customer}', parsedArgs.customer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\CustomerController::update
 * @see app/Http/Controllers/Inventory/CustomerController.php:54
 * @route '/pelanggan/{customer}'
 */
update.put = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\Inventory\CustomerController::update
 * @see app/Http/Controllers/Inventory/CustomerController.php:54
 * @route '/pelanggan/{customer}'
 */
update.patch = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\Inventory\CustomerController::update
 * @see app/Http/Controllers/Inventory/CustomerController.php:54
 * @route '/pelanggan/{customer}'
 */
    const updateForm = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Inventory\CustomerController::update
 * @see app/Http/Controllers/Inventory/CustomerController.php:54
 * @route '/pelanggan/{customer}'
 */
        updateForm.put = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\Inventory\CustomerController::update
 * @see app/Http/Controllers/Inventory/CustomerController.php:54
 * @route '/pelanggan/{customer}'
 */
        updateForm.patch = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\Inventory\CustomerController::destroy
 * @see app/Http/Controllers/Inventory/CustomerController.php:64
 * @route '/pelanggan/{customer}'
 */
export const destroy = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/pelanggan/{customer}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Inventory\CustomerController::destroy
 * @see app/Http/Controllers/Inventory/CustomerController.php:64
 * @route '/pelanggan/{customer}'
 */
destroy.url = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { customer: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { customer: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    customer: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        customer: typeof args.customer === 'object'
                ? args.customer.id
                : args.customer,
                }

    return destroy.definition.url
            .replace('{customer}', parsedArgs.customer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\CustomerController::destroy
 * @see app/Http/Controllers/Inventory/CustomerController.php:64
 * @route '/pelanggan/{customer}'
 */
destroy.delete = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Inventory\CustomerController::destroy
 * @see app/Http/Controllers/Inventory/CustomerController.php:64
 * @route '/pelanggan/{customer}'
 */
    const destroyForm = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Inventory\CustomerController::destroy
 * @see app/Http/Controllers/Inventory/CustomerController.php:64
 * @route '/pelanggan/{customer}'
 */
        destroyForm.delete = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const CustomerController = { index, store, update, destroy }

export default CustomerController