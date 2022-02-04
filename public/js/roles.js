
$('#save,#update').click(function (e) {

    e.preventDefault();

	let base_url=$("#baseURL").val().trim();

    if(!validateForm()){
        toastr["warning"]("Please Fill Required Fields!");
            return;
        }
    

    var this_id=this.id;

    if(this_id=="save")  //Save start
    {
     if(confirm("Do You Wants to Save Record ?")){
        e.preventDefault();
        data = new FormData($('#role-form')[0]);//form name

        $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
        $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
        $.ajax({
        type: 'POST',
        url: base_url+'roles/addRole',
        data: data,
        cache: false,
        contentType: false,
        processData: false,
 
        success: function(result){

            if(result=="success")
            {
                window.location=base_url+"roles/add";
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
            data = new FormData($('#role-form')[0]);//form name
            
            $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
            $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
            $.ajax({
            type: 'POST',
            url: base_url+'roles/updateRole',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(result){
                if(result=="success")
                {
                    //toastr["success"]("Record Updated Successfully!");
                    window.location=base_url+"roles/add";
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
function delete_role(id)
{
    csrf_token = $('#csrf_token').val();
	
   if(confirm("Do You Wants to Delete Record ?")){
   	$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
   $.post("roles/removeRole",{'id': id, 'csrf_token': csrf_token},
   function(result){

    if(result=="success")
        {
            toastr["success"]("Record Deleted Successfully!");
            $('#role_list').DataTable().ajax.reload();
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
			url: 'roles/removeMultipleRole',
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
					$('#role_list').DataTable().ajax.reload();
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

/*Roles Table*/
$('.change_me').click( function(event) {
	var id=this.id;
    if(event.target.checked){
     $("."+id+"_all").prop("checked",true);
    }
    else{
      $("."+id+"_all").prop("checked",false);
    }
});

$('.checkbox input').click(function(event){
    let str = this.id;
    let id = str.split('_')[0];
    let action = str.replace(id+'_','');

    switch(action){
        case 'edit': 
            if($('#'+id+'_edit').prop('checked')){
                $('#'+id+'_view').prop('checked', true).prop('disabled', true);
                $('#'+id+'_add').prop('checked', true).prop('disabled', true);
            }else{
                $('#'+id+'_view').prop('checked', false).prop('disabled', false);
                $('#'+id+'_add').prop('checked', false).prop('disabled', false);
            }
            break;
        case 'delete': 
            if($('#'+id+'_delete').prop('checked')){
                $('#'+id+'_view').prop('checked', true).prop('disabled', true);
            }else{
                $('#'+id+'_view').prop('checked', false).prop('disabled', false);
            }
             break;
        case 'payment_delete': 
             if($('#'+id+'_payment_delete').prop('checked')){
                 $('#'+id+'_payment_view').prop('checked', true).prop('disabled', true);
             }else{
                 $('#'+id+'_payment_view').prop('checked', false).prop('disabled', false);
             }
              break;
        case 'return_payment_delete': 
             if($('#'+id+'_return_payment_delete').prop('checked')){
                 $('#'+id+'_return_payment_view').prop('checked', true).prop('disabled', true);
             }else{
                 $('#'+id+'_return_payment_view').prop('checked', false).prop('disabled', false);
             }
              break;
    }
})


