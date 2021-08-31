<div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
        <li class='active'><?php echo lang('order_details');?></li>
      </ul>
    </div>
  </div>
</div>
<main>        
  <div class="container">
    <div class="contact-page">
      <div class="row">
			<!--Middle Part Start-->
        <div class="col-md-12 contact-form">
          <div class="contact-title">
            <h4><?php echo lang('return_product');?></h4>
          </div>
                              
  				<div class="col-sm-12">
  				  <div class="panel panel-default">
                    <div class="panel-heading">
				      <?php echo isset($error) ? $error : '';?>
					    <span>
                          <?php echo lang('read_and_accepted');?> <a target="_blank" href="<?php echo base_url().'Page_Details/'.$return_policy->id;?>"><?php echo $return_policy->title;?></a>
			            </span>
   					</div>
              <?php if(!isset($error)){?>
                <!--<div class="panel-body">
                    <div id="balance">-->
                        <form class="register-form" action="" method="post">
                            <div class="row no-margin">
                           	    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="payment_bar">
                                      <div class="title">
                                      	<h2><?php echo $order_product->title;?></h2>
                                      </div><!--title-->
                                      
                                   	    <div class="form-group required">
                                            <label for="blan" class="line2_5" style="line-height:2"><?php echo lang('quantity');?></label>
                                            <div class="col-lg-9 col-md-9 col-sm-19 col-xs-12">
                                            <select size="width:100%;height:48px;" name="qty" class="form-control select2">
                                                <?php for($i=1;$i<=$order_product->qty;$i++){?>
                                                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                                <?php }?>
                                            </select>
                                            </div>
                                    	</div><!--col-->

                                      <div class="form-group required">
                                          <label for="serial_input" class="line2_5"><?php echo lang('return_reason');?></label>
                                          <div class="col-lg-9 col-md-9 col-sm-19 col-xs-12">
                                          <?php
                                            $message_att = array(
                                                                'name'     => 'reason',
                                                                'class'    => 'form-control unicase-form-control',
                                                                'required' => 'required',
                                                                 );
                                            echo form_textarea($message_att);
                                          ?>
                                              <?php echo form_error('reason');?>
                                          </div><!--col-->
                                      </div>

                                    </div><!--payment_bar-->
                                </div><!--col-->
                            </div><!--row-->

                              <div class="form-group">
                                <button type="submit" class="btn-upper btn btn-primary checkout-page-button"><?php echo lang('continue');?></button>
                              </div><!--col-->
                          </form>
                        <!--</div>
                    </div>-->
                <?php }?>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</main>