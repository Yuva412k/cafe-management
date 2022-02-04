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
        .contact-form{
            width: 800px;
            height: 520px;
            display: flex;
            align-items: center;
            justify-content: space-evenly;
            font-size: var(--font-size);
            box-shadow: 2px 2px 10px var(--box-shadow-color);
        }
        .right-container, .left-container{
            height: 100%;
            width: 100%;
        }
        .right-container{
            background-color: var(--box-bgcolor);
        }
        h2{
            padding: 15px 30px 0;
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
        .btn{
            color: var(--btn-color);
            background-color: var(--btn-bgcolor);
        }
        .btn:hover{
            color: var(--btn-color);
            background-color: var(--btn-hover);
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
                <li><a href="<?php echo PUBLIC_ROOT.'Login/aboutus'?>">About Us</a></li>
            </ul>
        </div>
    </header>
    <div class="container">
    <div class="contact-form">
        <div class="left-container">
            <h2>Write Us</h2>
            <form style="width: 80%;margin:auto">
                <div class="validate-input">
                    <label for="name">Name</label>
                    <input type="text" class="req-input" id="name">
                </div>
                <div class="validate-input">
                    <label for="email">Email</label>
                    <input type="text" class="req-input" id="email">
                </div>
                <div class="validate-input">
                    <label for="subject">Subject</label>
                    <input type="text" class="req-input" id="subject">
                </div>
                <div class="validate-input">
                    <label for="name">Message</label>
                    <textarea name="message" id="message" style="height:80px;width:100%"></textarea>
                </div>
                <br>
                <button class="btn" style="font-size: 13px;">Send Message</button>
            </form>
        </div>
        <div class="right-container">
            <h2>Contact information</h2>
            <p style="width: 80%;margin:10px auto;font-size:14px">We're open for any suggestion or just to have a chat</p>
            <div style="width: 80%;margin:10px auto">

            <div class="row">
                <i class="fas fa-map-marker"></i><b>Address:</b><span>101, cafe street, example city, puducherry - 605004</span>
            </div>
            <div class="row">
                <i class="fas fa-phone"></i><b>Phone:</b><span>+91 98765 43210</span>
            </div>
            <div class="row">
                <i class="fas fa-paper-plane"></i><b>Email:</b><span>cafemanagement@example.com</span>
            </div>
            <div class="row">
                <i class="fas fa-globe"></i><b>Website</b><span>www.cafemanagement.com</span>
            </div>
            </div>

        </div>
    </div>
    </div>
<script src="<?php echo PUBLIC_ROOT.'Plugins/jquery/jquery-3.5.1.min.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'js/common.js'?>"></script>

</body>
</html>