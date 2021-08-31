<div class="logo text-center">
    <a href="#"><img src="<?php echo base_url();?>assets/template/site/img/logo.png" class="img-responsive" alt="logo"/></a>
</div><!--logo-->

<?php if(isset($error_msg)){?>
    <span class="error"><?php echo $error_msg;?></span>
<?php }else{?>
    <span class="error"><?php if(isset($validation_msg)) echo $validation_msg;?></span>
<?php
}    
    //$att=array('class'=> 'form-horizontal form-bordered');
    //echo form_open_multipart($form_action, $att);
?>
<?php if(isset($auth)){?>
    <form method="post" action="<?php echo base_url();?>auth/activate_user_phone/<?php echo $user_id;?>">
<?php }else{?>
    <form method="post" action="">
<?php }?>

<?php if($this->session->flashdata('message')){?>   
    <div class="success-alert"><?php echo $this->session->flashdata('message');?></div><!--success_message-->
<?php }?>
<?php if($this->session->flashdata('error')){?>
    <div class="error-messege"><?php echo $this->session->flashdata('error');?></div><!--fail_message-->
<?php }?>

    <div class="right-content">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('sms_code');?></label>
                            <?php
                             $code_data = array(
                                                'name'=>'sms_code', 
                                                'placeholder'=>lang('sms_code'), 
                                                'class'=>'form-control');
                             echo form_input($code_data);
                            ?>
                            <p class="error-alert"><?php echo form_error('sms_code');?></p>
                    </div>
                </div>
            </div>
            
                 
            <div class="form-group">
                <div class="row no-gutters align-items-left">
                    <div class="col-md-12">
                        <button class="button"><?php echo lang('send');?></button>
                    </div>
                </div>
            </div>
                                    
        </div>
    </div>

</form>

<div class="row-form">
    <?php if(! isset($auth)){?>
        <a href="<?php echo base_url();?>users/users/resend_sms_code/1"><?php echo lang('resend_activation_code');?></a>
    <?php }?>
</div><!--row-form-->