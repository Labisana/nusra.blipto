import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Inventory\ReportController::index
 * @see app/Http/Controllers/Inventory/ReportController.php:18
 * @route '/laporan'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/laporan',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Inventory\ReportController::index
 * @see app/Http/Controllers/Inventory/ReportController.php:18
 * @route '/laporan'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\ReportController::index
 * @see app/Http/Controllers/Inventory/ReportController.php:18
 * @route '/laporan'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Inventory\ReportController::index
 * @see app/Http/Controllers/Inventory/ReportController.php:18
 * @route '/laporan'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Inventory\ReportController::index
 * @see app/Http/Controllers/Inventory/ReportController.php:18
 * @route '/laporan'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Inventory\ReportController::index
 * @see app/Http/Controllers/Inventory/ReportController.php:18
 * @route '/laporan'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Inventory\ReportController::index
 * @see app/Http/Controllers/Inventory/ReportController.php:18
 * @route '/laporan'
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
* @see \App\Http\Controllers\Inventory\ReportController::exportMethod
 * @see app/Http/Controllers/Inventory/ReportController.php:34
 * @route '/laporan/export'
 */
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/laporan/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Inventory\ReportController::exportMethod
 * @see app/Http/Controllers/Inventory/ReportController.php:34
 * @route '/laporan/export'
 */
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\ReportController::exportMethod
 * @see app/Http/Controllers/Inventory/ReportController.php:34
 * @route '/laporan/export'
 */
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Inventory\ReportController::exportMethod
 * @see app/Http/Controllers/Inventory/ReportController.php:34
 * @route '/laporan/export'
 */
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Inventory\ReportController::exportMethod
 * @see app/Http/Controllers/Inventory/ReportController.php:34
 * @route '/laporan/export'
 */
    const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: exportMethod.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Inventory\ReportController::exportMethod
 * @see app/Http/Controllers/Inventory/ReportController.php:34
 * @route '/laporan/export'
 */
        exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportMethod.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Inventory\ReportController::exportMethod
 * @see app/Http/Controllers/Inventory/ReportController.php:34
 * @route '/laporan/export'
 */
        exportMethodForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportMethod.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    exportMethod.form = exportMethodForm
const ReportController = { index, exportMethod, export: exportMethod }

export default ReportController