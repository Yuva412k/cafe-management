/*Email validation code*/
function validateEmail(sEmail) {
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(sEmail)) {
        return true;
    }
    else {
        return false;
    }
}

$('#save, #update').click(function(e){

    e.preventDefault();

    let baseURL = $('#baseURL').val().trim();

    // var email = $('#email').val().trim();
    flag = true;
    function check_field(id)
    {

      if(!$("#"+id).val().trim() ) //Also check Others????
        {
            $('#'+id+'_msg').fadeIn(200).show().html('Required Field').addClass('required');
            $('#'+id).css({'background-color' : '#E8E2E9'});
            flag=false;
        }
        else
        {
             $('#'+id+'_msg').fadeOut(200).hide();
             $('#'+id).css({'background-color' : '#FFFFFF'});    //White color
        }
    }

    //Validate form input's
    check_field('cust_name');
    check_field('cust_id');
    
    // var email = $('#email').val().trim();
    // if(email!='' && !validateEmail(email)){
    //     $('#email_msg').html('Invalid Email!').show();
    //     return toastr['warning']('Please Enter valid Email ID.');
    // }else{
    //     $('#email_msg').html('Invalid Email!').hide();
    // }

    if(flag===false){
        toastr['warning']('Please Fill Required Fields')
        return;
    }

    var this_id = this.id;

    if(this_id == 'save')//Save popup
    {
        if(confirm('Do You Wants to Save Record ?')){
            e.preventDefault();
            data = new FormData($('#customers-form')[0]); 
            //Check xss code

            $('.box').append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');

            $('#'+this_id).attr('disabled', true); // Enable Save or Update button
            $.ajax({
                type: 'POST',
                url: 'addCustomer',
                data : data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(result){
                    if(result=='success'){
                        window.location=baseURL+"customer/add";
                        return;
                    }else if(result=='failed'){
                        toastr['error']('Sorry! Failed to save Record. Try again');
                    }else{
                        toastr['error'](result)
                    }
                    $('#'+this_id).attr('disabled', false); // Enable Save or Update button
                    $('.overlay').remove();
                }
            });
        }
    }
    //update Popup
    else if(this_id == 'update')
    {
        if(confirm('Do You Wants to Save Record ?')){
            e.preventDefault();
            data = new FormData($('#customers-form')[0]); 
            //Check xss code

            $('.box').append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');

            $('#'+this_id).attr('disabled', true); // Enable Save or Update button
            $.ajax({
                type: 'POST',
                url: 'updateCustomer',
                data : data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(result){
                    if(result=='success'){
                        window.location=base_url+"customers/add";
                        return;
                    }else if(result=='failed'){
                        toastr['error']('Sorry! Failed to save Record. Try again');
                    }else{
                        toastr['error'](result)
                    }
                    $('#'+this_id).attr('disabled', false); // Enable Save or Update button
                    $('.overlay').remove();
                }
            });
        }

    }


});


//Delete Record start
function delete_customers(cust_id)
{
	
   if(confirm("Do You Wants to Delete Record ?")){
   	$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
   $.post("customers/delete",{cust_id:cust_id},function(result){

    if(result=="success")
    {
        toastr["success"]("Record Deleted Successfully!");
        $('#cust_list').DataTable().ajax.reload();
    }
    else if(result=="failed"){
        toastr["error"]("Failed to Delete .Try again!");
    }
    else{
        toastr["error"](result);
    }
    $(".overlay").remove();
    return false;
   });
   }
}

function multi_delete(){

    var this_id=this.id;
	//var base_url=$("#base_url").val().trim();
    
		if(confirm("Are you sure ?")){
			$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
			$("#"+this_id).attr('disabled',true);  //Enable Save or Update button
		/************************************TODO form */	
			data = new FormData($('#table_form')[0]);//form name
			$.ajax({
			type: 'POST',
			url: 'customers/multiDelete',
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			success: function(result){
				result=result.trim();
  //alert(result);return;
				if(result=="success")
				{
					toastr["success"]("Record Deleted Successfully!");
					$('#cust_list').DataTable().ajax.reload();
					$(".check-all").prop("checked",false);
				}
				else if(result=="failed")
				{
				   toastr["error"]("Sorry! Failed to save Record.Try again!");
				}
				else
				{
					toastr["error"](result);
				}
				$("#"+this_id).attr('disabled',false);  //Enable Save or Update button
				$(".overlay").remove();
		   }
		   });
	}
	//e.preventDefault
}
