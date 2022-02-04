// // Show hide 
$(document).ready(function(){

$("#btn-toggler").click(function(){
    if(localStorage.getItem('sidebar') ==='open'){
        localStorage.setItem('sidebar','close');
    }else{
        localStorage.setItem('sidebar', 'open');
    }
    $(".navbar").toggleClass('collapse');
    $(".wrapper").toggleClass('collapse');  
});
});
