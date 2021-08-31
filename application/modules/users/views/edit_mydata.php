<style>
.validation{
    color: red;
}
</style>
<div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
        <li class='active'><?php echo lang('edit_mydata');?></li>
      </ul>
    </div>
  </div>
</div>

<main>
  <div class="container">

    <div class="row">
      <?php $this->load->view('site/user_menu');?>
      <div class="col-md-8">
        <div class="edit-my-data">

          <form action="" method="post" enctype="multipart/form-data">
            <div class="setting-container mt-0">
              <h4><?php echo lang('my_personal');?> - <?php echo $this->data['customer_group_name'];?> </h4>
              <?php if($this->session->flashdata('message')){?>
                  <div class="success-alert"><?php echo $this->session->flashdata('message');?></div><!--success_message-->
              <?php }?>
              <?php if($this->session->flashdata('error')){?>
                  <div class="error-messege"><?php echo $this->session->flashdata('error');?></div><!--fail_message-->
              <?php }?>

              <div class="form-group">
                <label for="username"><?php echo lang('first_name');?></label>
                <?php
                $username_att = array('name'=>'username', 'type'=>'text', 'id'=>'username', 'placeholder'=>lang('username'), 'class'=>'form-control','value'=> isset($user->first_name)? $user->first_name : set_value('username'));
                echo form_input($username_att);
               ?>
               <p><?php echo form_error('username');?></p>
              </div>

              <div class="form-group">
                <label for="namelast"><?php echo lang('last_name');?></label>
                <?php
                  $l_name_att = array('name'=>'last_name', 'type'=>'text', 'id'=>'namelast', 'placeholder'=>lang('last_name'), 'class'=>'form-control', 'value'=> isset($user->last_name)? $user->last_name : set_value('last_name'));
                  echo form_input($l_name_att);
                 ?>
                 <p><?php echo form_error('last_name');?></p>
              </div>

              <div class="form-group">
                <label><?php echo lang('email');?></label>
                <?php
                  $email_att = array('name'=>'email', 'type'=>'email', 'id'=>'email_lab', 'placeholder'=>lang('email'), 'class'=>'form-control', 'value'=> isset($user->email)? $user->email : set_value('email'), 'readonly'=>'readonly');
                  echo form_input($email_att);
                ?>
                <p><?php echo form_error('email');?></p>
              </div>

              <div class="form-group">
                <label><?php echo lang('phone');?> (<?php echo lang('phone_ex');?>)</label>
                <?php
                  $phone_att = array('name'=>'phone', 'id'=>'tel', 'placeholder'=>'', 'class'=>'form-control', 'value'=> isset($user_phone)? $user_phone : set_value('phone'), 'style'=>'100%');
                  echo form_input($phone_att);
                ?>
                <p><?php echo form_error('phone');?></p>
              </div>

              <div class="form-group">
                <label><?php echo lang('country');?></label>
                <select id="user_nationality" class="form-control" name="country_id">
                    <?php foreach($user_countries as $country_id => $country){
                            $selected = '';
                            if($country_id == $user->Country_ID)
                            {
                                $selected   = 'selected';
                            }
                    ?>
                        <option value="<?php echo $country_id;?>" <?php echo $selected; ?>><?php echo $country;?></option>
                    <?php }?>
                </select>
                <p><?php echo form_error('country_id');?></p>
              </div>

            </div>
            <div class="setting-container">
              <h4><?php echo lang('your_password');?></h4>
              <div class="form-group">
                <label><?php echo lang('password');?></label>
                <?php
                  $password_att = array('name'=>'password', 'type'=>'password', 'id'=>'password', 'placeholder'=>'', 'class'=>'form-control');
                  echo form_password($password_att);
                ?>
                <p><?php echo form_error('password');?></p>
              </div>

              <div class="form-group">
                <label><?php echo lang('confirm_password');?></label>
                <?php
                  $conf_password_att = array('name'=>'conf_password', 'type'=>'password', 'id'=>'conf_password', 'placeholder'=>'', 'class'=>'form-control');
                  echo form_password($conf_password_att);
                ?>
                <p><?php echo form_error('conf_password');?></p>
              </div>

            </div>
            <div class="setting-container">
              <h4><?php echo lang('mail_list');?></h4>
              <p class="mb-1"><?php echo lang('join_mail_list_inquiry');?></p>

              <?php   $mail_checked = '';
              $mail_notchecked = '';

              if($user->mail_list == 1)
              {
                 $mail_checked = 'checked == checked';

              }else{

                 $mail_notchecked = 'checked == checked';
              }
              ?>

              <div class="form-group">
                <input type="radio" class="ml-5px mr-5px" name="mail_list" value="1" <?php echo $mail_checked;?>/>
                <label><?php echo lang('yes');?></label>
              </div>
              <div class="form-group">
                <input type="radio" class="ml-5px mr-5px" name="mail_list" value="0" <?php echo $mail_notchecked;?> />
                <label><?php echo lang('no');?></label>
              </div>
            </div>

            <?php if($this->config->item('allow_user_auth')){?>
              <div class="setting-container">

                <h4><?php echo lang('tow_way_auth');?> </h4>
                <div class="form-group">
                  <input type="radio" class="ml-5px mr-5px" name="login_auth" value="1" <?php echo ($user->login_auth == 1)? 'checked' : false ;?> />
                  <label><?php echo lang('sms');?></label>
                </div>

                <div class="form-group">
                  <input type="radio" class="ml-5px mr-5px" name="login_auth" value="2" <?php echo ($user->login_auth == 2)? 'checked' : false ;?> />
                  <label><?php echo lang('way_auth_google'); ?></label>
                </div>

                <div class="form-group">
                  <input type="radio" class="ml-5px mr-5px" name="login_auth" value="0" <?php echo ($user->login_auth == 0)? 'checked' : false ;?> />
                  <label><?php echo lang('disable'); ?></label>
                </div>

              </div>
            <?php }?>

            <?php /*<div class="setting-container">

              <h4><?php echo lang('your_bank_accounts');?></h4>
              <?php foreach($user_bank_accounts as $account) { ?>
                <div class="form-group">
                  <p class="bold"><?php echo $account->bank;?></p>
                  <label><?php echo lang('account_name');?></label>
                  <input type="text" name="account_name[]"  class="form-control" placeholder="<?php echo $account->account_name ;?>"  value ="<?php echo $account->account_name;?>"/>
                </div>

                <div class="form-group">
                  <label><?php echo lang('account_number');?></label>

                  <input type="text" name="account_number[]"  class="form-control" placeholder="<?php echo $account->account_number ;?>" value ="<?php echo $account->account_number;?>"/>
                  <input type="hidden" name="bank_id[]" value="<?php echo $account->bank_account_id;?> " />
                </div>
              <?php }?>

            </div>
            */?>
            <button class=""><?php echo lang('edit');?></button>

          </form>
        </div>
      </div>
    </div>
  </div>
</main>
