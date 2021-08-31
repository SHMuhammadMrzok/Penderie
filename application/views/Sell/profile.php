    <?php if(isset($error_msg)){?>
        <span class="error"><?php echo $error_msg;?></span>
    <?php }else{?>
        <span class="error"><?php if(isset($validation_msg)) echo $validation_msg;?></span>
    <?php
    }    
        //$att=array('class'=> 'form-horizontal form-bordered');
        //echo form_open_multipart($form_action, $att);
    ?>
    <form action="<?php echo base_url();?>sell/edit_my_data" method="post" class="form-horizontal form-bordered" enctype="multipart/form-data">
    
        <div class="right-content">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <div class="form-item">
                            <label><?php echo lang('email');?></label>
                                <?php
                                  $email_att = array('name'=>'email',
                                                     'id'=>'email_lab', 
                                                     'placeholder'=>lang('email'), 
                                                     'class'=>'form-control', 
                                                     'value'=> isset($user->email)? $user->email : set_value('email'), 'readonly'=>'readonly');
                                  echo form_input($email_att);
                                ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-group">
                        <div class="form-item">
                            <label><?php echo lang('first_name');?></label>
                                <?php
                                  $first_name_att = array(
                                                    'name'  => 'first_name', 
                                                    'id'    => 'first_name', 
                                                    'placeholder'=>lang('first_name'), 
                                                    'class' => 'form-control', 
                                                    'value' => isset($user->first_name)? $user->first_name : set_value('first_name'), 
                                                    );
                                  echo form_input($first_name_att); 
                                ?>                                             
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-group">
                        <div class="form-item">
                            <label><?php echo lang('last_name');?></label>
                                <?php
                                  $last_name_att = array(
                                                    'name'  => 'last_name', 
                                                    'id'    => 'last_name', 
                                                    'placeholder'=>lang('last_name'), 
                                                    'class' => 'form-control', 
                                                    'value' => isset($user->last_name)? $user->last_name : set_value('last_name'), 
                                                    );
                                  echo form_input($last_name_att); 
                                ?>                                             
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-group">
                        <div class="form-item">
                            <label><?php echo lang('phone');?></label>
                                <?php
                                  $phone_att = array(
                                                    'name'  =>'phone', 
                                                    'id'    =>'tel', 
                                                    'placeholder'=>lang('phone_ex'), 
                                                    'class' =>'form-control', 
                                                    'value'=> isset($user_phone)? $user_phone : set_value('phone'), 
                                                    'style'=>'100%');
                                  echo form_input($phone_att); 
                                ?>                                             
                        </div>
                    </div>
                </div>
                
                
                <div class="col-12">
                    <div class="form-group">
                        <div class="form-item">
                            <label><?php echo lang('password');?></label>
                                <?php
                                  $password_att = array('name'=>'password', 
                                                        'id'=>'password', 
                                                        'placeholder'=>'', 
                                                        'class'=>'form-control');
                                  echo form_password($password_att); 
                                ?>                                          
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-group">
                        <div class="form-item">
                            <label><?php echo lang('confirm_password');?></label>
                                <?php 
                                  $conf_password_att = array(
                                                            'name'=>'conf_password', 
                                                            'id'=>'conf_password', 
                                                            'placeholder'=>'', 
                                                            'class'=>'form-control');
                                  echo form_password($conf_password_att);
                                ?>                                           
                        </div>
                    </div>
                </div>
                            
                <div class="form-group">
                    <div class="row no-gutters align-items-left">
                        <div class="col-md-12">
                            <button class="button"><?php echo lang('save');?></button>
                        </div>
                    </div>
                </div>
                                        
            </div>
        </div>
    
    </form>