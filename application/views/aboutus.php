<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once APP.'views/common/css_common.php'?>
    <title>About Us</title>

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
        width: 200px;
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
    .box-container{
        text-align: center;
        display: flex;
        width: 90%;
        justify-content: center;
        background-color: transparent;
    }
    .box{
        box-shadow: 2px 2px 5px var(--box-shadow-color);
        flex-basis: 250px;
        margin: 10px;
        padding: 10px;
        background-color: var(--box-bgcolor);
    }
    .box-content{
        text-align: left;
    }
    i{
        padding: 10px;
    }
    .row{
        margin: 10px 0;
    }
    span{
        color: grey;
        padding:10px;
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
            <li><a href="<?php echo PUBLIC_ROOT.'Login/contact'?>">Contact</a></li>
            </ul>
        </div>
    </header>
    <div class="container">
        <h1 style="padding: 10px;">About Us</h1>
       <div class="box-container">
            <div class="box b1">
                <div class="box-header">
                    <h2 style="display: block;">Team Leader</h2>
                    <i class="fas fa-user-circle" style="font-size: 8vw;opacity:.5;"></i>
                    <h3>Yuvaraj</h3>
                </div>
                <div class="box-content">
                    <div class="icon-container">
                    <div class="row">
                        <i class="fa fa-facebook-square"></i><span>facebook.com/yuva2000</span>
                    </div>
                    <div class="row">
                        <i class="fa fa-instagram"></i><span>instagram.com/yuvaraj412k</span>
                    </div>
                    <div class="row">
                        <i class="fa fa-whatsapp"></i><span>+91 98765 43210</span>
                    </div>
                    <div class="row">
                        <i class="fa fa-google"></i><span>yuvaraj412k@gmail.com</span>
                    </div>
                    </div>
                </div>
            </div>
            <div class="box b1">
                <div class="box-header">
                    <h2 style="display: block;">Designer</h2>
                    <i class="fas fa-user-circle" style="font-size: 8vw;opacity:.5;"></i>
                    <h3>Aaranjan</h3>
                </div>
                <div class="box-content">
                    <div class="icon-container">
                    <div class="row">
                        <i class="fa fa-facebook-square"></i><span>facebook.com/aaranjan.don.56</span>
                    </div>
                    <div class="row">
                        <i class="fa fa-instagram"></i><span>instagram.com/joseph5229</span>
                    </div>
                    <div class="row">
                        <i class="fa fa-whatsapp"></i><span>+91 98765 43210</span>
                    </div>
                    <div class="row">
                        <i class="fa fa-google"></i><span>aaranjan26@gmail.com</span>
                    </div>
                    </div>
                </div>
            </div>
            <div class="box b1">
                <div class="box-header">
                    <h2 style="display: block;">Programmer</h2>
                    <i class="fas fa-user-circle" style="font-size: 8vw;opacity:.5;"></i>
                    <h3>Boobaalan</h3>
                </div>
                <div class="box-content">
                    <div class="icon-container">
                    <div class="row">
                        <i class="fa fa-facebook-square"></i><span>facebook.com/boobaalan2001</span>
                    </div>
                    <div class="row">
                        <i class="fa fa-instagram"></i><span>instagram.com/boobaalan2001</span>
                    </div>
                    <div class="row">
                        <i class="fa fa-whatsapp"></i><span>+91 98765 43210</span>
                    </div>
                    <div class="row">
                        <i class="fa fa-google"></i><span>boobaalan1803@gmail.com</span>
                    </div>
                    </div>
                </div>
            </div>
            <div class="box b1">
                <div class="box-header">
                    <h2 style="display: block;">Programmer</h2>
                    <i class="fas fa-user-circle" style="font-size: 8vw;opacity:.5;"></i>
                    <h3>Govarthanan</h3>
                </div>
                <div class="box-content">
                    <div class="icon-container">
                    <div class="row">
                        <i class="fa fa-facebook-square"></i><span>facebook.com/govarthanan</span>
                    </div>
                    <div class="row">
                        <i class="fa fa-instagram"></i><span>instagram.com/govarthanan</span>
                    </div>
                    <div class="row">
                        <i class="fa fa-whatsapp"></i><span>+91 98765 43210</span>
                    </div>
                    <div class="row">
                        <i class="fa fa-google"></i><span>govarthanan@gmail.com</span>
                    </div>
                    </div>
                </div>
            </div>
       </div>
    </div>
<script src="<?php echo PUBLIC_ROOT.'Plugins/jquery/jquery-3.5.1.min.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'js/common.js'?>"></script>

</body>
</html>