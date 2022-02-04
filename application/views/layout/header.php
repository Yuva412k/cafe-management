<?php

use app\core\Session;
$user = $this->controller->loadModel('usersModel')->getProfileInfo(Session::getUserId());
unset($user['id']);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include_once APP.'views/common/css_common.php';?>
    </head>
<body>
<?php include_once APP.'views/common/preloader.php'?>

<?php include_once APP.'views/layout/sidebar.php';?>
<div class="app">

<div class="header wrapper">
    <div class="header-container" id="header">
        <div class="left-sec">
            <div class="dropdown">
                <a href="#" class="nav-btn">+</a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo PUBLIC_ROOT.'sales/add';?>">Add Sales</a></li>
                    <li><a href="<?php echo PUBLIC_ROOT.'purchase/add';?>">Add Purchase</a></li>
                    <li><a href="<?php echo PUBLIC_ROOT.'customer/add';?>">Add Customer</a></li>
                    <li><a href="<?php echo PUBLIC_ROOT.'supplier/add';?>">Add Supplier</a></li>
                    <li><a href="<?php echo PUBLIC_ROOT.'item/add';?>">Add Product</a></li>
                </ul>
            </div>
        </div>
        <div class="right-sec">
            <ul class="right-sec-content">
                <li>
                    <a href="#" onclick="darkmode()"><i class="fas fa-adjust"></i></a>
                </li>
                <li>
            <a href="<?php echo PUBLIC_ROOT?>">Dashboard</a>
                </li>
                <li>
            <div class="dropdown">
                <a href="#" data-val="active" style="font-size:30px"><i class="fas fa-user-circle"></i></a>
                <ul class="dropdown-menu profile">
                    <li>
                        <div class="profile-info">
                        <i class="fas fa-user-circle" style="font-size:40px;"></i>
                            <div>
                                <h3><?php echo $user['name'] ?></h3>
                                <p><?php echo $user['role_name']?></p>
                            </div>
                        </div>
                    </li>
                    <li><a href="<?php echo PUBLIC_ROOT.'users/update/'.Session::getUserId();?>">Profile</a></li>
                    <li><a href="<?php echo PUBLIC_ROOT.'settings';?>">Settings</a></li>
                    <li><a href="<?php echo PUBLIC_ROOT.'login/logout'; ?>">Logout</a></li>
                </ul>
            </div>
            </li>   
            </ul>

        </div>
    </div>
</div>
