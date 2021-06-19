// Show hide popover
$(document).ready(function(){

    $(".menu-item").click(function(){
        $(this).find(".sub-menu").slideToggle("fast");
    });
    $(document).on("click", function(event){
        var $trigger = $(".menu-item");
        if($trigger !== event.target && !$trigger.has(event.target).length){
            $(".sub-menu").slideUp("fast");
        }            
    });

$("#btn-toggler").click(function(){
    $(".navbar").toggleClass('collapse');
    $(".wrapper").toggleClass('collapse');
});
});
