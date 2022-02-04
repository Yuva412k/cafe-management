<?php 
use app\core\Session;
if(isset($result)){
    extract($result);
}
if(!isset($id)){
    $email=$name=$role_id='';
}
?>

<section class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <h2>User</h2>
        </div>
        <!-- FLASH MESSAGE START -->
            <?php include_once APP.'views/common/flashdata.php'?>
        <!-- FLASH MESSAGE END -->
        <div class="wrapper-container">
            <form  name='users-form' class="validate-form" id='users-form' method="post" action="<?php echo PUBLIC_ROOT.'users/addUser'?>">
                <div class="header">
                <div class="item-pair">
                        <label for="name">Name<sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="name is required">
                        <input type="text" class="req-input" name="name" style="width: 100%;"  value="<?php echo $name;?>" id="name" autofocus>
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="role_id">Role<sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="role is required">
                        <select id='role' class="req-input" style="width: 100%;" name="role_id">
                        <option value="">--Select Role--</option>
                                <?php 
                                    if(isset($usersData) && !empty($usersData)){
                                        foreach($usersData as $data){
                                            $selected = ($data['id'] == $role_id) ? 'selected' : '';
                                            echo '<option '.$selected.' value="'.$data['id'].'">'.$data['role_name'].'</option>';
                                        }
                                    }
                                ?>
                        </select>
                    </div>
                    </div>
                    <div class="item-pair">
                        <label for="email">Username<sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="username is required">
                        <input type="text" class="req-input" name="email" style="width: 100%;"  value="<?php echo $email;?>" id="email" autofocus>
                        </div>
                    </div>
                   
                    <div class="item-pair">
                        <label for="password">Password<sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input"  data-validate="password is required">
                        <input type="password" class="req-input" name="password" style="width: 100%;"  value="" id="password">
                        </div>
                    </div>
                    <div class="item-pair">
                        <label for="cpassword">Confirm Password<sup style="color: red">*</sup></label>
                        <div style="width:60%" class="validate-input" data-validate="password is required">
                        <input type="password" name="cpassword" class="req-input" style="width: 100%;"  value="" id="cpassword" >
                        </div>
                    </div>
                  

                    <div class="item-pair">
                        <input type="hidden" id="baseURL" value="<?php echo PUBLIC_ROOT; ?>">
                        <input type="hidden" name="csrf_token" value="<?= Session::generateCsrfToken(); ?>" />
                    </div>
                </div>

                <?php 
                        if(isset($id)){
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
                    <div class="btn"><a href="<?php echo PUBLIC_ROOT.'users' ?>" id='button'>Cancel</a></div>
                    <div class="btn"><input type="submit" name='submit' id="<?php echo $btn_id;?>" value="<?php echo $btn_name;?>"></div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include_once APP.'views/common/common_js.php'?>

<script src="<?php echo PUBLIC_ROOT.'js/users.js'?>"></script>

    <script>
        $(document).ready(function() {
            $("#role").select2({
                placeholder: "Select User"
            });  
        });
        
    </script>