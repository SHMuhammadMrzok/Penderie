<style>
.error-alert{
  color: red;
}
</style>
<section class="registration-page" style="background: url(<?php echo $images_path.$this->config->item('register_background');?>) center center no-repeat;">
  <div class="container">
    <div class="row">
      <div class="col-md-5 mx-auto">
        <div class="login-container">
          <form action="<?php echo base_url();?>Register"  method="post" enctype="multipart/form-data" class="w-100" id="register_form">
            <div class="form-logo">
              <a href="<?php echo base_url();?>"  title="<?php echo lang('home');?>">
                <img src="<?php echo $images_path.$this->config->item('logo');?>" alt="<?php echo $this->config->item('site_name');?>" />
              </a>
              <span><?php echo lang('register');?></span>
            </div>

            <div class="signup-link">
              <p><?php echo lang('already_have_account');?>
                  <a href="<?php echo base_url();?>User_login"><?php echo lang('login');?></a>
              </p>

              <?php if($this->session->flashdata('message')){?>
                  <div class="success-alert"><?php echo $this->session->flashdata('message');?></div><!--success_message-->
              <?php }?>
              <?php if($this->session->flashdata('error')){?>
                  <div class="error-messege"><?php echo $this->session->flashdata('error');?></div><!--fail_message-->
              <?php }?>

            </div>

            <div class="form-group">
              <label for="email"><?php echo lang('first_name');?></label>
              <?php
              $first_name_att = array(
                'name'        => 'first_name',
                'id'          => 'first_name',
                //'placeholder' => 'example@example.example',
                'class'       => 'form-control',
                'value'       => set_value('first_name'),
                'autofocus'   => true
              );
              echo form_input($first_name_att);
              ?>
              <p class="error-alert"><?php echo form_error('first_name');?></p>
            </div>

            <div class="form-group">
              <label for="email"><?php echo lang('email');?></label>
              <?php
              $email_att = array(
                'name'        => 'email',
                'id'          => 'email',
                'placeholder' => 'example@example.example',
                'class'       => 'form-control',
                'value'       => set_value('email'),
                'autofocus'   => true
              );
              echo form_input($email_att);
              ?>
              <p class="error-alert"><?php echo form_error('email');?></p>
            </div>

            <div class="form-group">
                <label for="country"><?php echo lang('country');?></label>
                <?php
                  echo form_dropdown('country_id', $user_countries, set_value('country_id'), 'class="form-control select2" id="country_id"');
                ?>
                <p class="error-alert"><?php echo form_error('country_id');?></p>
              </div>

            <div class="form-group">
                <label for="phone"><?php echo lang('phone');?></label>
                <input type="text" value="" style="display: none;" class="form-control country_code" readonly />
                <?php
                  $phone_att = array(
                    'name'        => 'phone',
                    'id'          => 'phone',
                    'placeholder' => lang('phone_ex'),
                    'value'       => set_value('phone'),
                    'class'       => 'form-control'
                  );
                  echo form_input($phone_att);
                ?>
                <p class="error-alert"><?php echo form_error('phone');?></p>
              </div>

            <div class="form-group">
              <label for="password"><?php echo lang('password');?></label>
              <span class="show-password">
                <div class="svg-eye">
                  <svg>
                    <use xlink:href="#show-password"></use>
                  </svg>
                </div>

                <div class="svg-uneye">
                  <svg>
                    <use xlink:href="#un-show-password"></use>
                  </svg>
                </div>
              </span>
              <?php
                $password_att = array('name'=>'password', 'id'=>'password', 'placeholder'=>lang('password'), 'class'=>'form-control');
                echo form_password($password_att);
              ?>
              <p class="error-alert"><?php echo form_error('password');?></p>
            </div>

            <div class="form-group">
              <label for="password"><?php echo lang('confirm_password');?></label>
              <span class="show-password">
                <div class="svg-eye">
                  <svg>
                    <use xlink:href="#show-password"></use>
                  </svg>
                </div>

                <div class="svg-uneye">
                  <svg>
                    <use xlink:href="#un-show-password"></use>
                  </svg>
                </div>
              </span>
              <?php
              $conf_password_att = array('name'=>'conf_password', 'id'=>'conf_password', 'placeholder'=>lang('confirm_password'), 'class'=>'form-control');
              echo form_password($conf_password_att);
              ?>
              <p class="error-alert"><?php echo form_error('confirm_password');?></p>
            </div>


            <div class="form-group">
              <div class="checkbox">

                <p class="agree">
                  <input type="checkbox" value="1"  name="terms_conditions" required />
                  <?php echo lang('agree_by_logging');?>
                  <a target="_blank" href="<?php echo base_url();?>Page_Details/3"><?php echo lang('privacy_policy');?> </a> -
                  <a target="_blank" href="<?php echo base_url();?>Page_Details/4"><?php echo lang('terms_conditions');?></a>
                </p>
              </div>

              <p class="error-alert"><?php echo form_error('terms_conditions');?></p>

            </div>

            <div class="form-group">
              <?php /* <button class="button" formaction="<?php echo base_url();?>Register"><?php echo lang('register');?></button> */ ?>
              <button class="g-recaptcha button" formaction="<?php echo base_url();?>Register" data-sitekey="<?php echo $reCAPTCHA_site_key; ?>" data-callback='onSubmit' data-action='submit'><?php echo lang('register');?></button>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>
</section>

<script>

//on page load
$( document ).ready (function(){
 if($( "#country_id option:selected" ).val() != 0)
 {
   postData = {country_id : $( "#country_id option:selected" ).val()}
   $.post('<?php echo base_url().'users/register/get_country_call_code';?>', postData, function(result){
       $('.country_code').val(result);
       $('.country_code').show();

  }, 'json');
 }
});
// on change country_id input
$('body').on("change", '#country_id', function(){
 postData = {country_id : $( "#country_id option:selected" ).val()}

 $.post('<?php echo base_url().'users/register/get_country_call_code';?>', postData, function(result){
     $('.country_code').val(result);
     $('.country_code').show();

}, 'json');
});
</script>

<script>
   function onSubmit(token) {
     document.getElementById("register_form").submit();
   }
 </script>
