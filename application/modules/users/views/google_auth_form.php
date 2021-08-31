        <section class="predcramp">
            <div class="container no-padding">
                <ul>
                    <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
                    <li><span>/</span></li>
                    <li><a href="#"><?php echo lang('google_auth_code');?></a></li>
                </ul>
            </div><!--container-->
        </section><!--predcramp-->
        <main class="no-padding-top">
            <div class="container no-padding">
                <div class="row">
                    <div class="col-md-5 center-div">
                        <div class="title-page">
                            <h4><?php echo lang('google_auth_code');?></h4>
                        </div><!--title-page-->
                        
                        <div class="registration">
                            <div class="logo text-center">
                                <a href="#"><img src="<?php echo base_url();?>assets/template/site/img/logo.png" class="img-responsive" alt="logo"/></a>
                            </div><!--logo-->
                            
                            <div class="text">
                                <article></article>
                            </div>
                            
                            <div class="login-area">
                                <form method="post" action="<?php echo base_url();?>users/users/google_auth_verify">
                                
                                    <?php if($this->session->flashdata('message')){?>   
                                        <div class="success-alert"><?php echo $this->session->flashdata('message');?></div><!--success_message-->
                                    <?php }?>
                                    <?php if($this->session->flashdata('login_error')){?>
                                        <div class="error-messege"><?php echo $this->session->flashdata('login_error');?></div><!--fail_message-->
                                    <?php }?>
                                    
         							<div class="row-form">
         								<label><?php echo lang('google_auth_code');?></label>
                                        <?php
                                         $code_data = array('name'=>'google_auth_code', 'placeholder'=>lang('google_auth_code'), 'class'=>'form-control');
                                         echo form_input($code_data);
                                        ?>
         								<p class="error-alert"><?php echo form_error('google_auth_code');?></p>
         							</div><!--row-form-->
                                    
                                    <div class="row-form">
                                        <label class="checkbox">
                                            <input type="checkbox" name="remember" id="Remember" value="1"/>
                                            <span></span>
                                        </label>
                                        <label for="Remember"><?php echo lang('remember');?></label>
                                    </div><!--col-->
                                    
         							<div class="row-form">
         								<button class="btn btn-default"><?php echo lang('login');?></button>
         							</div><!--row-form-->
                                </form>
                            </div><!--login-area-->
                        </div><!--registration-->
                    </div><!--col-->
               </div><!--row-->
            </div><!--container-->
        </main>