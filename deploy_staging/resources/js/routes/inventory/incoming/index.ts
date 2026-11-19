import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::store
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:46
 * @route '/barang-masuk'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/barang-masuk',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::store
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:46
 * @route '/barang-masuk'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::store
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:46
 * @route '/barang-masuk'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::store
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:46
 * @route '/barang-masuk'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::store
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:46
 * @route '/barang-masuk'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::update
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:62
 * @route '/barang-masuk/{transaction}'
 */
export const update = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/barang-masuk/{transaction}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::update
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:62
 * @route '/barang-masuk/{transaction}'
 */
update.url = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transaction: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { transaction: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    transaction: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        transaction: typeof args.transaction === 'object'
                ? args.transaction.id
                : args.transaction,
                }

    return update.definition.url
            .replace('{transaction}', parsedArgs.transaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::update
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:62
 * @route '/barang-masuk/{transaction}'
 */
update.put = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::update
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:62
 * @route '/barang-masuk/{transaction}'
 */
    const updateForm = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::update
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:62
 * @route '/barang-masuk/{transaction}'
 */
        updateForm.put = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::destroy
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:77
 * @route '/barang-masuk/{transaction}'
 */
export const destroy = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/barang-masuk/{transaction}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::destroy
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:77
 * @route '/barang-masuk/{transaction}'
 */
destroy.url = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transaction: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { transaction: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    transaction: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        transaction: typeof args.transaction === 'object'
                ? args.transaction.id
                : args.transaction,
                }

    return destroy.definition.url
            .replace('{transaction}', parsedArgs.transaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::destroy
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:77
 * @route '/barang-masuk/{transaction}'
 */
destroy.delete = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::destroy
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:77
 * @route '/barang-masuk/{transaction}'
 */
    const destroyForm = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::destroy
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:77
 * @route '/barang-masuk/{transaction}'
 */
        destroyForm.delete = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const incoming = {
    store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
}

export default incoming