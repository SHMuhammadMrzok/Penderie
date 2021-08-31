<main>
	<div class="container">
       <div class="row margin-top-30px">
       	   <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
           	  <div class="send-message">
              	   <div class="title">
                   	 <h2><?php echo lang('careers_form');?></h2>
                   </div><!--title-->
                   
                   <?php if(isset($_SESSION['error_msg'])){?>
                       <div class="fail_message">
                            <?php echo $this->session->flashdata('error_msg');?>
                       </div><!--fail_message-->
                   <?php }?>
                    
                    <?php if($this->session->flashdata('success_msg')){?>
                    <div class="success_message">
                        <?php echo $this->session->flashdata('success_msg');?>
                    </div><!--success-->
                    <?php }?>
                    
              	  <form method="post" action="<?php echo base_url();?>careers/careers/save" enctype="multipart/form-data">
                  	 <div class="row no-margin margin-bottom-10px">
                        <?php $name_att = array(  
                                                'id'       => 'name',
                                                'name'     => 'name',
                                                'class'    => '',
                                                'required' => 'required',
                                                'value'    => set_value('name'),
                                                'placeholder' => lang('name')
                                             );
                        
                            echo form_input($name_att);
                            echo form_error('name');
                        ?>
                     	
                     </div><!--row-->
                      
                      <div class="row no-margin margin-bottom-10px">
                     	
                        <?php 
                            $email_att = array(  
                                                'name'          => 'email',
                                                'type'          => 'email',
                                                'id'            => 'email',
                                                'required'      => 'required',
                                                'value'         => set_value('email'),
                                                'placeholder'   => lang('email')
                                             );
                            
                            echo form_input($email_att);
                            echo form_error('email');
                        ?>
                     </div><!--row-->
                     <div class="row no-margin margin-bottom-10px">
                     	<?php 
                            $mobile_att = array(  
                                                'name'     => 'mobile',
                                                'id'       => 'mobile',
                                                'class'    => 'form-control',
                                                'required' => 'required',
                                                'value'    => set_value('mobile'),
                                                'placeholder' => lang('mobile')
                                             );
                            
                            echo form_input($mobile_att);
                            echo form_error('mobile');
                        ?>
                     </div><!--row-->
               <?php /*      
                     <div class="row no-margin margin-bottom-10px">
                     	<?php 
                            $phone_att = array(  
                                                'name'     => 'phone',
                                                'id'       => 'phone',
                                                'class'    => 'form-control',
                                                'required' => 'required',
                                                'value'    => set_value('phone'),
                                                'placeholder' => lang('phone')
                                             );
                            
                            echo form_input($phone_att);
                            echo form_error('phone');
                        ?>
                     </div><!--row-->
                     
                     <div class="row no-margin margin-bottom-10px">
                     	<?php 
                            $address_att = array(  
                                                'name'     => 'address',
                                                'id'       => 'address',
                                                'class'    => 'form-control',
                                                'required' => 'required',
                                                'value'    => set_value('address'),
                                                'placeholder' => lang('address')
                                             );
                            
                            echo form_input($address_att);
                            echo form_error('address');
                        ?>
                     </div><!--row-->
                     
                     <div class="row no-margin margin-bottom-10px">
                     	<?php 
                            $mailbox_att = array(  
                                                'name'     => 'mailbox',
                                                'id'       => 'mailbox',
                                                'class'    => 'form-control',
                                                'required' => 'required',
                                                'value'    => set_value('mailbox'),
                                                'placeholder' => lang('mailbox')
                                             );
                            
                            echo form_input($mailbox_att);
                            echo form_error('mailbox');
                        ?>
                     </div><!--row-->
                     
                     <div class="row no-margin margin-bottom-10px">
                     	<?php 
                            $post_number_att = array(  
                                                'name'     => 'post_number',
                                                'id'       => 'post_number',
                                                'class'    => 'form-control',
                                                'required' => 'required',
                                                'value'    => set_value('post_number'),
                                                'placeholder' => lang('postal_code')
                                             );
                            
                            echo form_input($post_number_att);
                            echo form_error('post_number');
                        ?>
                     </div><!--row-->
                     
                     <div class="row no-margin margin-bottom-10px">
                     	<?php 
                            $city_att = array(  
                                                'name'     => 'city',
                                                'id'       => 'city',
                                                'class'    => 'form-control',
                                                'required' => 'required',
                                                'value'    => set_value('city'),
                                                'placeholder' => lang('city_name')
                                             );
                            
                            echo form_input($city_att);
                            echo form_error('city');
                        ?>
                     </div><!--row-->
                     
                     <div class="row no-margin margin-bottom-10px">
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
                     </div><!--row-->
                     
                     <div class="row no-margin margin-bottom-10px">
                     	<?php 
                            $date_of_birth_att = array(  
                                                'name'          => 'date_of_birth',
                                                'id'            => 'date_of_birth',
                                                'class'         => 'form-control',
                                                'required'      => 'required',
                                                'value'         => set_value('date_of_birth'),
                                                'placeholder'   => lang('date_of_birth')  ,
                                                'type'          => 'date'
                                             );
                            
                            echo form_input($date_of_birth_att);
                            echo form_error('date_of_birth');
                        ?>
                     </div><!--row-->
                     */?>
                     <div class="row no-margin margin-bottom-10px">
                     	<?php 
                            $applied_job_att = array(  
                                                'name'     => 'applied_job_att',
                                                'id'       => 'applied_job_att',
                                                'class'    => 'form-control',
                                                'required' => 'required',
                                                'value'    => set_value('applied_job_att'),
                                                'placeholder' => lang('applied_job')
                                             );
                            
                            echo form_input($applied_job_att);
                            echo form_error('applied_job_att');
                        ?>
                     </div><!--row-->
                   
                   <?php /*  
                     <div class="row no-margin margin-bottom-10px">
                     	<?php 
                            $education_att = array(  
                                                'name'     => 'education',
                                                'id'       => 'education',
                                                'class'    => 'form-control',
                                                'required' => 'required',
                                                'value'    => set_value('education'),
                                                'placeholder' => lang('education')
                                             );
                            
                            echo form_input($education_att);
                            echo form_error('education');
                        ?>
                     </div><!--row-->
                     
                      <div class="row no-margin margin-bottom-10px">
                     	<?php 
                            $experience_att = array(  
                                                'name'     => 'experience',
                                                'id'       => 'experience',
                                                'class'    => 'form-control',
                                                'required' => 'required',
                                                'value'    => set_value('experience'),
                                                'placeholder' => lang('experience')
                                                
                                             );
                            echo form_textarea($experience_att);
                            echo form_error('experience');
                        ?>
                     </div><!--row-->
                     */?>
                     
                     <div class="row no-margin margin-bottom-10px">
                     	<?php 
                            $cv_att = array(  
                                            'name'     => 'userfile',
                                            'id'       => 'cv',
                                            'class'    => 'form-control',
                                            'required' => 'required',
                                            'value'    => set_value('cv'),
                                            'placeholder' => lang('cv')
                                            
                                         );
                            
                            echo form_upload($cv_att);
                            echo form_error('cv');
                        ?>
                     </div><!--row-->
                     
                      <div class="row no-margin margin-bottom-10px">
                     	<button><?php echo lang('send');?></button>
                     </div><!--row-->
                  </form>
              </div><!--send-message-->
           </div><!--col-->
           
       </div><!--row-->
            </div>
</main>