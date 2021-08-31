<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
    <div class="row no-margin">
   	    <div class="iner_page">
       	    <div class="row no-margin">
                <h1 class="title_h1"><?php echo lang('register');?></h1>
                <div class="registration">
                    <form action="<?php echo base_url();?>users/register/save" method="post">
                            <?php if($this->session->flashdata('message')){?>   
                                <div class="success_message"><?php echo $this->session->flashdata('message');?></div><!--success_message-->
                            <?php }?>
                            <?php if($this->session->flashdata('error')){?>
                                <div class="fail_message"><?php echo $this->session->flashdata('error');?></div><!--fail_message-->
                            <?php }?>

                            <div class="block_regist">

                               <div class="row no-margin margin-bottom-10px">

                                    <h3><?php echo lang('before_activate')."  : "; ?></h3>

                                    <div class="gray">
                                    
                                        <div class="row no-margin margin-bottom-10px">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label for="namefrist">
                                                
                                                    <?php echo lang('email_active');?>
                                                
                                                </label>
                                            </div><!--col-->
                                        </div><!--row-->

                                        <div class="row no-margin margin-bottom-10px">
                                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                                <label for="namefrist">
                                                    <?php echo lang('sms_active')."(";?>
                                                    <a href="<?php echo base_url();?>users/register/sms_register_active/<?php echo $id; ?>"><?php echo lang('enter_active_code');?></a>
                                                    <?php echo ")";?>
                                                </label>
                                            </div><!--col-->
                                       </div><!--row-->

                                      
                                    </div><!--gray-->
                                </div><!--row-->

                            </div><!--block_regist-->

                        </form>

                    </div><!--registration-->

                </div><!--row-->

		    </div><!--iner_page-->

        </div><!--row-->

    </div><!--col-->   	

