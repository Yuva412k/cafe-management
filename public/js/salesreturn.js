$('#create,#save,#update').click(function (e) {
	var base_url=$("#baseUrl").val().trim();

    e.preventDefault();
    //Initially flag set true
    var flag=true;

    function check_field(id)
    {

      if(!$("#"+id).val().trim() ) //Also check Others????
      //   {

      //       // $('#'+id+'_msg').fadeIn(200).show().html('Required Field').addClass('required');
      //      // $('#'+id).css({'background-color' : '#E8E2E9'});
            flag=false;
      //   }
      //   else
      //   {
      //       //  $('#'+id+'_msg').fadeOut(200).hide();
      //        //$('#'+id).css({'background-color' : '#FFFFFF'});    //White color
      //   }
    }


   //Validate Input box or selection box should not be blank or empty
	check_field("customer_id");
  check_field("return_id");
  check_field("return_date");
  check_field("return_status");

	if(flag==false)
	{
		toastr["warning"]("Please Fill Required Fields!");
		return;
	}

	//Atleast one record must be added in sales table 
  var rowcount=document.getElementById("hidden_rowcount").value;
	var flag1=false;
	for(var n=1;n<=rowcount;n++){
		if($("#td_data_qty_"+n).val()!=null && $("#td_data_qty_"+n).val()!=''){
			flag1=true;
		}	
	}
	
    if(flag1==false){
    	toastr["warning"]("Please Select Item!!");
        $("#item_search").focus();
		return;
    }
    //end

    if($("#customer_id").val().trim()=='CU0001'){
      if(parseFloat($("#grand_total").text())!=parseFloat($("#amount").val())){
        $("#amount").focus();
        toastr["warning"]("Walk-in Customer Should Pay Complete Amount!!");
        return;
      }
        if($("#payment_type").val()==''){
          toastr["warning"]("Please Select Payment Type!!");
          return;
        }
    }

    var tot_subtotal_amt=$("#sub_total").text();
    var other_charges_amt=$("#other_charges_amt").text();//other_charges include tax calcualated amount
    var tot_dicount_on_all_amt=$("#discount_on_all_amt").text();
    var tot_total_amt=$("#grand_total").text();
    var round_off = $('#round_off_amt').text();
    var tax_cgst = $('#tax_amt_cgst').text();
    var tax_sgst = $('#tax_amt_sgst').text();

    var this_id=this.id;
    
			//if(confirm("Do You Wants to Save Record ?")){
				e.preventDefault();
				data = new FormData($('#sales-form')[0]);//form name
        data.append('sub_total', tot_subtotal_amt);
        data.append('other_charges_amt', other_charges_amt);
        data.append('discount_on_all_amt', tot_dicount_on_all_amt);
        data.append('grand_total', tot_total_amt);
        data.append('round_off', round_off);
        data.append('tax_amt_cgst', tax_cgst);
        data.append('tax_amt_sgst', tax_sgst);
        
        $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
        $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
				$.ajax({
				type: 'POST',
				url: base_url+'salesReturn/addSales/'+this_id,
				data: data,
				cache: false,
				contentType: false,
				processData: false,
				success: function(result){
         // alert(result);return;
				result=result.split("&&");
					if(result[0]=="success")
					{
						location.href=base_url+"salesReturn/invoice/"+result[1];
					}
					else if(result[0]=="failed")
					{
					   toastr['error']("Sorry! Failed to save Record.Try again");
					}
					else
					{
						alert(result);
					}
					$("#"+this_id).attr('disabled',false);  //Enable Save or Update button
					$(".overlay").remove();

			   }
			   });
		//}
  
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


$("#item_search").bind("paste", function(e){
    $("#item_search").autocomplete('search');
} );
$("#item_search").autocomplete({
    source: function(data, cb){
        $.ajax({
          autoFocus:true,
            url: $("#baseUrl").val()+'item/getJsonItemsDetails',
            method: 'POST',
            dataType: 'json',
            /*showHintOnFocus: true,
      autoSelect: true, 
      
      selectInitial :true,*/
      
            data: {
                name: data.term,
            },
            success: function(res){
              // console.log(res);
                var result;
                result = [
                    {
                        //label: 'No Records Found '+data.term,
                        label: 'No Records Found ',
                        value: ''
                    }
                ];

                if (res.length) {
                    result = $.map(res, function(el){
                        return {
                            label: el.item_id +'--[Qty:'+el.stock_qty+'] --'+ el.label,
                            value: '',
                            id: el.id,
                            item_name: el.value,
                            stock: el.stock_qty,
                        };
                    });
                }

                cb(result);
            }
        });
    },
        response:function(e,ui){
          if(ui.content.length==1){
            $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
            $(this).autocomplete("close");
          }
          //console.log(ui.content[0].id);
        },
        //loader start
        search: function (e, ui) {
        },
        select: function (e, ui) { 

            if(typeof ui.content!='undefined'){
              if(isNaN(ui.content[0].id)){
                return;
              }
              var stock=ui.content[0].stock;
              var item_id=ui.content[0].id;
            }
            else{
              var stock=ui.item.stock;
              var item_id=ui.item.id;
            }
            if(parseFloat(stock)<=0){
              toastr["warning"](stock+" Items in Stock!!");
              return false;
            }
            if(restrict_quantity(item_id)){
              returnRowWithData(item_id);  
            }
          $("#item_search").val('');
        },   
        //loader end
});

function returnRowWithData(item_id){
  $("#item_search").addClass('ui-autocomplete-loader-center');
	var base_url=$("#baseUrl").val().trim();
	var rowcount=$("#hidden_rowcount").val();
	$.post(base_url+"salesReturn/returnRowWithData",{rowcount, item_id},function(result){
        //alert(result);
        $('#salesreturn_table tbody').append(result);
       	$("#hidden_rowcount").val(parseFloat(rowcount)+1);
        $("#item_search").removeClass('ui-autocomplete-loader-center');
        $("#item_search").val('');
        calculateQty(rowcount);
    }); 
}
//INCREMENT ITEM
function calculateQty(rowcount){
  
  var flag = restrict_quantity($("#tr_item_id_"+rowcount).val().trim());
  if(!flag){ return false;}

  var item_qty=$("#td_data_qty_"+rowcount).val();
  var available_qty=$("#tr_available_qty_"+rowcount+"_13").val();
  if(item_qty==''){
    item_qty = 1;
  }else if(item_qty<=1){
    $("#td_data_qty_"+rowcount).val(1);
  }else if(parseFloat(item_qty)<parseFloat(available_qty)){
    item_qty=parseFloat(item_qty);
    $("#td_data_qty_"+rowcount).val(item_qty);
  }

  calculate_amount(rowcount);
}

function update_paid_payment_total() {  
  var rowcount=$("#paid_amt_tot").attr("data-rowcount");
  var tot=0;
  for(i=1;i<rowcount;i++){
    if(document.getElementById("paid_amt_"+i)){
      tot += parseFloat($("#paid_amt_"+i).html());
    }
  }
  $("#paid_amt_tot").html(tot.toFixed(2));
}
function delete_payment(payment_id){
 if(confirm("Do You Wants to Delete Record ?")){
    var base_url=$("#baseUrl").val().trim();
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
   $.post(base_url+"salesReturn/delete_payment",{payment_id:payment_id},function(result){
   //alert(result);return;
   result=result.trim();
     if(result=="success")
        { 
          toastr["success"]("Record Deleted Successfully!");
          $("#payment_row_"+payment_id).remove();
          success.currentTime = 0; 
          success.play();
        }
        else if(result=="failed"){
          toastr["error"]("Failed to Delete .Try again!");
          failed.currentTime = 0; 
          failed.play();
        }
        else{
          toastr["error"](result);
          failed.currentTime = 0; 
          failed.play();
        }
        $(".overlay").remove();
        update_paid_payment_total();
   });
   }//end confirmation   
  }

  //Delete Record start
function delete_sales(id)
{
   csrf_token = $('#csrf_token').val()
   if(confirm("Do You Wants to Delete Record ?")){
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $.post("salesReturn/removeSales",{return_id:id, csrf_token:csrf_token},function(result){
   //alert(result);return;
     if(result=="success")
        {
          toastr["success"]("Record Deleted Successfully!");
          $('#salesreturn_list').DataTable().ajax.reload();
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
    url: 'salesReturn/removeMultipleSales',
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
        $('#salesreturn_list').DataTable().ajax.reload();
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

function pay_now(sales_id){
  $.post('salesReturn/show_pay_now_modal', {sales_id: sales_id}, function(result) {
    $(".pay_now_modal").html('').html(result);
    //Date picker
    $('.datepicker').datepicker({
      autoclose: true,
    format: 'dd-mm-yyyy',
     todayHighlight: true
    });
    $('#pay_now').modal('toggle');

  });
}
function view_payments(sales_id){
  $.post('salesReturn/view_payments_modal', {sales_id: sales_id}, function(result) {
    $(".view_payments_modal").html('').html(result);
    $('#view_payments_modal').modal('toggle');
  });
}

function save_payment(sales_id){
  var base_url=$("#baseUrl").val().trim();

    //Initially flag set true
    var flag=true;

    function check_field(id)
    {

      if(!$("#"+id).val().trim() ) //Also check Others????
        {

            $('#'+id+'_msg').fadeIn(200).show().html('Required Field').addClass('required');
           // $('#'+id).css({'background-color' : '#E8E2E9'});
            flag=false;
        }
        else
        {
             $('#'+id+'_msg').fadeOut(200).hide();
             //$('#'+id).css({'background-color' : '#FFFFFF'});    //White color
        }
    }


   //Validate Input box or selection box should not be blank or empty
    check_field("amount");
    check_field("payment_date");

    var payment_date=$("#payment_date").val().trim();
    var amount=$("#amount").val().trim();
    var payment_type=$("#payment_type").val().trim();
    var payment_note=$("#payment_note").val().trim();

    if(amount == 0){
      toastr["error"]("Please Enter Valid Amount!");
      return false; 
    }

    if(amount > parseFloat($("#due_amount_temp").html().trim())){
      toastr["error"]("Entered Amount Should not be Greater than Due Amount!");
      return false;
    }

    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $(".payment_save").attr('disabled',true);  //Enable Save or Update button
    $.post('salesReturn/save_payment', {return_id: return_id,payment_type:payment_type,amount:amount,payment_date:payment_date,payment_note:payment_note}, function(result) {
      result=result.trim();
  //alert(result);return;
        if(result=="success")
        {
          $('#pay_now').modal('toggle');
          toastr["success"]("Payment Recorded Successfully!");
          $('#example2').DataTable().ajax.reload();
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

function delete_sales_payment(payment_id){
 if(confirm("Do You Wants to Delete Record ?")){
    var base_url=$("#baseUrl").val().trim();
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
   $.post(base_url+"salesReturn/delete_payment",{payment_id:payment_id},function(result){
   //alert(result);return;
   result=result.trim();
     if(result=="success")
        {
          $('#view_payments_modal').modal('toggle');
          toastr["success"]("Record Deleted Successfully!");
          $('#salesreturn_list').DataTable().ajax.reload();
        }
        else if(result=="failed"){
          toastr["error"]("Failed to Delete .Try again!");
        }
        else{
          toastr["error"](result);
        }
        $(".overlay").remove();
   });
   }//end confirmation   
  }

  function restrict_quantity(item_id) {
  	var rowcount=$("#hidden_rowcount").val();
  	var available_qty = 0;
  	var count_item_qty = 0;
  	var selected_item_id = 0;
      for(i=1;i<=rowcount;i++){
        if(document.getElementById("tr_item_id_"+i)){
        	selected_item_id = $("#tr_item_id_"+i).val().trim();
            if( item_id== selected_item_id ){
	             available_qty = parseFloat($("#tr_available_qty_"+i+"_13").val().trim());
	             count_item_qty += parseFloat($("#td_data_qty_"+i).val().trim());
          }
        }
      }//end for
      if(available_qty!=0 && count_item_qty>=available_qty){
        toastr["warning"]("Only "+available_qty+" Items in Stock!!");
      	return false;
      }
      return true;
  }

