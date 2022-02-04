<?php

use app\core\Database;
use app\core\Session;

$role = Session::getUserRole();
$db = Database::openConnection();
$query = "SELECT permission FROM permissions WHERE role_id=:role_id";
$db->prepare($query);
$db->bindValue(':role_id', $role);
$db->execute();
$data = $db->fetchAllAssociative();

global $permission;
$permission = [];
foreach($data as $k => $v){
    $permission[] = $v['permission'];
}

function check($str){
    return in_array($str, $GLOBALS['permission']);
}
?>

<nav id="toggle" class="navbar collapse">
    <div class="nav-header">
        <a href="#" id="nav-logo"><img src="<?php echo PUBLIC_ROOT.'image/logo.png'?>"></a>
    </div>

    <div class="main-menu">
        <ul class="menu">
            <?php if(check('dashboard_view')):?>
            <li class="menu-item">
                <a href="<?php echo PUBLIC_ROOT?>"><i class="fas fa-home"></i><span>Dashboard</span></a>
            </li>
            <?php endif;?>
            <?php if(check('items_view') || check('items_add')):?>
            <li class="menu-item">
                <a href="#"><i class="fas fa-th-large"></i><span>Products</span></a>
                <ul class="sub-menu" >
                    <?php if(check('items_add')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'item/add';?>"><i class="fas fa-plus-square"></i>Add Product</a></li>
                    <?php } if(check('items_view')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'item';?>"><i class="fas fa-list-ul"></i>Products List</a></li>
                    <?php } if(check('items_category_add')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'category/add';?>"><i class="fas fa-plus-square"></i>Add Category</a></li>
                    <?php } if(check('items_category_view')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'category';?>"><i class="fas fa-list-ul"></i>Categories List</a></li>
                    <?php }?>
                </ul>
            </li>
            <?php endif;?>
            <?php if(check('customers_view') || check('customers_add')):?>
            <li class="menu-item">
                <a href="#"><i class="fas fa-users"></i><span>Customer</span></a>
                <ul class="sub-menu" >
                    <?php if(check('customers_add')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'customer/add';?>"><i class="fas fa-plus-square"></i>Add Customer</a></li>
                    <?php } if(check('customers_view')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'customer';?>"><i class="fas fa-list-ul"></i>Customer List</a></li>
                    <?php }?>
                </ul>
            </li>
            <?php endif;?>
            <?php if(check('sales_view') || check('sales_add') || check('sales_return_add') || check('sales_return_view')):?>
            <li class="menu-item">
                <a href="#"><i class="fas fa-shopping-cart"></i><span>Sales</span></a>
                <ul class="sub-menu" >
                    <?php if(check('sales_add')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'sales/add';?>"><i class="fas fa-plus-square"></i>Add Sales</a></li>
                    <?php } if(check('sales_view')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'sales';?>"><i class="fas fa-list-ul"></i>Sales List</a></li>
                    <?php } if(check('sales_return_add')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'salesReturn/create';?>"><i class="fas fa-plus-square"></i>Sales Return</a></li>
                    <?php } if(check('sales_return_view')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'salesReturn';?>"><i class="fas fa-list-ul"></i>Sales Return List</a></li>
                    <?php }?>
                </ul>
            </li>
            <?php endif;?>
            <?php if(check('purchase_view') || check('purchase_add') || check('purchase_return_add') || check('purchase_return_view')):?>
            <li class="menu-item">
                <a href="#"><i class="fas fa-truck"></i><span>Purchase</span></a>
                <ul class="sub-menu" >
                    <?php if(check('purchase_add')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'purchase/add';?>"><i class="fas fa-plus-square"></i>Add Purchase</a></li>
                    <?php } if(check('purchase_view')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'purchase';?>"><i class="fas fa-list-ul"></i>Purchase List</a></li>
                    <?php } if(check('purchase_return_add')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'purchaseReturn/create';?>"><i class="fas fa-plus-square"></i>Purchase Return</a></li>
                    <?php } if(check('purchase_return_view')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'purchaseReturn';?>"><i class="fas fa-list-ul"></i>Purchase Return List</a></li>
                    <?php }?>
                </ul>
            </li>
            <?php endif;?>
            <?php if(check('suppliers_view') || check('suppliers_add')):?>
            <li class="menu-item">
                <a href="#"><i class="fas fa-id-badge"></i><span>Supplier</span></a>
                <ul class="sub-menu" >
                    <?php if(check('suppliers_add')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'supplier/add';?>"><i class="fas fa-plus-square"></i>Add Supplier</a></li>
                    <?php } if(check('suppliers_view')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'supplier';?>"><i class="fas fa-list-ul"></i>Supplier List</a></li>
                    <?php }?>
                </ul>
            </li>
            <?php endif;?>
            <?php if(check('users_view') || check('users_add') || check('roles_add')):?>
            <li class="menu-item">
                <a href="#"><i class="fas fa-users-cog"></i><span>Users</span></a>
                <ul class="sub-menu" >
                    <?php if(check('users_add')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'users/add';?>"><i class="fas fa-plus-square"></i>Add User</a></li>
                    <?php } if(check('users_view')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'users';?>"><i class="fas fa-list-ul"></i>Users List</a></li>
                    <?php }?>
                    <?php if(check('roles_add')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'roles/add';?>"><i class="fas fa-plus-square"></i>Add Role</a></li>
                    <?php }?>
                    <?php if(check('roles_view')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'roles';?>"><i class="fas fa-list-ul"></i>Roles List</a></li>
                    <?php }?>
                </ul>
            </li>
            <?php endif;?>
            <?php if(check('sales_report') || check('sales_return_report') || check('purchase_report') || check('purchase_return_report') || check('stock_report') || check('item_sales_report') || check('purchase_payments_report') || check('sales_payments_report') || check('purchase_payments_report') || check('expired_items_report')):?>
            <li class="menu-item">
                <a href="#"><i class="fas fa-bar-chart"></i><span>Reports</span></a>
                <ul class="sub-menu" id="report-menu" >
                    <?php if(check('sales_report')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'report/sales';?>"><i class="fas fa-table"></i>Sales Report</a></li>
                    <?php } if(check('sales_payments_report')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'report/salespayment';?>"><i class="fas fa-table"></i>Sales Payments Report</a></li>
                    <?php } if(check('sales_return_report')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'report/salesReturn';?>"><i class="fas fa-table"></i>Sales Return Report</a></li>
                    <?php }?>
                    <?php if(check('purchase_report')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'report/purchase';?>"><i class="fas fa-table"></i>Purchase Report</a></li>
                    <?php }?>
                    <?php if(check('purchase_payments_report')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'report/purchasepayment';?>"><i class="fas fa-table"></i>Purchase Payments Re...</a></li>
                    <?php }?>
                    <?php if(check('purchase_return_report')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'report/purchaseReturn';?>"><i class="fas fa-table"></i>Purchase Return Re...</a></li>
                    <?php }?>
                    <?php if(check('stock_report')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'report/stock';?>"><i class="fas fa-table"></i>Stock Report</a></li>
                    <?php }?>
                    <?php if(check('item_sales_report')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'report/itemsales';?>"><i class="fas fa-table"></i>Product Sales Report</a></li>
                    <?php }?>
                    <?php if(check('expired_items_report')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'report/expireditems';?>"><i class="fas fa-table"></i>Expired Product Report</a></li>
                    <?php }?>
                </ul>
            </li>
            <?php endif;?>
            <li class="menu-item">
                <a href="<?php echo PUBLIC_ROOT.'settings'?>"><i class="fas fa-cog"></i><span>Settings</span></a>
            </li>
        </ul>
    </div>
    <div class="toggler">
        <a href="#" id="btn-toggler" class="btn-toggler"></a>
    </div>
</nav>
                        