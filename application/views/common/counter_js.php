<script src="<?php echo PUBLIC_ROOT.'Plugins/jquery/jquery-2.1.3.min.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'Plugins/jquery/jquery.counterup.min.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'Plugins/jquery/waypoints2.0.3.min.js'?>"></script>

<script >
    let jQuery_2_1_3 = $.noConflict(true)
    jQuery_2_1_3('.counter').counterUp({
                delay: 10,
                time: 2000
    });
</script>