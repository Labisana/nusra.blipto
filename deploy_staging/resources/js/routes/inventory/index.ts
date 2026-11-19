import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import incomingB33b6a from './incoming'
import outgoingFa0346 from './outgoing'
import stock0387f8 from './stock'
import reports3613d0 from './reports'
/**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::incoming
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:24
 * @route '/barang-masuk'
 */
export const incoming = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: incoming.url(options),
    method: 'get',
})

incoming.definition = {
    methods: ["get","head"],
    url: '/barang-masuk',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::incoming
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:24
 * @route '/barang-masuk'
 */
incoming.url = (options?: RouteQueryOptions) => {
    return incoming.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::incoming
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:24
 * @route '/barang-masuk'
 */
incoming.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: incoming.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::incoming
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:24
 * @route '/barang-masuk'
 */
incoming.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: incoming.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::incoming
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:24
 * @route '/barang-masuk'
 */
    const incomingForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: incoming.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::incoming
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:24
 * @route '/barang-masuk'
 */
        incomingForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: incoming.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Inventory\IncomingTransactionController::incoming
 * @see app/Http/Controllers/Inventory/IncomingTransactionController.php:24
 * @route '/barang-masuk'
 */
        incomingForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: incoming.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    incoming.form = incomingForm
/**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::outgoing
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:24
 * @route '/barang-keluar'
 */
export const outgoing = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: outgoing.url(options),
    method: 'get',
})

outgoing.definition = {
    methods: ["get","head"],
    url: '/barang-keluar',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::outgoing
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:24
 * @route '/barang-keluar'
 */
outgoing.url = (options?: RouteQueryOptions) => {
    return outgoing.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::outgoing
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:24
 * @route '/barang-keluar'
 */
outgoing.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: outgoing.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::outgoing
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:24
 * @route '/barang-keluar'
 */
outgoing.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: outgoing.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::outgoing
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:24
 * @route '/barang-keluar'
 */
    const outgoingForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: outgoing.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::outgoing
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:24
 * @route '/barang-keluar'
 */
        outgoingForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: outgoing.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Inventory\OutgoingTransactionController::outgoing
 * @see app/Http/Controllers/Inventory/OutgoingTransactionController.php:24
 * @route '/barang-keluar'
 */
        outgoingForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: outgoing.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    outgoing.form = outgoingForm
/**
 * @see routes/web.php:41
 * @route '/gudang'
 */
export const gudang = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: gudang.url(options),
    method: 'get',
})

gudang.definition = {
    methods: ["get","head"],
    url: '/gudang',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:41
 * @route '/gudang'
 */
gudang.url = (options?: RouteQueryOptions) => {
    return gudang.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:41
 * @route '/gudang'
 */
gudang.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: gudang.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:41
 * @route '/gudang'
 */
gudang.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: gudang.url(options),
    method: 'head',
})

    /**
 * @see routes/web.php:41
 * @route '/gudang'
 */
    const gudangForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: gudang.url(options),
        method: 'get',
    })

            /**
 * @see routes/web.php:41
 * @route '/gudang'
 */
        gudangForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: gudang.url(options),
            method: 'get',
        })
            /**
 * @see routes/web.php:41
 * @route '/gudang'
 */
        gudangForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: gudang.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    gudang.form = gudangForm
/**
* @see \App\Http\Controllers\Inventory\ItemController::stock
 * @see app/Http/Controllers/Inventory/ItemController.php:20
 * @route '/stok'
 */
export const stock = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stock.url(options),
    method: 'get',
})

stock.definition = {
    methods: ["get","head"],
    url: '/stok',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Inventory\ItemController::stock
 * @see app/Http/Controllers/Inventory/ItemController.php:20
 * @route '/stok'
 */
stock.url = (options?: RouteQueryOptions) => {
    return stock.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\ItemController::stock
 * @see app/Http/Controllers/Inventory/ItemController.php:20
 * @route '/stok'
 */
stock.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stock.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Inventory\ItemController::stock
 * @see app/Http/Controllers/Inventory/ItemController.php:20
 * @route '/stok'
 */
stock.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: stock.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Inventory\ItemController::stock
 * @see app/Http/Controllers/Inventory/ItemController.php:20
 * @route '/stok'
 */
    const stockForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: stock.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Inventory\ItemController::stock
 * @see app/Http/Controllers/Inventory/ItemController.php:20
 * @route '/stok'
 */
        stockForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: stock.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Inventory\ItemController::stock
 * @see app/Http/Controllers/Inventory/ItemController.php:20
 * @route '/stok'
 */
        stockForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: stock.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    stock.form = stockForm
/**
* @see \App\Http\Controllers\Inventory\ReportController::reports
 * @see app/Http/Controllers/Inventory/ReportController.php:18
 * @route '/laporan'
 */
export const reports = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reports.url(options),
    method: 'get',
})

reports.definition = {
    methods: ["get","head"],
    url: '/laporan',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Inventory\ReportController::reports
 * @see app/Http/Controllers/Inventory/ReportController.php:18
 * @route '/laporan'
 */
reports.url = (options?: RouteQueryOptions) => {
    return reports.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inventory\ReportController::reports
 * @see app/Http/Controllers/Inventory/ReportController.php:18
 * @route '/laporan'
 */
reports.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reports.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Inventory\ReportController::reports
 * @see app/Http/Controllers/Inventory/ReportController.php:18
 * @route '/laporan'
 */
reports.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: reports.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Inventory\ReportController::reports
 * @see app/Http/Controllers/Inventory/ReportController.php:18
 * @route '/laporan'
 */
    const reportsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: reports.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Inventory\ReportController::reports
 * @see app/Http/Controllers/Inventory/ReportController.php:18
 * @route '/laporan'
 */
        reportsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: reports.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Inventory\ReportController::reports
 * @see app/Http/Controllers/Inventory/ReportController.php:18
 * @route '/laporan'
 */
        reportsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: reports.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    reports.form = reportsForm
const inventory = {
    incoming: Object.assign(incoming, incomingB33b6a),
outgoing: Object.assign(outgoing, outgoingFa0346),
gudang: Object.assign(gudang, gudang),
stock: Object.assign(stock, stock0387f8),
reports: Object.assign(reports, reports3613d0),
}

export default inventory