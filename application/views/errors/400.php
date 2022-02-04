<html>
    <head>
        <title>Bad Request</title>
        <?php include_once APP.'views/common/css_common.php';?>
        <style>
body{
    display:unset;
    overflow: hidden;
}
.context {
    height:100%;
    width: 100%;
    display: grid;
    place-items:center;
    
}

.context h1{
    text-align: center;
    color: var(--text-color);
    font-size: 40px;
}


.area{
    background: #4e54c8;  
    background: var(--base-bgcolor);  
    width: 100%;
    height:100vh;
    
   
}
#error-btn{
    padding:8px 10px;
    border: 2px solid var(--text-color);
    text-decoration: none;
    display: inline-block;
    color: var(--btn-color);
    margin: -20px 0 10px 0;
    transition: .3s;
}
#error-btn:hover{
    color: var(--base-bgcolor);
    border: 2px solid var(--base-bgcolor);
    background-color: var(--btn-color);
}
.circles{
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: -1;
}

.circles li{
    position: absolute;
    display: block;
    list-style: none;
    width: 20px;
    height: 20px;
    background: rgba(255, 255, 255, 0.2);
    animation: animate 25s linear infinite;
    bottom: -150px;
    
}

.circles li:nth-child(1){
    left: 25%;
    width: 80px;
    height: 80px;
    animation-delay: 0s;
}


.circles li:nth-child(2){
    left: 10%;
    width: 20px;
    height: 20px;
    animation-delay: 2s;
    animation-duration: 12s;
}

.circles li:nth-child(3){
    left: 70%;
    width: 20px;
    height: 20px;
    animation-delay: 4s;
}

.circles li:nth-child(4){
    left: 40%;
    width: 60px;
    height: 60px;
    animation-delay: 0s;
    animation-duration: 18s;
}

.circles li:nth-child(5){
    left: 65%;
    width: 20px;
    height: 20px;
    animation-delay: 0s;
}

.circles li:nth-child(6){
    left: 75%;
    width: 110px;
    height: 110px;
    animation-delay: 3s;
}

.circles li:nth-child(7){
    left: 35%;
    width: 150px;
    height: 150px;
    animation-delay: 7s;
}

.circles li:nth-child(8){
    left: 50%;
    width: 25px;
    height: 25px;
    animation-delay: 15s;
    animation-duration: 45s;
}

.circles li:nth-child(9){
    left: 20%;
    width: 15px;
    height: 15px;
    animation-delay: 2s;
    animation-duration: 35s;
}

.circles li:nth-child(10){
    left: 85%;
    width: 150px;
    height: 150px;
    animation-delay: 0s;
    animation-duration: 11s;
}



@keyframes animate {

    0%{
        transform: translateY(0) rotate(0deg);
        opacity: 1;
        border-radius: 0;
    }

    100%{
        transform: translateY(-1000px) rotate(720deg);
        opacity: 0;
        border-radius: 50%;
    }

}
        </style>
    </head>
    <body>
       <div class="context">
       <div style="text-align: center;">
         <h1 style="font-size:15vw">400</h1>
        <a id="error-btn" href="<?php echo PUBLIC_ROOT;?>">Go Back To Home</a>
         <h1>Bad Request</h1>
      </div>
      </div>

<div class="area" >
            <ul class="circles">
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
            </ul>
    </div >
       <!-- <img src="image.png"> -->
       
    </body>
</html>