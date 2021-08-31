<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="memberModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="memberModalLabel">
            
        </h4>
      </div>
      <div class="modal-body">
        <form method="post" action="<?php echo base_url();?>users/users/login">
            <div class="row no-margin margin-bottom-10px">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label for="email_lab"><?php echo lang('email');?></label>
                </div><!--col-->
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <?php
                      $email_att = array('name'=>'email', 'id'=>'email_lab', 'class'=>'form-control');
                      echo form_input($email_att);
                    ?>
                </div><!--col-->
                <?php echo form_error('email');?>
            </div><!--Email row-->


            <div class="row no-margin margin-bottom-10px">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                <label for="password"><?php echo lang('password');?></label>
            </div><!--col-->
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <?php
                      $password_att = array('name'=>'password', 'id'=>'password', 'placeholder'=>'', 'class'=>'form-control');
                      echo form_password($password_att); 
                    ?>
                </div><!--col-->
                <?php echo form_error('password');?>
            </div><!--Password row-->

            <div class="modal-footer">
                <button class="btn btn-primary full-width"><?php echo lang('login');?> </button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo lang('close');?></button>
            </div>

        </form>

      </div>          
      
    </div>
  </div>
</div>
