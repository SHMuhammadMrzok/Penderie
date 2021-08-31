<div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
        <li class='active'><?php echo lang('contact_us');?></li>
      </ul>
    </div>
  </div>
</div>

<main>
  <div class="container">
    <div class="contact-page">
      <div class="row">

        <div class="w-100">
          <div class="map-area">
            <iframe src="https://www.google.com/maps/embed?pb=<?php echo $this->config->item('google_map_key');?>" width="100%" height="450" style="border:0">
            </iframe>
          </div>

        </div>
      </div>
      <div class="row">

        <div class="col-md-8 contact-form">
          <div class="contact-title">
            <h4><?php echo lang('contact_us');?></h4>
          </div>

          <form class="register-form" role="form" action="<?php echo base_url();?>contact_us/contact_us/send" method="post" enctype="multipart/form-data" id="contact_us_form">
              <?php if(isset($_SESSION['contact_us_validation_message'])){//$this->session->flashdata('contact_us_validation_message')){?>
                <div class="alert alert-danger">
                    <?php echo $this->session->flashdata('contact_us_validation_message');?>
                </div><!--fail_message-->
              <?php }?>
              <?php if(isset($_SESSION['contact_us_success_send_message'])){//<?php if($this->session->flashdata('contact_us_success_send_message')){?>
                <div class="alert alert-success">
                    <?php echo $this->session->flashdata('contact_us_success_send_message');?>
                </div><!--success-->
              <?php }?>
              <?php if(isset($_SESSION['contact_us_failed_send_message'])){//<?php if($this->session->flashdata('contact_us_failed_send_message')){?>
                <div class="alert alert-danger">
                    <?php echo $this->session->flashdata('contact_us_failed_send_message');?>
                </div><!--fail_message-->
              <?php }?>

            <div class="form-group">
              <label class="info-title" for="exampleInputName"><?php echo lang('name');?><span>*</span></label>
              <?php $name_att = array(
                                      'id'       => 'exampleInputName',
                                      'name'     => 'name',
                                      'class'    => 'form-control unicase-form-control text-input',
                                      'required' => 'required',
                                      'value'    => set_value('name')
                                   );

                  echo form_input($name_att);
              ?>
              <p class="error-alert"><?php echo form_error('name'); ?></p>
            </div>

            <div class="form-group">
              <label class="info-title" for="exampleInputEmail1"><?php echo lang('email');?> <span>*</span></label>
              <?php
                  $email_att = array(
                                      'name'          => 'email',
                                      'type'          => 'email',
                                      'id'            => 'exampleInputEmail1',
                                      'class'         => 'form-control unicase-form-control text-input',
                                      'required'      => 'required',
                                      'value'         => set_value('email')
                                   );

                  echo form_input($email_att);
              ?>
              <p class="error-alert"><?php echo form_error('email'); ?></p>
            </div>

            <div class="form-group">
              <label class="info-title" for="input-mobile"><?php echo lang('mobile');?> <span></span></label>
              <?php
                  $mobile_att = array(
                                      'name'     => 'mobile',
                                      'id'       => 'input-mobile',
                                      'class'    => 'form-control unicase-form-control text-input',
                                      'required' => 'required',
                                      'value'    => set_value('mobile')
                                   );

                  echo form_input($mobile_att);
              ?>
              <p class="error-alert"><?php echo form_error('mobile'); ?></p>
            </div>

            <div class="form-group">
              <label class="info-title" for="exampleInputTitle"><?php echo lang('message_title');?> <span>*</span></label>
              <?php
                  $title_att = array(
                                      'name'     => 'title',
                                      'id'       => 'exampleInputTitle',
                                      'class'    => 'form-control unicase-form-control text-input',
                                      'required' => 'required',
                                      'value'    => set_value('title')
                                   );
                  echo form_input($title_att);
              ?>
              <p class="error-alert"><?php echo form_error('title'); ?></p>
            </div>


            <div class="form-group">
              <label class="info-title" for="exampleInputComments"><?php echo lang('message');?> <span>*</span></label>
              <?php
                $message_att = array(
                                    'name'     => 'message',
                                    'id'       => 'exampleInputComments',
                                    'class'    => 'form-control unicase-form-control',
                                    'required' => 'required',
                                    'value'    => set_value('message')
                                    //'rows'       => '10'

                                 );
                echo form_textarea($message_att);
            ?>
            <p class="error-alert"><?php echo form_error('message'); ?></p>
            </div>

            <div class="form-group">
              <button type="submit" class="g-recaptcha btn-upper btn btn-primary checkout-page-button" data-sitekey="<?php echo $reCAPTCHA_site_key; ?>" data-callback='onSubmit' data-action='submit'><?php echo lang('send');?></button>
            </div>
          </form>
        </div>

        <div class="col-md-4 contact-info">
            <div class="contact-title">
              <h2><?php echo lang('contact_data');?></h2>
            </div>
            <ul>
              <?php if($site_address != ''){?>
                <li>
                  <svg>
                    <use xlink:href="#pin"></use>
                  </svg>
                  <span><?php echo $site_address;?></span>
                </li>
              <?php }?>

              <?php if($site_mobiles != ''){?>
                <li>
                  <svg>
                    <use xlink:href="#phone"></use>
                  </svg>
                  <span>
                    <?php $site_mobiles = str_replace(',', '<br>', $site_mobiles);?>
                    <?php echo $site_mobiles;?>
                 </span>
                </li>
              <?php }?>

              <?php if($this->config->item('site_email') != ''){?>
                <li>
                  <svg>
                    <use xlink:href="#email"></use>
                  </svg>
                  <span><a href="mailto:<?php echo $this->config->item('site_email');?>"><?php echo $this->config->item('site_email');?></a></span>
                </li>
              <?php }?>
            </ul>
          </div>

      </div>
    </div>
  </div>
</main>

<script>
   function onSubmit(token) {
     document.getElementById("contact_us_form").submit();
   }
 </script>