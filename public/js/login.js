$('#submit').click(function (e) {

    e.preventDefault();
    //Initially flag set true
    var flag=true;
    var base_url = $('#baseURL').val();

    if(!validateForm()){
		toastr["warning"]("Please Fill Required Fields!");
        return;
    }

    var this_id = this.id;

    data = new FormData($('#login-form')[0]);//form name

    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
    $.ajax({
    type: 'POST',
    url: base_url+'Login/login',
    data: data,
    cache: false,
    contentType: false,
    processData: false,
    success: function(result){

        if(result=="success")
        {
            window.location=base_url+"dashboard";
            return;
        }
        else if(result=="failed")
        {
            toastr["error"]("Sorry! Incorrect Username or Password");
        }
        else
        {
            toastr["error"](result);
            $('.error_msg').html(result);
        }
        $("#"+this_id).attr('disabled',false);  //Enable Save or Update button
        $(".overlay").remove();
    }
    });
})