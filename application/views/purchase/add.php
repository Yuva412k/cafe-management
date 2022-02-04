<?php

    if(!isset($id)){
        $supplier_id=$purchase_status=$reference_no=$other_charges_amt=$other_charges_input=$other_charges_type=$discount_type=$discount_on_all_input=$discount_on_all_amt=$discount_on_all_type=$tax_id=$purchase_status='';
        $purchase_date=date('Y-m-d');
        $discount_on_all_amt = 0.00;
        $other_charges_amt=0.00;
    }else{  
        extract($purchaseData);
    }
?>

<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Purchase</h2>
        </div>
        <div class="wrapper-container">
            <form id="purchase-form" class="validate-form" enctype="multipart/form-data">
                <input type="hidden" name="hidden_rowcount" id="hidden_rowcount" value="1">
                <input type="hidden" id="baseURL" value="<?php echo PUBLIC_ROOT;?>">
                <input type="hidden" value='0' id="hidden_update_rowid" name="hidden_update_rowid">

                <div class="header">
                    <div class="item-pair">
                        <label for="supplier_id">Supplier Name <sup style="color: red">*</sup></label>
                        <div style="width: 60%;" class="validate-input" data-validate="Supplier Name is required">
                        <select class="req-input" name="supplier_id" id="supplier_id" style="width: 100%;">
                        <?php
                                 if(isset($id)){
                                     echo '<option selected value="'.$supplier_id.'">'.$supplier_name.'</option>';
                                 }else{
                                     echo '<option value="">--Select Supplier--</option>';
                                 }
                                 ?>
                        </select>
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="purchase_order">Purchase Id<sup style="color: red">*</sup></label>
                        <div style="width: 60%;" class="validate-input" data-validate="Purchase Id is required">
                        <input type="text" class="req-input" style="width:100%" name="purchase_id" id="purchase_id" readonly value="<?php print $purchase_id ?>" >
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="purchase_date">Purchase Date <sup style="color: red">*</sup></label>
                        <div style="width: 60%;" class="validate-input" data-validate="Purchase date is required">
                        <input type="date" class="req-input" style="width: 100%;" name="purchase_date" id="purchase_date" value="<?php print $purchase_date ?>">
                        </div>
                    </div>

                    <div class="item-pair">
                        <label for="purchase_status">Purchase Status <sup style="color: red">*</sup></label>
                        <div style="width: 60%;" class="validate-input" data-validate="Purchase status is required">
                        <select class="select req-input" style="width: 100%;" name="purchase_status" id="purchase_status">
                           <?php
                            $selectordered = '';
                            $selectreceived = '';
                            $selectpending = '';
                            
                                if(isset($purchase_status)){
                                    if($purchase_status == 'ordered'){$selectordered = 'selected';}
                                    if($purchase_status == 'pending'){$selectpending = 'selected';}
                                    if($purchase_status == 'received'){$selectreceived = 'selected';}
                                }
                           ?>
                            <option <?= $selectordered?>value="ordered">Ordered</option>
                            <option <?= $selectpending?> value="received">Received</option>
                            <option <?= $selectpending?> value="pending">Pending</option>
                        </select>
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="reference_no">Reference No</label>
                        <input type="text" name="reference_no" id="reference_no" value="<?php print $reference_no?>">
                    </div>                
                    <div class="item-pair"></div>
                </div>
                <hr style="background-color: var(--border-color);">
                <br>
                <div style="display: flex;justify-content:center;">
                <label for="item_search" style="margin-right: 20px;">Select Product</label>
                <input type="text" placeholder="Product name or Product code" id="item_search" style="flex-basis: 400px;" value="">
                </div>
                <br>
                <hr style="background-color: var(--border-color);">
                <br>

                <div class="purchase-table tb-h10">
                    <table style="width: 100%" id="purchase_table">
                        <thead style="text-align: right !important;">
                            <tr>
                                <th style="text-align: left !important;">PRODUCT NAME</th>
                                <th>QUANTITY</th>
                                <th>RATE</th>
                                <th>DISCOUNT</th>
                                <th>AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

                <hr style="background-color: var(--border-color);">

                <div class="table-row">
                    <div class="table-col" style="flex-basis: 400px;">
                    <div class="" style="display: flex;flex-wrap:wrap">
                    <div style="flex-grow:1;flex-basis:47%;margin-left:5px">
                    <span>Tax</span>
                    <div style="width:100%" class="validate-input" data-validate="Tax type is required">
                    <select class="select req-input" style="width: 100%;" id="tax_id" name="tax_id">  
                    <?php
                        foreach($taxData as $row){
                            $select = '';
                            if(isset($tax_id) && $tax_id != ''){
                                if($row['tax_id'] == $tax_id){
                                    $select = 'selected';
                                }
                            }
                        echo "<option $select data-tax='".$row['tax']."' value='".$row['tax_id']."'>".$row['tax_name']."</option>";
                        }
                    ?>
                    </select>
                    </div>
                    </div>
                    <div style="flex-grow:1;flex-basis:47%;margin-left:5px">
                    <span>Discount on All</span>
                        <div>
                            <input style="margin: 0;width:calc(100% - 50px)" class="total-disc number" type="text" name="discount_on_all_input" onkeyup="final_total()" id="discount_on_all_input" value="<?php print $discount_on_all_input ?? 0; ?>">
                            <select class="select" id="discount_on_all_type" style="width: 45px;" name="discount_on_all_type">
                            <?php 
                            $selectPer = '';
                            $selectRup = '';
                            if($discount_on_all_type === 'percentage'){$selectPer = 'selected';}
                            if($discount_on_all_type === 'rupee'){$selectRup = 'selected';}
                            ?>
                                <option <?=$selectPer?> value="percentage">%</option>
                                <option <?=$selectRup?> value="rupee">Rs.</option>
                            </select>
                        </div>
                    </div>
                    <div style="flex-grow:1;flex-basis:47%;margin-left:5px">
                    <span>Other Charges</span>
                        <div>
                            <input style="margin: 0;width:calc(100% - 50px)" class="number" type="text" name="other_charges_input" id="other_charges_input" onkeyup="final_total()" value="<?php print $other_charges_input ?? 0?>">
                            <select class="select" name="other_charges_type" style="width: 45px;" id="other_charges_type">
                            <?php
                            $selectPer = '';
                            $selectRup = '';
                            if($other_charges_type == 'percentage'){$selectPer = 'selected';}
                            if($other_charges_type == 'rupee'){$selectRup = 'selected';}
                            ?>
                                <option <?=$selectPer?> value="percentage">%</option>
                                <option <?=$selectRup?> value="rupee">Rs.</option>
                            </select>
                        </div>
                    </div>
                    </div>
                    <div class="payment_box">
                            <div class="payments_div">
                            <div class="box-body">
                                <div class="row"></div>
                                <div class="">
                                    <label for="payment_type">Payment Type</label>
                                    <div style="width:100%" class="validate-input" data-validate="Payment type is required">
                                    <select style="width: 100%;" class="select req-input" id='payment_type' name="payment_type">
                                    <?php
                                        foreach($paymenttypeData as $row){
                                            $select = '';
                                            if(isset($paymenttype_id) && $paymenttype_id != ''){
                                                if($row['paymenttype_id'] == $paymenttype_id){
                                                    $select = 'selected';
                                                }
                                            }
                                        echo "<option $select value='".$row['paymenttype_id']."'>".$row['paymenttype_name']."</option>";
                                        }
                                    ?>
                                    </select>
                                    </div>
                                </div>
                                <div class="amount_div">
                                        <label for="amount">Paid Amount</label>
                                        <div style="width:100%">
                                        <input onkeyup="calculate_balance()" type="text" style="width: 100%;" class="number" id="amount" name="amount" placeholder="" >
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="table-col" style="flex-basis: 400px;">
                    <table class="total-table" style="width: 100%;height:100%">
                        <tr>
                            <td><span>Sub Total</span></td>
                            <td>
                            <span name="sub_total" id="sub_total" class="sub-total">0.00</span>
                            </td>
                        </tr>
                        <tr>
                            <td><span>Discount on All</span></td>
                            <td>
                            <span id="discount_on_all_amt" name="discount_on_all_amt" class="total-discount">0.00</span>
                            </td>
                        </tr>
                        <tr>
                            <td><span>Other Charges</span></td>
                            <td>
                            <span id="other_charges_amt" name="other_charges_amt" class="total-charges">0.00</span>
                            </td>
                        </tr>
                        <tr>
                            <td><span>CGST <i class="tax_per"></i></span></td>
                            <td>
                                <span id="tax_amt_cgst" name="sgst_amt" class="">0.00</span>
                            </td>
                        </tr>
                        <tr>
                            <td><span>SGST <i class="tax_per"></i></span></td>
                            <td>
                            <span id="tax_amt_sgst" name="sgst_amt" class="">0.00</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="round_off">Round Off
                                <input type="checkbox" id="round_off" onclick="final_total()" checked>
                                </label>
                            </td>
                            <td>
                            <span id="round_off_amt" name="round_off_amt" class="">0.00</span>
                            </td>
                        </tr>
                        <tr>
                            <td><h2>Grand Total</h2></td>
                            <td>
                            <h2 name="grand_total" id="grand_total">0.00</h2>
                            </td>
                        </tr>
                        <tr>
                            <td><h4>Balance</h4></td>
                            <td>
                            <h4 id="balance_amt"></h4>
                            </td>
                        </tr>
                    </table>
                    </div>
                </div>
                <?php 
                        if(isset($id)){
                            $btn_name = 'Update';
                            $btn_id = 'update';
                        ?>
                        <input type='hidden' name='id' value='<?php print $id;?>'>
                        <?php 
                        }
                        else{
                            $btn_id='save';
                            $btn_name='Save';
                        }
                        ?>
                </div>

                <hr style="background-color: var(--border-color);">
                <br>
                <div class="btn-container">
                    <div class="btn"><a href="<?php echo PUBLIC_ROOT.'purchase' ?>" id='button'>Cancel</a></div>
                    <div class="btn"><input type="submit" name='submit' id="<?php echo $btn_id;?>" value="<?php echo $btn_name;?>"></div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include_once APP.'views/common/common_js.php'?>
