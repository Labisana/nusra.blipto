import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
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
const reports = {
    export: Object.assign(exportMethod, exportMethod),
}

export default reports