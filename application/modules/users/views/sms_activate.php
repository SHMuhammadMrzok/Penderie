<div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
        <li class='active'><?php echo lang('sms_active_code');?></li>
      </ul>
    </div>
  </div>
</div>
<main>
   <div class="container">
      <div class="row">
         <div class="col-md-6 m-auto">
             <div class="verifcation-page">
                 <h2><?php echo lang('sms_active_code');?></h2>
                 <p><?php echo lang('sms_activation_code_msg');?></p>
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

                    <div class="d-flex inputs-area">

                      <?php
                       $code_data = array('name'=>'sms_code4',
                                          'maxlength' => 1,
                                          'class'=>'form-control'
                                        );
                       echo form_input($code_data);

                       $code_data = array('name'=>'sms_code3',
                                          'maxlength' => 1,
                                          'class'=>'form-control'
                                        );
                       echo form_input($code_data);

                       $code_data = array('name'=>'sms_code2',
                                          'maxlength' => 1,
                                          'class'=>'form-control'
                                        );
                       echo form_input($code_data);

                       $code_data = array('name'=>'sms_code1',
                                          'maxlength' => 1,
                                          'class'=>'form-control'
                                        );
                       echo form_input($code_data);
                      ?>
                      <p class="error-alert"><?php echo form_error('sms_code1');?></p>

                     <?php /*<input type="text" placeholder=""/>
                     <input type="text" placeholder=""/>
                     <input type="text" placeholder=""/>
                     <input type="text" placeholder=""/>
                     */?>
                    </div>

                     <button class=" mt-3"><?php echo lang('send');?></button>
                  </form>
                  <?php if(! isset($auth)){?>
                      <a href="<?php echo base_url();?>users/users/resend_sms_code"><?php echo lang('resend_activation_code');?></a>
                  <?php }?>
             </div>
         </div>
      </div>
   </div>
</main>
