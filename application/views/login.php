<?php
use app\core\Session;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once APP.'views/common/css_common.php'?>
    <title>Login</title>
    <style>
    body{
        flex-direction: column;
        height: 100vh;
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
            <h2 style="margin-top:10px">Cafeteria</h2>
        </div>
        <div class="nav">
            <ul>
            <a href="#" onclick="darkmode()"><i class="fas fa-adjust"></i></a>
                <li><a href="<?php echo PUBLIC_ROOT.'Login/contact'?>">Contact</a></li>
                <li><a href="<?php echo PUBLIC_ROOT.'Login/aboutus'?>">About Us</a></li>
            </ul>
        </div>
    </header>
    <div class="container">     
    <div>    
        <div class="logo"><h1 style="text-align: center;">Cafe Management</h1></div>
        <div class="box-container">
            <form id='login-form' class="validate-form" method="POST" action="<?php echo PUBLIC_ROOT."Login/login"?>">
            <span class="error_msg" style="color:#ff4141;font-size:12px">
<?php            
if(isset($error_text) && $error_text != ''){
    echo $error_text;
}?>
            </span>
                <div>
                    <label for="username">Username</label>
                    <div style="width: 100%;" class="validate-input" data-validate="Username is required">
                        <input type="text" class="req-input" id='username' name="username" placeholder="Username">    
                    </div>
                </div>
                <div>
                    <label for="password">Password</label>
                    <div style="width: 100%;" class="validate-input" data-validate="Password is required">
                    <input type="password" class="req-input" id='password' name="password" placeholder="Password">
                    </div>
                </div>              
                <label for='remember_me' ><input type="checkbox" id='remember_me' name="remember_me" value="remember_me"> remember me</label>
                <div id="ft-pass">
                    <a href="#">Forgot Password?</a>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>">
                <input type="hidden" id="baseURL" value="<?php echo PUBLIC_ROOT; ?>">
                <input type="submit" value="Login" name="submit" id="submit">
            </form>
        </div>
        </div>
    </div>

<footer>
    <span>Copyright &copy; 2021. All Rights Reserved</span>
</footer>

<script src="<?php echo PUBLIC_ROOT.'Plugins/jquery/jquery-3.5.1.min.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'js/common.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'js/login.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'Plugins/toastr/toastr.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'Plugins/toastr/toastr_custom.js'?>"></script>

</body>
</html>