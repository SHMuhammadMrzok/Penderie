        <main class="no-padding-top">
            <div class="container no-padding">
                <div class="row">
                    <div class="col-md-5 center-div">
                        <div class="title-page">
                            <h4><?php echo lang('register');?></h4>
                        </div><!--title-page-->
                        
                        <div class="registration">                            
                            <div class="login-area">
                                <form action="<?php echo base_url();?>users/register/save" method="post">
                                
                                    <?php if($this->session->flashdata('message')){?>   
                                        <div class="success-alert"><?php echo $this->session->flashdata('message');?></div><!--success_message-->
                                    <?php }?>
                                    <?php if($this->session->flashdata('error')){?>
                                        <div class="error-messege"><?php echo $this->session->flashdata('error');?></div><!--fail_message-->
                                    <?php }?>
                                    
         							
                                    <div class="text">
                                        <article><?php echo lang('email_active');?></article>
                                    </div>
                                </form>
                            </div><!--login-area-->
                        </div><!--registration-->
                    </div><!--col-->
               </div><!--row-->
            </div><!--container-->
        </main>