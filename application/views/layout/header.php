<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include_once APP.'views/common/css_common.php';?>
    </head>
<body>
<div class="wrapper">
    <div class="header-container" id="header">
        <div class="left-sec">
            <div class="dropdown">
                <a href="#" class="nav-btn">+</a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo PUBLIC_ROOT.'sales/add';?>">Sales</a></li>
                    <li><a href="<?php echo PUBLIC_ROOT.'purchase/add';?>">Purchase</a></li>
                    <li><a href="<?php echo PUBLIC_ROOT.'customer/add';?>">Customer</a></li>
                    <li><a href="<?php echo PUBLIC_ROOT.'supplier/add';?>">Supplier</a></li>
                    <li><a href="<?php echo PUBLIC_ROOT.'item/add';?>">Item</a></li>
                    <li><a href="<?php echo PUBLIC_ROOT.'expense/add';?>">Expense</a></li>
                </ul>
            </div>
        </div>
        <div class="right-sec">
            <a href="#">Dashboard</a>
            <div class="dropdown">
                <a href="#" data-val="active" data-target="dropdown-menu" ><i class="fas fa-user-circle"></i></a>
                <ul class="dropdown-menu profile">
                    <li><div class="profile-info">
                    <i class="fas fa-user-circle"></i>
                        <div>
                            <h3>username</h3>
                            <span>example@gmail.com</span>
                        </div>
                    </div></li>
                    <li><a href="#">Profile</a></li>
                    <li><a href="#">Settings</a></li>
                    <li><a href="<?php echo PUBLIC_ROOT.'login/logout'; ?>">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
