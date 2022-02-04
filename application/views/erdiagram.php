<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once APP.'views/common/css_common.php'?>
    <title>Contact</title>

    <style>
  body{
        flex-direction: column;
        height: 100vh;
        color: var(--text-color);
    }
    header{
        width:100%;
        height:70px;
        padding: 0 5%;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    header .logo{
        margin: 0 !important;
        display: flex;
        align-items: center;
        border: none !important;
    }
    #logo_image{
        width: 50px;
        height: 50px;
        border-radius: 50%;
        z-index: 1;
    }
    #logo_image img{
        width: 100%;
        height: 100%;
    }
  
    .logo a, h2 {
        display: inline-block;
        padding: 2px;   
        color: var(--text-color);
    }
    .nav ul{
        width: 100px;
        display: flex;
        justify-content: space-around;
        align-items: center;
    }
    .nav li{
        list-style: none;
    }
    .nav a{
        text-decoration: none;
        color: var(--text-color);
        font-size: 14px;
        font-weight: bolder;
    }
        
    </style>
</head>
<body>
<div class="preloader">
        <div class='preloader-container'>
            <div class="preloader-circle">
                <div class="ring outer">
                    <div class="line"></div>
                </div>
                <div class="ring inner">
                    <div class="line"></div>
                </div>
            </div>
        </div>
    </div>
    <header>
        <div class="logo">
            <a href="#" id="logo_image"><img src="<?php echo PUBLIC_ROOT.'image/logo.png'?>"></a>
            <h2 style="margin-top:10px;padding:2px">Cafeteria</h2>
        </div>
        <div class="nav">
            <ul>
            <a href="#" onclick="darkmode()"><i class="fas fa-adjust"></i></a>
                <li><a href="<?php echo PUBLIC_ROOT.'Login'?>">Home</a></li>
            </ul>
        </div>
    </header>
    <div class="container">
        <h2 style="text-align: center;">ER Diagram</h2>
        <img src="<?php echo PUBLIC_ROOT.'image/erdiagram.png'?>" alt="" height="100%">
    </div>
<script src="<?php echo PUBLIC_ROOT.'Plugins/jquery/jquery-3.5.1.min.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'js/common.js'?>"></script>

</body>
</html>