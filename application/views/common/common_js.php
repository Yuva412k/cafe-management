<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script><script src="<?php echo PUBLIC_ROOT.'js/common.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'js/sidebar.js'?>"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="<?php echo PUBLIC_ROOT.'Plugins/toastr/toastr_custom.js'?>"></script>

<?php
    use app\core\Session;
    if(Session::hasflashData('complete')){
        echo '<script>$(document).ready(function(){toastr["success"]("'.Session::getFlashData('complete').'")});</script>';
    }
?>


