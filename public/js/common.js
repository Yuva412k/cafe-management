/**
 * Preloader
 */
$(window).on('load',function(){

    if(localStorage.getItem('sidebar') === 'open'){
        $(".navbar").removeClass('collapse');
        $(".wrapper").removeClass('collapse');
    }
    $(".preloader").fadeOut('slow');
})


/**
 * Dropdown Menu Toggle
 */
$(document).ready(function(){
    // Show hide popover

    $('.dropdown a').click(function(){
        $('.dropdown').removeClass('open');
        $(this).parent().toggleClass('open');
    })
    $(document).on("click", function(event){
        var $trigger = $(".dropdown");
        if($trigger !== event.target && !$trigger.has(event.target).length){
            $(".dropdown").removeClass("open");
        }
    });  

    $(".number").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
           //display error message
                  return false;
       }
      });

});
function setCookie(key, value, expiry) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 1000));
    document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
}

function getCookie(key) {
    var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
    return keyValue ? keyValue[2] : null;
}


function darkmode(){
    if(getCookie('webTheme') != null){
        if(getCookie('webTheme') == 1){
        setCookie('webTheme','2','3');
        }else{
        setCookie('webTheme','1','3');
        }
    }else{
        setCookie('webTheme','2','3');
    }
    window.location.reload();
}

function dropdown(obj){

    $('.dropdown').removeClass('open');
    obj.parentNode.classList.toggle('open');
    $(document).on("click", function(event){
        var $trigger = $(".dropdown");
        if($trigger !== event.target && !$trigger.has(event.target).length){
            $(".dropdown").removeClass("open");
        }
    });  
}

  function validateForm(input = ''){
    var input = $('.validate-input .req-input');
    var check = true;

    for(var i=0; i<input.length; i++) {
        if(validate(input[i]) == false){
            showValidate(input[i]);
            check=false;
        }
    }

    return check;
  }

  $('.validate-form .req-input').each(function(){
      $(this).focus(function(){
         hideValidate(this);
      });
  });

  function validate (input) {
    if($(input).val().trim() == ''){
        return false;
    }
  }

  function showValidate(input) {
      var thisAlert = $(input).parent();

      $(thisAlert).addClass('alert-validate');
  }

  function hideValidate(input) {
      var thisAlert = $(input).parent();

      $(thisAlert).removeClass('alert-validate');
  }

  
$(document).on('ajaxError',function(event, xhr){
    if(xhr.status === 403){
        toastr['warning']("Sorry! You don't have permission to do this action");
    }
});