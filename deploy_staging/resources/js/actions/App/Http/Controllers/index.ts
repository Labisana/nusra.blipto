import DashboardController from './DashboardController'
import Inventory from './Inventory'
import Users from './Users'
import Settings from './Settings'
const Controllers = {
    DashboardController: Object.assign(DashboardController, DashboardController),
Inventory: Object.assign(Inventory, Inventory),
Users: Object.assign(Users, Users),
Settings: Object.assign(Settings, Settings),
}

export default Controllers