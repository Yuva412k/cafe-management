<?php

use app\core\Session;

?>
<?php if(Session::getFlashData('welcome')):?>
    <div class="flash-success">
        <strong>
            Welcome to Cafe Management System
        </strong>
    </div>
<?php endif;?>       
