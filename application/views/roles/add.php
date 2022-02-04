<?php 
use app\core\Session;

if(!isset($id)){
    $role_name=$role_description='';
}
?> 

<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>Roles</h2>
        </div>
        <!-- FLASH MESSAGE START -->
            <?php include_once APP.'views/common/flashdata.php'?>
        <!-- FLASH MESSAGE END -->
        <div class="wrapper-container">
            <form  name='role-form' class="validate-form" id='role-form'>
                <div class="header">
                    <div class="item-pair">
                        <label for="role_name">Role Name <sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input" data-validate="Role name is required">
                        <input type="text"  class='req-input' name="role_name" style="width: 100%;" value="<?php echo $role_name; ?>"  id="role_name" autofocus>
                        </div>
                    </div>
                    <div class="item-pair" style="display: flex;align-items: center;">
                        <label for="role_description">Description</label>
                        <textarea style="width: 60%;border-radius: 5px;border:1px solid #ccc;margin-left: 3px;" id="role_description" name="role_description"><?php print $role_description; ?></textarea>
                    </div>     
                    
                    <div class="item-pair">
                        <input type="hidden" name="csrf_token" value="<?= Session::generateCsrfToken(); ?>" />
                    </div>

                    <table class="table">
                                      <thead class="bg-primary">
                                          <tr>
                                            <th>#</th>
                                            <th>Modules</th>
                                            <th>Select All</th>
                                            <th width="40%">Specific Permissions</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                        <?php $i=1; ?>
                                        <!-- USERS -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td>Users</td>
                                          <td>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="change_me" id="users" > Select All
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[users]" value="on">
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="users_all" id='users_add' name="permission[users_add]" >Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="users_all" id='users_edit' name="permission[users_edit]">Edit
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="users_all" id='users_delete' name="permission[users_delete]">Delete
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="users_all" id='users_view' name="permission[users_view]">View
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Roles -->

                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td>Roles</td>
                                          <td>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="change_me" id="roles" > Select All
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[roles]" value="on">
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="roles_all" id='roles_add' name="permission[roles_add]" >Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="roles_all" id='roles_edit' name="permission[roles_edit]">Edit
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="roles_all" id='roles_delete' name="permission[roles_delete]">Delete
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="roles_all" id='roles_view' name="permission[roles_view]">View
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- TAX -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td>Tax</td>
                                          <td>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="change_me" id="tax"> Select All
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[tax]" value="on">
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="tax_all" id='tax_add' name="permission[tax_add]">Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="tax_all" id='tax_edit' name="permission[tax_edit]">Edit
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="tax_all" id='tax_delete' name="permission[tax_delete]">Delete
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="tax_all" id='tax_view' name="permission[tax_view]">View
                                              </label></div>
                                          </td>
                                        </tr>
            
                                       <!-- UNITS -->
                                       <tr>
                                          <td><?= $i++;?></td>
                                          <td>Units</td>
                                          <td>
                                              <div class="checkbox"><label>
                                                <input type="checkbox" class="change_me" id="units"> Select All
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[units]" value="on">
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="units_all" id='units_add' name="permission[units_add]">Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="units_all" id='units_edit' name="permission[units_edit]">Edit
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="units_all" id='units_delete' name="permission[units_delete]">Delete
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="units_all" id='units_view' name="permission[units_view]">View
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- PAYMENT TYPES -->
                                       <tr>
                                          <td><?= $i++;?></td>
                                          <td>Payments Types</td>
                                          <td>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="change_me" id="payment_types"> Select All
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[payment_types]" value="on">
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="payment_types_all" id='payment_types_add' name="permission[payment_types_add]">Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="payment_types_all" id='payment_types_edit' name="permission[payment_types_edit]">Edit
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="payment_types_all" id='payment_types_delete' name="permission[payment_types_delete]">Delete
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="payment_types_all" id='payment_types_view' name="permission[payment_types_view]">View
                                              </label></div>
                                          </td>
                                        </tr>
  
                                        <!--SHOP INFO  -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td>Shop Info</td>
                                          <td>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="change_me" id="company"> Select All
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[company]" value="on">
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="company_all" id='company_edit' name="permission[company_edit]">Edit
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!--DASHBOARD  -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td>Dashboard</td>
                                          <td>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="change_me" id="dashboard"> Select All
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[dashboard]" value="on">
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="dashboard_all" id='dashboard_view' name="permission[dashboard_view]">View Dashboard
                                              </label></div>
                                          </td>
                                        </tr>
                                       
                                        <!-- PRODUCTS -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td>Products</td>
                                          <td>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="change_me" id="items" > Select All
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[items]" value="on">
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="items_all" id='items_add' name="permission[items_add]" >Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="items_all" id='items_edit' name="permission[items_edit]">Edit
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="items_all" id='items_delete' name="permission[items_delete]">Delete
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="items_all" id='items_view' name="permission[items_view]">View
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="items_all" id='items_category_add' name="permission[items_category_add]" > Category Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="items_all" id='items_category_edit' name="permission[items_category_edit]"> category Edit
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="items_all" id='items_category_delete' name="permission[items_category_delete]"> Category Delete
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="items_all" id='items_category_view' name="permission[items_category_view]"> Category View
                                              </label></div>
                                          </td>
                                        </tr>
                            
                                        <!-- Suppliers -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td>Suppliers</td>
                                          <td>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="change_me" id="suppliers" > Select All
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[suppliers]" value="on">
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="suppliers_all" id='suppliers_add' name="permission[suppliers_add]" >Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="suppliers_all" id='suppliers_edit' name="permission[suppliers_edit]">Edit
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="suppliers_all" id='suppliers_delete' name="permission[suppliers_delete]">Delete
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="suppliers_all" id='suppliers_view' name="permission[suppliers_view]">View
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Customers -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td>Customers</td>
                                          <td>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="change_me" id="customers" > Select All
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[customers]" value="on">
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="customers_all" id='customers_add' name="permission[customers_add]" >Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="customers_all" id='customers_edit' name="permission[customers_edit]">Edit
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="customers_all" id='customers_delete' name="permission[customers_delete]">Delete
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="customers_all" id='customers_view' name="permission[customers_view]">View
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Purchase -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td>Purchase</td>
                                          <td>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="change_me" id="purchase" > Select All
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[purchase]" value="on">
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_add' name="permission[purchase_add]" >Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_edit' name="permission[purchase_edit]">Edit
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_delete' name="permission[purchase_delete]">Delete
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_view' name="permission[purchase_view]">View
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_payment_view' name="permission[purchase_payment_view]"> Purchase Payments View
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_payment_add' name="permission[purchase_payment_add]"> Purchase Payments Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_payment_delete' name="permission[purchase_payment_delete]"> Purchase Payments Delete
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Purchase Return-->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td>Purchase Return</td>
                                          <td>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="change_me" id="purchase_return" > Select All
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[purchase_return]" value="on">
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="purchase_return_all" id='purchase_return_add' name="permission[purchase_return_add]" >Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="purchase_return_all" id='purchase_return_edit' name="permission[purchase_return_edit]">Edit
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="purchase_return_all" id='purchase_delete' name="permission[purchase_return_delete]">Delete
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="purchase_return_all" id='purchase_view' name="permission[purchase_return_view]">View
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="purchase_return_all" id='purchase_return_payment_view' name="permission[purchase_return_payment_view]"> Purchase Return Payments View
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="purchase_return_all" id='purchase_return_payment_add' name="permission[purchase_return_payment_add]"> Purchase Return Payments Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="purchase_return_all" id='purchase_return_payment_delete' name="permission[purchase_return_payment_delete]"> Purchase Return Payments Delete
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Sales -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td>Sales</td>
                                          <td>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="change_me" id="sales" > Select All
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[sales]" value="on">
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="sales_all" id='sales_add' name="permission[sales_add]" >Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="sales_all" id='sales_edit' name="permission[sales_edit]">Edit
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="sales_all" id='sales_delete' name="permission[sales_delete]">Delete
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="sales_all" id='sales_view' name="permission[sales_view]">View
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="sales_all" id='sales_payment_view' name="permission[sales_payment_view]"> Sales Payments View
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="sales_all" id='sales_payment_add' name="permission[sales_payment_add]"> Sales Payments Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="sales_all" id='sales_payment_delete' name="permission[sales_payment_delete]"> Sales Payments Delete
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Sales Return-->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td>Sales Return</td>
                                          <td>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="change_me" id="sales_return" > Select All
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[sales_return]" value="on">
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="sales_return_all" id='sales_return_add' name="permission[sales_return_add]" >Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="sales_return_all" id='sales_return_edit' name="permission[sales_return_edit]">Edit
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="sales_return_all" id='sales_delete' name="permission[sales_return_delete]">Delete
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="sales_return_all" id='sales_view' name="permission[sales_return_view]">View
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="sales_return_all" id='sales_return_payment_view' name="permission[sales_return_payment_view]"> Sales Return Payments View
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="sales_return_all" id='sales_return_payment_add' name="permission[sales_return_payment_add]"> Sales Return Payments Add
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="sales_return_all" id='sales_return_payment_delete' name="permission[sales_return_payment_delete]"> Sales Return Payments Delete
                                              </label></div>
                                          </td>
                                        </tr>
                                     
                                        <!-- Reports -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td>Reports</td>
                                          <td>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="change_me" id="reports" > Select All
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[reports]" value="on">
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="reports_all" id='sales_report' name="permission[sales_report]" > Sales Report
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="reports_all" id='sales_return_report' name="permission[sales_return_report]"> Sales Return Report
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="reports_all" id='purchase_report' name="permission[purchase_report]"> Purchase Report
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="reports_all" id='purchase_return_report' name="permission[purchase_return_report]"> Purchase Return Report
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="reports_all" id='stock_report' name="permission[stock_report]"> Stock Report
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="reports_all" id='item_sales_report' name="permission[item_sales_report]"> Product Sales Report
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="reports_all" id='purchase_payments_report' name="permission[purchase_payments_report]"> Purchase Payments Report
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="reports_all" id='sales_payments_report' name="permission[sales_payments_report]"> Sales Payments Report
                                              </label></div>
                                              <div class="checkbox "><label>
                                                <input type="checkbox" class="reports_all" id='expired_items_report' name="permission[expired_items_report]"> Expired Products Report
                                              </label></div>
                                          </td>
                                        </tr>
                                      </tbody>
                                      
                                    </table>
                    <?php 
                        if($role_name != ""){
                            $btn_name = 'Update';
                            $btn_id = 'update';
                    ?>
                        <input type='hidden' name='id' value='<?php echo $id;?>'>
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
                   <div class="btn"><a href="<?php echo PUBLIC_ROOT.'roles' ?>" id='button'>Cancel</a></div>
                    <div class="btn"><input type="submit" name='submit' id="<?php echo $btn_id;?>" value="<?php echo $btn_name;?>"></div>
                </div>
            </form>
            <input type="hidden" id="baseURL" value="<?php echo PUBLIC_ROOT; ?>">
            <input type="hidden" id="csrf_token" value="<?= Session::generateCsrfToken(); ?>" />
        </div>
    </div>
</section>
 <?php include_once APP.'views/common/common_js.php'?> 

<script src="<?php echo PUBLIC_ROOT.'js/roles.js'?>"></script>
   <!-- SELECT THE CHECKBOX'S -->
   <script type="text/javascript">
        <?php 
        $str='';
        if(isset($id) && !empty($id)){
          if($permissions[0]>0){
            foreach ($permissions[1] as $res1) {
              if(empty($str)){
                $str=' #'.$res1['permission'];   
              }
              else{
                $str=$str.', #'.$res1['permission'];
              }
          } 
        }
      }
        ?>
        $('<?php echo $str;?>').prop("checked",true);

      </script>
