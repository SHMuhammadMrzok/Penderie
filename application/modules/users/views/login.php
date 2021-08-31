<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
  <div class="row no-margin">
    	<div class="iner_page">
      	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 login_center">
          <div class="login_area">
             <h1><?php echo lang('login');?></h1>
                <form method="post" action="<?php echo base_url();?>users/users/login">
                         <?php  if($this->session->flashdata('user_login_message'))   { ?>
                            <div class="fail_message"><?php echo $this->session->flashdata('user_login_message');?></div>
                	   <?php } ?>

                    	<div class="row no-margin margin-bottom-10px">
                        	<input type="email" name="email" placeholder="<?php echo lang('email');?>" class="form-control"/>
                        </div><!--row-->

                        <div class="row no-margin margin-bottom-10px">
                        	<input type="password" name="password" placeholder="<?php echo lang('password');?>" class="form-control"/>
                        </div><!--row-->

                        <div class="row no-margin margin-bottom-10px">
                        	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                            	<input id="check" type="checkbox"/>
                                <label for="check"><?php echo lang('remember');?></label>
                            </div><!--col-->

                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding text-left">
                            	<a href="<?php echo base_url();?>users/users/forget"><?php echo lang('forget');?></a>
                            </div><!--col-->

                        </div><!--row-->

                        <div class="row no-margin margin-bottom-10px">

                        	<button class="btn bg-primary"><?php echo lang('login');?></button>
                            <a href="<?php echo base_url().'Register';?>" class="link_new_account"><?php echo lang('sign_up');?></a>
                        </div><!--row-->
                  </form>

                </div><!--login_area-->

            </div><!--col-->

        </div><!--iner_page-->

    </div><!--row-->

    </div>
