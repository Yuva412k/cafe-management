$(document).ready(function(){
    // Show hide popover
    $(".dropdown").click(function(){
        $(this).find(".dropdown-menu").slideToggle("fast");
    });

});
function dropdown(el){
    $(el).parent().find(".dropdown-menu").slideToggle("fast");
    console.log( "whore");
}
// $(document).on("click", function(event){
//     var $trigger = $(".dropdown");
//     if($trigger !== event.target && !$trigger.has(event.target).length){
//         $(".dropdown-menu").slideUp("fast");
//     }            
// });

$(document).on('ajaxError',function(event, xhr){
    if(xhr.status === 403){
        toastr['warning']("Sorry! You don't have permission to do this action");
    }
});