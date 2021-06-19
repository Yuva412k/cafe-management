<?php
use app\core\Session;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT?>css/style.css">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <div class="logo"><h1>Cafe Management</h1></div>
        <div class="box-container">
            <form method="POST" action="<?php echo PUBLIC_ROOT."Login/login"?>">
                <?php if(Session::hasflashData('login-failed')): ?>
                <div class="error">
                    <?php
                        $flashdata =  Session::getFlashdata('login-failed');
                        foreach($flashdata as $error){
                            echo "<span>$error</span><br>";
                        }
                    ?>
                </div>
                <?php endif;?>
                <div>
                    <label for="username">Email</label>
                    <input type="text" name="username" placeholder="Username">    
                </div>
                <div>
                    <label for="password">Password</label>
                    <input type="password" name="password" placeholder="Password">
                </div>              
                <div id="ft-pass">
                    <a href="#">Forgot Password?</a>
                </div>
                <input type="checkbox" name="remember_me">
                <input type="hidden" name="csrf_token" value=<?php echo Session::generateCsrfToken(); ?>>
                <input type="submit" value="Login" name="submit" id="btn">
            </form>
        </div>
    </div>
</body>
</html>