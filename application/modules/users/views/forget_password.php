<section class="forget-pass" style="background:url(<?php echo base_url().'assets/uploads/'.$this->config->item('forget_password_background');?>) center center no-repeat;">
  <div class="container">
    <div class="row">
      <div class="col-md-5 mx-auto">
        <div class="login-container">
          <form method="post" action="<?php echo base_url();?>users/users/forgot_password" class="w-100">
            <div class="form-logo">
              <a href="<?php echo base_url();?>" title="<?php echo lang('home');?>">
                <img src="<?php echo base_url().'assets/uploads/'.$this->config->item('logo');?>" alt="<?php echo lang('home');?>" />
              </a>
              <span><?php echo lang('change_password');?></span>
              <?php if($this->session->flashdata('message')){?>
                  <div class="success-alert"><?php echo $this->session->flashdata('message');?></div><!--success_message-->
              <?php }?>
              <?php
              //echo '<pre>'; print_r($this->session->flashdata());die();
              if($this->session->flashdata('error_message')){?>
                  <div class="error-messege"><?php echo $this->session->flashdata('error_message');?></div><!--fail_message-->
              <?php }?>
            </div>

            <div class="signup-link">
              <p><a href="<?php echo base_url();?>User_login"> <?php echo lang('Back').' '.lang('to').' '.lang('login');?></a></p>
            </div>
            <div class="form-group">
              <label for="username"><?php echo lang('email');?></label>
              <?php
                $email_att = array('name'=>'email', 'id'=>'email_lab', 'placeholder'=>lang('email'), 'class'=>'form-control', 'value'=>set_value('email'), 'autofocus'=>true);
                echo form_input($email_att);
              ?>
            </div>

            <div class="form-group">
              <button class="button"><?php echo lang('reset');?></button>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>
</section>