<script src="<?php echo PUBLIC_ROOT.'Plugins/autocomplete/autocomplete.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'Plugins/jquery-ui/jquery-ui.min.js'?>"></script>
<script src="<?php echo PUBLIC_ROOT.'js/purchase.js'?>"></script>
    <script>
        function removerow(id){//id=Rowid
           
           $("#row_"+id).remove();
           final_total();
           }
          $(document).ready(function() {
              base_url = $('#baseURL').val();
            $("#supplier_id").select2({
                ajax:{
                    url: base_url+'/purchase/supplierAjax',
                    type: 'POST',
                    delay: 250,
                    data: function (data){
                        return {
                            searchTerm: data.term //search term
                        };
                    },

                    processResults: function(data){
                        return {
                            results : JSON.parse(data) ,
                        };
                    },
                    cache : true
                } 
            });  
            $(".select").select2();
        });
        $('.select').change(function(){
            final_total();
        })
    </script>
    <script>
         <?php if(isset($id)){ ?> 
             $(document).ready(function(){
                var base_url='<?=PUBLIC_ROOT?>';
                var purchase_id='<?= $purchase_id;?>';
                $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
                $.post(base_url+"purchase/returnPurchaseList/"+purchase_id,{},function(result){
                  //alert(result);
                  $('#purchase_table tbody').append(result);
                  $("#hidden_rowcount").val(parseFloat(<?=$items_count;?>)+1);
                  $(".overlay").remove();
                final_total();              
            }); 
             });
         <?php }?>
    </script>
    <script>    
      function calculate_balance(){
        amount= parseFloat($('#amount').val());
        grand_total = parseFloat($('#grand_total').html());
        tot = amount-grand_total;
        $('#balance_amt').html(parseFloat(tot).toFixed(2));
        if(tot<0){
            $('#balance_amt').css('color','crimson');
        }else{
            $('#balance_amt').css('color','green');
        }
     }

    /*********Calculate Amount ***********/
    function calculate_amount(i){

        let qty = $('#td_data_qty_'+i).val();
        let purchase_price = parseFloat($('#td_data_tot_unit_cost_'+i).val());
        //set purchase price
        $('#td_data_per_unit_price_'+i).val(purchase_price);

        /*Discount*/
        let discount_amt = 0;
        let discount_input = $('#td_data_discount_input_'+i).val().trim();
        let discount_type = $('#td_data_discount_type_'+i).val();

        let amt = parseFloat(qty) * purchase_price;
      
        if(discount_type === 'percentage'){
            discount_amt = (isNaN(parseFloat(discount_amt))) ? 0 : (parseFloat(discount_amt)*amt)/100;
        }else{
            discount_amt = (isNaN(parseFloat(discount_amt))) ? 0 : parseFloat(discount_amt);
        }

        let total_amt = amt-discount_amt;

        //set hiddent discount amt
        $('#td_data_discount_amt_'+i).val(discount_amt);
        //set rate price or unit price
        $('#td_data_total_cost_'+i).val(total_amt.toFixed(2));

        final_total();
    }

    /********FINAL TOTAL CALCULATION ***********/
    function final_total(){

        let rowcount = $('#hidden_rowcount').val();
        let subtotal = parseFloat(0);
        let other_charges_per_amt = parseFloat(0);
        let other_charges_total_amt = 0;
        let taxable = 0;
        let tax = $('#tax_id').find(':selected').attr('data-tax');


        let total_quantity = 0;
        let tax_amt = 0;

        // calculate subtotal
        for(i=1; i<=rowcount; i++){

            if($('#td_data_qty_'+i).val() != null && $('#td_data_qty_'+i).val() != ''){
                subtotal += parseFloat($('#td_data_total_cost_'+i).val());
            }

        }

        if((subtotal != null || subtotal != '') && (subtotal!=0)){

            $("#sub_total").html(subtotal.toFixed(2));

            let other_charges_input = $('#other_charges_input').val();
            if(other_charges_input != null && other_charges_input != ''){
                other_charges_type = $('option:selected', '#other_charges_type').val();

                if(other_charges_type == "percentage"){
                    other_charges_per_amt = (other_charges_input * subtotal)/100;
                }else{
                    other_charges_per_amt = other_charges_input;
                }

                taxable = parseFloat(other_charges_per_amt);
                other_charges_total_amt = parseFloat(other_charges_per_amt);
            }
            $('#other_charges_amt').html(parseFloat(other_charges_total_amt).toFixed(2));

            taxable=taxable+subtotal;
             
             //discount_on_all_amt
            var discount_on_all_input=parseFloat($("#discount_on_all_input").val());
            discount_on_all_input = isNaN(discount_on_all_input) ? 0 : discount_on_all_input;
            var discount=0;
            if(discount_on_all_input>0){
                var discount_type=$("#discount_on_all_type").val();
                if(discount_type=='rupee'){
                    taxable-=discount_on_all_input;
                    discount=discount_on_all_input;
                }
                else if(discount_type=='percentage'){
                    discount=(taxable*discount_on_all_input)/100;
                    taxable-=discount;
                }
            }
            else{
            //discount += $("#")
            }
            discount=parseFloat(discount).toFixed(2);
            $("#discount_on_all_amt").html(discount);  
            $("#hidden_discount_on_all_amt").val(discount);  

            //GST calculation
            per = tax/2.0;
            cgst = (taxable * per)/100;
            sgst = (taxable * per)/100;
            taxforsubtotal= cgst + sgst;

        
            subtotalwithtax = taxforsubtotal + taxable;
            console.log(subtotalwithtax);

            round_off_check = $('#round_off').is(':checked')
            if(round_off_check == true){         
                round_off =Math.round(subtotalwithtax)- parseFloat(subtotalwithtax);
                subtotal_round=Math.round(subtotalwithtax);
            }else{
                round_off = 0;
                subtotal_round=subtotalwithtax;
            }
            
            if(round_off<0){
            $('#round_off_amt').css('color','crimson');
            }else{
            $('#round_off_amt').css('color','green');
            }
            $(".tax_per").html(per+'%');
            $("#tax_amt_sgst").html(parseFloat(sgst).toFixed(2));
            $("#tax_amt_cgst").html(parseFloat(cgst).toFixed(2));
            $('#round_off_amt').html(parseFloat(round_off).toFixed(2));
            $("#grand_total").html(parseFloat(subtotal_round).toFixed(2));
            <?php if(!isset($id)){?>$("#amount").val(parseFloat(subtotal_round).toFixed(2));<?php }?>
            $("#hidden_total_amt").val(parseFloat(subtotal_round).toFixed(2)); 
            calculate_balance()
        }
        else{
            $("#sub_total").html('0.00'); 
            $("#grand_total").html('0.00'); 
            $("#discount_on_all_amt").html('0.00'); 
            $('#round_off_amt').html('0.00');
            $("#tax_amt_sgst").html('0.00');
            $("#tax_amt_cgst").html('0.00');
            $("#subtotal_amt").html('0.00'); 
            $("#other_charges_amt").html('0.00');  
        }
    }
    </script>