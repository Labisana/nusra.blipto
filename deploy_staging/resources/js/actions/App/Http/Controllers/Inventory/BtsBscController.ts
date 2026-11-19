import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Inventory\BtsBscController::index
 * @see app/Http/Controllers/Inventory/BtsBscController.php:18
 * @route '/bts-bsc'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/bts-bsc',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Inventory\BtsBscController::index
 * @see app/Http/Controllers/Inventory/BtsBscController.php:18
 * @route '/bts-bsc'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\BtsBscController::index
 * @see app/Http/Controllers/Inventory/BtsBscController.php:18
 * @route '/bts-bsc'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Inventory\BtsBscController::index
 * @see app/Http/Controllers/Inventory/BtsBscController.php:18
 * @route '/bts-bsc'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Inventory\BtsBscController::index
 * @see app/Http/Controllers/Inventory/BtsBscController.php:18
 * @route '/bts-bsc'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Inventory\BtsBscController::index
 * @see app/Http/Controllers/Inventory/BtsBscController.php:18
 * @route '/bts-bsc'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Inventory\BtsBscController::index
 * @see app/Http/Controllers/Inventory/BtsBscController.php:18
 * @route '/bts-bsc'
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
* @see \App\Http\Controllers\Inventory\BtsBscController::store
 * @see app/Http/Controllers/Inventory/BtsBscController.php:31
 * @route '/bts-bsc'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/bts-bsc',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Inventory\BtsBscController::store
 * @see app/Http/Controllers/Inventory/BtsBscController.php:31
 * @route '/bts-bsc'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\BtsBscController::store
 * @see app/Http/Controllers/Inventory/BtsBscController.php:31
 * @route '/bts-bsc'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Inventory\BtsBscController::store
 * @see app/Http/Controllers/Inventory/BtsBscController.php:31
 * @route '/bts-bsc'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Inventory\BtsBscController::store
 * @see app/Http/Controllers/Inventory/BtsBscController.php:31
 * @route '/bts-bsc'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Inventory\BtsBscController::update
 * @see app/Http/Controllers/Inventory/BtsBscController.php:41
 * @route '/bts-bsc/{bts_bsc}'
 */
export const update = (args: { bts_bsc: number | { id: number } } | [bts_bsc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/bts-bsc/{bts_bsc}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Inventory\BtsBscController::update
 * @see app/Http/Controllers/Inventory/BtsBscController.php:41
 * @route '/bts-bsc/{bts_bsc}'
 */
update.url = (args: { bts_bsc: number | { id: number } } | [bts_bsc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { bts_bsc: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { bts_bsc: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    bts_bsc: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        bts_bsc: typeof args.bts_bsc === 'object'
                ? args.bts_bsc.id
                : args.bts_bsc,
                }

    return update.definition.url
            .replace('{bts_bsc}', parsedArgs.bts_bsc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\BtsBscController::update
 * @see app/Http/Controllers/Inventory/BtsBscController.php:41
 * @route '/bts-bsc/{bts_bsc}'
 */
update.put = (args: { bts_bsc: number | { id: number } } | [bts_bsc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\Inventory\BtsBscController::update
 * @see app/Http/Controllers/Inventory/BtsBscController.php:41
 * @route '/bts-bsc/{bts_bsc}'
 */
update.patch = (args: { bts_bsc: number | { id: number } } | [bts_bsc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\Inventory\BtsBscController::update
 * @see app/Http/Controllers/Inventory/BtsBscController.php:41
 * @route '/bts-bsc/{bts_bsc}'
 */
    const updateForm = (args: { bts_bsc: number | { id: number } } | [bts_bsc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Inventory\BtsBscController::update
 * @see app/Http/Controllers/Inventory/BtsBscController.php:41
 * @route '/bts-bsc/{bts_bsc}'
 */
        updateForm.put = (args: { bts_bsc: number | { id: number } } | [bts_bsc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\Inventory\BtsBscController::update
 * @see app/Http/Controllers/Inventory/BtsBscController.php:41
 * @route '/bts-bsc/{bts_bsc}'
 */
        updateForm.patch = (args: { bts_bsc: number | { id: number } } | [bts_bsc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Inventory\BtsBscController::destroy
 * @see app/Http/Controllers/Inventory/BtsBscController.php:51
 * @route '/bts-bsc/{bts_bsc}'
 */
export const destroy = (args: { bts_bsc: number | { id: number } } | [bts_bsc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/bts-bsc/{bts_bsc}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Inventory\BtsBscController::destroy
 * @see app/Http/Controllers/Inventory/BtsBscController.php:51
 * @route '/bts-bsc/{bts_bsc}'
 */
destroy.url = (args: { bts_bsc: number | { id: number } } | [bts_bsc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { bts_bsc: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { bts_bsc: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    bts_bsc: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        bts_bsc: typeof args.bts_bsc === 'object'
                ? args.bts_bsc.id
                : args.bts_bsc,
                }

    return destroy.definition.url
            .replace('{bts_bsc}', parsedArgs.bts_bsc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\BtsBscController::destroy
 * @see app/Http/Controllers/Inventory/BtsBscController.php:51
 * @route '/bts-bsc/{bts_bsc}'
 */
destroy.delete = (args: { bts_bsc: number | { id: number } } | [bts_bsc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Inventory\BtsBscController::destroy
 * @see app/Http/Controllers/Inventory/BtsBscController.php:51
 * @route '/bts-bsc/{bts_bsc}'
 */
    const destroyForm = (args: { bts_bsc: number | { id: number } } | [bts_bsc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Inventory\BtsBscController::destroy
 * @see app/Http/Controllers/Inventory/BtsBscController.php:51
 * @route '/bts-bsc/{bts_bsc}'
 */
        destroyForm.delete = (args: { bts_bsc: number | { id: number } } | [bts_bsc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const BtsBscController = { index, store, update, destroy }

export default BtsBscController