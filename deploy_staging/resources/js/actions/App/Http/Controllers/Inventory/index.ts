import IncomingTransactionController from './IncomingTransactionController'
import OutgoingTransactionController from './OutgoingTransactionController'
import ItemController from './ItemController'
import ReportController from './ReportController'
import CustomerController from './CustomerController'
import BtsBscController from './BtsBscController'
const Inventory = {
    IncomingTransactionController: Object.assign(IncomingTransactionController, IncomingTransactionController),
OutgoingTransactionController: Object.assign(OutgoingTransactionController, OutgoingTransactionController),
ItemController: Object.assign(ItemController, ItemController),
ReportController: Object.assign(ReportController, ReportController),
CustomerController: Object.assign(CustomerController, CustomerController),
BtsBscController: Object.assign(BtsBscController, BtsBscController),
}

export default Inventory