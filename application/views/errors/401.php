<html>
    <head>
        <title>UnAuthenticated</title>
        <?php include_once APP.'views/common/css_common.php';?>
        <style>
body{
    display:unset;
}
.context {
    height:100%;
    width: 100%;
    display: grid;
    place-items:center;
    
}

.context h2{
    text-align: center;
    color: var(--text-color);
}

#error-btn{
    padding:8px 10px;
    border: 2px solid var(--text-color);
    text-decoration: none;
    display: inline-block;
    color: var(--btn-color);
    margin:-20px 0 15px 0;
    transition: .3s;
}
#error-btn:hover{
    color: var(--base-bgcolor);
    border: 2px solid var(--base-bgcolor);
    background-color: var(--btn-color);
}
        </style>
    </head>
    <body>
       <div class="context">
       <div style="text-align: center;">
       <h2>Sorry, you aren't authenticated. Please login with valid credentials!</h2>
         <img src="<?php echo PUBLIC_ROOT.'/image/403.gif'?>" id="error403-img" alt="" style="height: 400px;"><br>
        <a id="error-btn" href="<?php echo PUBLIC_ROOT;?>">Go Back To Home</a>
         <h2>UnAuthenticated: 401</h2>
      </div>
      </div>
    </body>
</html>
 