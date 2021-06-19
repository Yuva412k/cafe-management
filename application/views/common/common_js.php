<script src="<?php echo PUBLIC_ROOT.'Plugins/jquery/jquery-3.5.1.min.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'js/common.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'js/sidebar.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'Plugins/select2/select2.min.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'Plugins/toastr/toastr.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'Plugins/toastr/toastr_custom.js'?>"></script>

<?php
    use app\core\Session;
    if(Session::hasflashData('complete')){
        echo '<script>$(document).ready(function(){toastr["success"]("'.Session::getFlashData('complete').'")});</script>';
    }
?>
