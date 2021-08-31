<section class="forget-pass" style="background:url(<?php echo base_url().'assets/uploads/'.$this->config->item('forget_password_background');?>) center center no-repeat;">
  <div class="container">
    <div class="row">
      <div class="col-md-5 ml-auto">
        <div class="login-container">

            <?php echo form_open('users/users/reset_password/' . $code);?>
            <div class="form-logo">
              <a href="<?php echo base_url();?>" title="<?php echo lang('home');?>">
                <img src="<?php echo base_url().'assets/uploads/'.$this->config->item('logo');?>" alt="<?php echo lang('home');?>" />
              </a>
              <span><?php echo lang('reset_password_heading');?></span>
              <?php if($this->session->flashdata('message')){?>
                  <div class="success-alert"><?php echo $this->session->flashdata('message');?></div><!--success_message-->
              <?php }?>
              <?php if($this->session->flashdata('error')){?>
                  <div class="error-messege"><?php echo $this->session->flashdata('error');?></div><!--fail_message-->
              <?php }?>


            </div>

            <div class="signup-link">
              <p><a href="<?php echo base_url();?>User_login"> <?php echo lang('Back').' '.lang('to').' '.lang('login');?></a></p>
            </div>
            <div class="form-group">
              <label for="username"><?php echo sprintf(lang('reset_password_new_password_label'), $min_password_length);?><span class="required">*</span></label>

              <?php echo form_input($new_password);?>
              <p class="error-alert"><?php echo form_error('new_password');?></p>

            </div>

            <div class="form-group">
              <label for="username"><?php echo lang('reset_password_new_password_confirm_label', 'new_password_confirm');?> <span class="required">*</span></label>
              <?php echo form_input($new_password_confirm);?>
              <p class="error-alert"><?php echo form_error('new_password_confirm');?></p>
            </div>

            <?php echo form_input($user_id);?>
            <?php echo form_hidden($csrf); ?>

            <div class="form-group">
              <button class="button"><?php echo lang('reset_password_submit_btn');?></button>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>
</section>
