import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::index
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:24
 * @route '/barang-keluar'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/barang-keluar',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::index
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:24
 * @route '/barang-keluar'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::index
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:24
 * @route '/barang-keluar'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::index
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:24
 * @route '/barang-keluar'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::index
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:24
 * @route '/barang-keluar'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::index
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:24
 * @route '/barang-keluar'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::index
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:24
 * @route '/barang-keluar'
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
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::store
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:50
 * @route '/barang-keluar'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/barang-keluar',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::store
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:50
 * @route '/barang-keluar'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::store
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:50
 * @route '/barang-keluar'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::store
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:50
 * @route '/barang-keluar'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::store
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:50
 * @route '/barang-keluar'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::update
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:66
 * @route '/barang-keluar/{transaction}'
 */
export const update = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/barang-keluar/{transaction}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::update
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:66
 * @route '/barang-keluar/{transaction}'
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
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::update
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:66
 * @route '/barang-keluar/{transaction}'
 */
update.put = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::update
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:66
 * @route '/barang-keluar/{transaction}'
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
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::update
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:66
 * @route '/barang-keluar/{transaction}'
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
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::destroy
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:81
 * @route '/barang-keluar/{transaction}'
 */
export const destroy = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/barang-keluar/{transaction}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::destroy
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:81
 * @route '/barang-keluar/{transaction}'
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
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::destroy
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:81
 * @route '/barang-keluar/{transaction}'
 */
destroy.delete = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::destroy
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:81
 * @route '/barang-keluar/{transaction}'
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
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::destroy
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:81
 * @route '/barang-keluar/{transaction}'
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
const OutgoingTransactionController = { index, store, update, destroy }

export default OutgoingTransactionController