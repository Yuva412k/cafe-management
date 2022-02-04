
$('#save,#update').click(function (e) {

    e.preventDefault();

    if(!validateForm()){
		toastr["warning"]("Please Fill Required Fields!");
        return;
    }

	let base_url=$("#baseURL").val().trim();
    //Initially flag set true
    let flag=true;

    var this_id=this.id;

    if(this_id=="save")  //Save start
    {
     if(confirm("Do You Wants to Save Record ?")){
        e.preventDefault();
        data = new FormData($('#customer-form')[0]);//form name

        $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
        $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
        $.ajax({
        type: 'POST',
        url: base_url+'customer/addCustomer',
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        success: function(result){

            if(result=="success")
            {
                window.location=base_url+"customer/add";
                return;
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

    }//Save end
	
	else if(this_id=="update")  //Save start
    {
    
        if(confirm("Do You Wants to Update Record ?")){
            e.preventDefault();
            data = new FormData($('#customer-form')[0]);//form name
            
            $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
            $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
            $.ajax({
            type: 'POST',
            url: base_url+'customer/updateCustomer',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(result){
                if(result=="success")
                {
                    //toastr["success"]("Record Updated Successfully!");
                    window.location=base_url+"customer/add";
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

    }//Save end
	

});

//Delete Record start
function delete_customer(customer_id)
{
    csrf_token = $('#csrf_token').val();
	
   if(confirm("Do You Wants to Delete Record ?")){
   	$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
   $.post("customer/removeCustomer",{'customer_id': customer_id, 'csrf_token': csrf_token},function(result){

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
   }//end confirmation
}

//Delete Multiple or Selected Record 

$('#delete_record').click(function(){

    var deleteids = [];
    // Read all checked checkboxes
    $("input:checkbox[class=row_check]:checked").each(function () {
       deleteids.push($(this).val());
    });
    csrf_token = $('#csrf_token').val();

    // Check checkbox checked or not
    if(deleteids.length > 0){

		if(confirm("Are you sure ?")){
			$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
			$("#delete_record").attr('disabled',true);  //Enable Save or Update button
			data = new FormData()
            for(let i = 0; i < deleteids.length; i++){
                data.append('deleteids[]', deleteids[i]);
            }
			$.ajax({
			type: 'POST',
			url: 'customer/removeMultipleCustomer',
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
					$(".checkall").prop("checked",false);
				}
				else if(result=="failed")
				{
				   toastr["error"]("Sorry! Failed to save Record.Try again!");
				}
				else
				{
					toastr["error"](result);
				}
				$("#delete_record").attr('disabled',false);  //Enable Save or Update button
				$(".overlay").remove();
		   }
		   });
	    }
    }else{
        toastr['warning']("No Rows Selected");
    }
	//e.preventDefault
    
});

// Checkbox checked
function checkcheckbox(){

    // Total checkboxes
    var length = $('.row_check').length;

    // Total checked checkboxes
    var totalchecked = 0;
    $('.row_check').each(function(){
        if($(this).is(':checked')){
            totalchecked+=1;
        }
    });

    // Checked unchecked checkbox
    if(totalchecked == length){
         $("#checkall").prop('checked', true);
    }else{
         $('#checkall').prop('checked', false);
    }
}


function pay_now(customer_id){

    $.post($("#baseURL").val().trim()+'customer/showPayNowModal', {'customer_id': customer_id}, function(result) {
      $(".pay_now_modal").html('').html(result);
        showModal();
  
    });
  }
  function save_payment(customer_id){
    var base_url=$("#baseURL").val().trim();
    
  
        if(!validateForm()){
          toastr["warning"]("Please Fill Required Fields!");
              return;
        }
  
  
      var payment_date=$("#payment_date").val().trim();
      var amount=$("#amount").val().trim();
      var payment_type=$("#payment_type").val().trim();
      var payment_note=$("#payment_note").val().trim();
  
      if(amount == 0){
        toastr["error"]("Please Enter Valid Amount!");
        return false; 
      }
  
      if(amount > parseFloat($("#amount").attr('data-due-amt'))){
        toastr["error"]("Entered Amount Should not be Greater than Due Amount!");
        return false;
      }
  
      //$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
      //$(".payment_save").attr('disabled',true);  //Enable Save or Update button
      $.post('customer/savePayment', {customer_id: customer_id,payment_type:payment_type,amount:amount,payment_date:payment_date,payment_note:payment_note}, function(result) {
        result=result.trim();
    //alert(result);return;
          if(result=="success")
          {
            hideModal();
            toastr["success"]("Payment Recorded Successfully!");
            $('#cust_list').DataTable().ajax.reload();
          }
          else if(result=="failed")
          {
             toastr["error"]("Sorry! Failed to save Record.Try again!");
          }
          else
          {
            toastr["error"](result);
          }
          $(".payment_save").attr('disabled',false);  //Enable Save or Update button
          $(".overlay").remove();
      });
  }
  
  function pay_return_due(customer_id){
  
    $.post($("#baseURL").val().trim()+'customer/showPayReturnDueModal', {customer_id: customer_id}, function(result) {
      $(".pay_now_modal").html('').html(result);

      showModal();
  
    });
  }
  function save_return_due_payment(customer_id){
    var base_url=$("#baseURL").val().trim();
    
  
    if(!validateForm()){
		toastr["warning"]("Please Fill Required Fields!");
        return;
    }
  
      var payment_date=$("#return_due_payment_date").val().trim();
      var amount=$("#return_due_amount").val().trim();
      var payment_type=$("#return_due_payment_type").val().trim();
      var payment_note=$("#return_due_payment_note").val().trim();
  
      if(amount == 0){
        toastr["error"]("Please Enter Valid Amount!");
        return false; 
      }
  
      if(amount > parseFloat($("#return_due_amount").attr('data-due-amt'))){
        toastr["error"]("Entered Amount Should not be Greater than Due Amount!");
        return false;
      }
  
      //$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
      //$(".payment_save").attr('disabled',true);  //Enable Save or Update button
      $.post('customer/saveReturnDuePayment', {customer_id: customer_id,payment_type:payment_type,amount:amount,payment_date:payment_date,payment_note:payment_note}, function(result) {
        result=result.trim();
    //alert(result);return;
          if(result=="success")
          {
            hideModal();
            toastr["success"]("Payment Recorded Successfully!");
            $('#cust_list').DataTable().ajax.reload();
          }
          else if(result=="failed")
          {
             toastr["error"]("Sorry! Failed to save Record.Try again!");
          }
          else
          {
            toastr["error"](result);
          }
          $(".return_due_payment_save").attr('disabled',false);  //Enable Save or Update button
          $(".overlay").remove();
      });
  }
  