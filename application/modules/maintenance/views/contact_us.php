    <section class="predcramp">
        <div class="container no-padding">
            <ul>
                <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
                <li><span>/</span></li>
                <li><a href="#"><?php echo lang('contact_us');?></a></li>
            </ul>
        </div><!--container-->
    </section><!--predcramp-->
    
    <main class="no-padding-top">
        <div class="container no-padding">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="title-page">
                        <h4><?php echo lang('contact_us');?></h4>
                    </div><!--title-page-->
                    <div class="row no-margin enquiry">
                        <form action="<?php echo base_url();?>contact_us/contact_us/send" method="post" enctype="multipart/form-data" class="form-horizontal">
                    
                        <?php if($this->session->flashdata('contact_us_validation_message')){?>
                            <p class="error-messege">
                                <?php echo $this->session->flashdata('contact_us_validation_message');?>
                            </p><!--fail_message-->
                            <?php }?>
                            <?php if($this->session->flashdata('contact_us_success_send_message')){?>
                            <p class="success-alert">
                                <?php echo $this->session->flashdata('contact_us_success_send_message');?>
                            </p><!--success-->
                            <?php }?>
                            <?php if($this->session->flashdata('contact_us_failed_send_message')){?>
                            <p class="error-messege">
                                <?php echo $this->session->flashdata('contact_us_failed_send_message');?>
                            </p><!--fail_message-->
                        <?php }?>
                        
	     	 		 	    <div class="row-form">
	     	 		 	    	<label><?php echo lang('name');?></label>
	     	 		 	    	<?php $name_att = array(  
                                                                'id'       => 'input-name',
                                                                'name'     => 'name',
                                                                'class'    => 'form-control',
                                                                'required' => 'required',
                                                                'value'    => set_value('name'),
                                                               // 'placeholder' => lang('name')
                                                             );
                                        
                                            echo form_input($name_att);
                                        ?>
                                <p class="error-alert"><?php echo form_error('name'); ?></p>
	     	 		 	    </div><!--row-form-->
	     	 		 	    <div class="row-form">
	     	 		 	    	<label><?php echo lang('email');?></label>
	     	 		 	    	<?php 
                                        $email_att = array(  
                                                            'name'          => 'email',
                                                            'type'          => 'email',
                                                            'id'            => 'email',
                                                            'class'         => 'form-control',
                                                            'required'      => 'required',
                                                            'value'         => set_value('email'),
                                                            //'placeholder'   => lang('email')
                                                         );
                                        
                                        echo form_input($email_att);
                                    ?>
                                <p class="error-alert"><?php echo form_error('email'); ?></p>
	     	 		 	    </div><!--row-form-->
	     	 		 	    <div class="row-form">
	     	 		 	    	<label><?php echo lang('mobile');?></label>
	     	 		 	    	<?php 
                                    $mobile_att = array(  
                                                        'name'     => 'mobile',
                                                        'id'       => 'input-mobile',
                                                        'class'    => 'form-control',
                                                        'required' => 'required',
                                                        'value'    => set_value('mobile'),
                                                        //'placeholder' => lang('mobile')
                                                     );
                                    
                                    echo form_input($mobile_att);
                                ?>
	     	 		 	    	<p class="error-alert"><?php echo form_error('mobile'); ?></p>
	     	 		 	    </div><!--row-form-->
	     	 		 	    <div class="row-form">
	     	 		 	    	<label><?php echo lang('message_title');?></label>
	     	 		 	    	<?php 
                                        $title_att = array(  
                                                            'name'     => 'title',
                                                            'id'       => 'title',
                                                            'class'    => 'form-control',
                                                            'required' => 'required',
                                                            'value'    => set_value('title'),
                                                            //'placeholder' => lang('message_title')
                                                         );
                                        echo form_input($title_att);
                                    ?>
	     	 		 	    	<p class="error-alert"><?php echo form_error('title'); ?></p>
	     	 		 	    </div><!--row-form-->
	     	 		 	    <div class="row-form">
	     	 		 	    	<label><?php echo lang('message');?></label>
	     	 		 	    	<?php 
                                    $message_att = array(  
                                                        'name'     => 'message',
                                                        'id'       => 'input-enquiry',
                                                        'class'    => 'form-control',
                                                        'required' => 'required',
                                                        'value'    => set_value('message'),
                                                       // 'placeholder' => lang('message')
                                                       'rows'       => '10'
                                                        
                                                     );
                                    echo form_textarea($message_att);
                                ?>
	     	 		 	    	<p class="error-alert"><?php echo form_error('message'); ?></p>
	     	 		 	    </div><!--row-form-->
	     	 		 	    <div class="row-form">
	     	 		 	    	<button class="btn btn-default create-button pull-right"><?php echo lang('send');?></button>
	     	 		 	    </div><!--row-form-->
	     	 		 	</form>
	     	 		 </div><!--row-->
		     	 </div><!--col-->
 		     	 
 	   	 	     <div class="col-md-6">
 	   	 	     	<!--
                          <div class="map">
 	   	 	     		<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d27630.954568063993!2d31.1882187!3d30.040606699999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2seg!4v1501049978998" width="100%" height="300" frameborder="0" style="border:0" allowfullscreen></iframe>
 	   	 	     	</div><!--map-->
                    <div class="title-page">
                        <h4><?php echo lang('store_name');?></h4>
                    </div><!--title-page-->
                    
 	   	 	     	<div class="contact-info">
 	   	 	     		<ul>
							<li><i class="fa fa-map-marker"></i> <span><?php echo $site_address;?></span></li>
   	 	     				<li><i class="fa fa-phone"></i> <span><?php echo $site_mobiles;?></span></li>
                            <!--
   	 	     				<li><i class="fa fa-envelope"></i> <a href="#">ahmedsamir084@gmail.com</a></li>
   	 	     				<li><i class="fa fa-facebook"></i> <a href="#">www.facebook.com</a></li>
   	 	     				<li><i class="fa fa-twitter"></i> <a href="#">www.twitter.com</a></li>
   	 	     				<li><i class="fa fa-linkedin"></i> <a href="#">www.linkdin.com</a></li>
   	 	     				<li><i class="fa fa-google-plus"></i> <a href="#">www.google.com</a></li>
                            -->
	   	 	     		</ul>
 	   	 	     	</div><!--contact-info-->
 	   	 	     </div><!--col-->
 	   	 	 
 		     </div><!--row-->
 	   	 </div><!--container-->
 	   </main>