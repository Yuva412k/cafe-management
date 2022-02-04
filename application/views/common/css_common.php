
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" integrity="sha384-vSIIfh2YWi9wW0r9iZe7RJPrKwp6bG+s9QZMoITbCckVJqGCCRhc+ccxNcdpHuYu" crossorigin="anonymous">
<link rel="stylesheet" href="<?php echo PUBLIC_ROOT?>css/style.css">
<link rel="stylesheet" href="<?php echo PUBLIC_ROOT?>css/table.css">
<link rel="stylesheet" href="<?php echo PUBLIC_ROOT?>Plugins/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php 
$webTheme = 'theme1';
if(isset($_COOKIE['webTheme'])){
    switch($_COOKIE['webTheme']){
        case 1 : 
            $webTheme = 'theme1';break;
        case 2 : 
            $webTheme = 'theme2';break;
        case 3 : 
            $webTheme = 'theme3';break;
    }
}else{
    $webTheme = 'theme1';
}
?>
<link rel="stylesheet" href="<?php echo PUBLIC_ROOT?><?php echo 'css/'.$webTheme.'.css';?>">

<style>
 .preloader{
        width: 100vw;
        height: 100vh;
        background: var(--base-bgcolor);
        position: fixed;
        top: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10;
    }
    .preloader-circle{
        width: 70px;
        height: 70px;
        position: relative;
    }
    .preloader-circle .ring{
        height: 100%;
        width: 100%;
        overflow: hidden;
        position:absolute;
    }
    .ring .line{
        border: 10px solid var(--theme);
        border-radius: 50%;
        position:absolute;
    }
    .ring.outer .line{
        width: 100%;
        height: 100%;
        border-top-color: transparent;
        border-bottom-color: transparent;
        animation: 1s rotateR linear infinite;
    }
    .ring.inner{
        display: grid;
        place-items: center;
        animation: 1s rotateR reverse linear infinite;
    }
    .ring.inner .line{
        width: 60%;
        height: 60%;
        border-left-color: transparent;
        border-right-color: transparent;
    }
    @keyframes rotateR{
        0%{
            transform: rotate(0);
        }100%{
            transform: rotate(360deg);
        }
    }
</style>
