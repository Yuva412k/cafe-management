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
        <a href="index.html" id="nav-logo">logo</a>
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
                <a href="#"><i class="fas fa-th-large"></i><span>Item</span></a>
                <ul class="sub-menu" style="display: none;">
                    <?php if(check('items_add')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'item/add';?>"><i class="fas fa-plus-square"></i>Add Item</a></li>
                    <?php } if(check('items_view')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'item';?>"><i class="fas fa-list-ul"></i>Item List</a></li>
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
                <ul class="sub-menu" style="display: none;">
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
                <ul class="sub-menu" style="display: none;">
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
                <ul class="sub-menu" style="display: none;">
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
                <ul class="sub-menu" style="display: none;">
                    <?php if(check('suppliers_add')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'supplier/add';?>"><i class="fas fa-plus-square"></i>Add Supplier</a></li>
                    <?php } if(check('suppliers_view')){?>
                    <li><a href="<?php echo PUBLIC_ROOT.'supplier';?>"><i class="fas fa-list-ul"></i>Supplier List</a></li>
                    <?php }?>
                </ul>
            </li>
            <?php endif;?>
        </ul>
    </div>
    <div class="toggler">
        <a href="#" id="btn-toggler" class="btn-toggler"></a>
    </div>
</nav>
