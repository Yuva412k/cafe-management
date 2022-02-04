<?php

use app\core\Session;
?>
<section class="wrapper">
  <div class="content-wrapper">
        <div class="content-header">
            <h2>Dashboard</h2>
        </div>
        
    <!-- FLASH MESSAGE START -->
        <?php include_once APP.'views/common/flashdata.php'?>
    <!--FLASH MESSAGE END -->
       
    <div class="wrapper-container">
        <h3 style="text-align: center;font-size:20px;opacity:.3">Welcome </h3>
        <h3 style="text-align: center;font-size:40px;opacity:.3">Cafe Management System </h3>
    </div>
  </div>
</section>
    
    <?php include_once APP.'views/common/common_js.php'?>
