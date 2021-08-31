<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>like4card </title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/template/site/template/css/font-awesome/css/font-awesome.min.css">
    	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/template/site/css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/template/site/css/style.css" media="all"/>
        <link rel="shortcut icon" href="<?php echo base_url();?>assets/template/site/img/fav_ico.ico" />
        <script src="<?php echo base_url();?>assets/template/site/js/modernizr.custom.97074.js"></script>
     </head>
    <body>
        <div class="iner_page">
            <div class="container">
                  <div class="logos_popup">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-sx-12 pull-left">
                        <div class="logo_pop">
                            <img src="<?php echo base_url();?>assets/template/site/img/logo.png" class="like" height="30" alt="logo"/>
                        </div><!--logo_pop-->
                      </div><!--col-->
                  </div><!--logos_popup-->
                  
                  <div class="thanks">
                      <div class="row no-margin"><?php echo lang('send_to_customer');?>   </div><!--row-->
                      
                  </div><!--thanks-->
                  
                  <form method="post" action="<?php echo base_url();?>orders/sms_serials/sms">
                    <input type="hidden" name="serial_id" value="<?php echo $serial_id;?>" />
                    <div class="row no-margin margin-bottom-10px">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="receiver_number"><?php echo lang('receiver_number');?></label>
                        </div><!--col-->
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                            <input type="text" id="receiver_number" name="receiver_number" class="form-control" required="required"/>
                        </div><!--col-->    
                    </div><!--row-->
                    
                    <div class="row no-margin margin-bottom-10px">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="message"><?php echo lang('message');?></label>
                        </div><!--col-->
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                            <textarea class="form-control" id="message" name="message" readonly="readonly"><?php echo $serial_data->msg;?></textarea>
                        </div><!--col-->    
                    </div><!--row-->
                    
                    <div class="row no-margin margin-bottom-10px">
                        <div class="col-lg-12 col-md-2 col-sm-12 col-xs-12">
                            <button class="btn btn-primary"><?php echo lang('send');?></button>
                        </div><!--col-->
                    </div><!--row-->
                </form>
                
                  <div class="footer_popup">
                      <span><?php echo lang('thanks_for_visiting_us');?></span> 
                      <br/>
                      <span>Thanks For Visiting US</span>
                      <br/>
                      <span>Tel : 0114889811 &nbsp; &nbsp;  Mobile :0592332212 - 0564243466 </span>
                  </div><!--footer_popup-->
            </div><!--container-->
        </div><!--iner_page-->
        <a href="#" class="scrollup"><i class="fa fa-angle-up"></i></a>
        <script type="text/javascript" src="<?php echo base_url();?>assets/template/site/js/jquery-2.1.3.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>assets/template/site/js/greensock.js"></script>
	    <script type="text/javascript" src="<?php echo base_url();?>assets/template/site/js/layerslider.transitions.js"></script>
	    <script type="text/javascript" src="<?php echo base_url();?>assets/template/site/js/layerslider.kreaturamedia.jquery.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>assets/template/site/js/bootstrap.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>assets/template/site/js/jquery.bxslider.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>assets/template/site/js/jquery.shiningImage.min.js"></script>       
        <script type="text/javascript" src="<?php echo base_url();?>assets/template/site/js/jquery.hoverdir.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>assets/template/site/js/bootstrap-hover-dropdown.js"></script>
	    <script type="text/javascript" src="<?php echo base_url();?>assets/template/site/js/html5lightbox.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>assets/template/site/js/function_js.js"></script>
		
        
   </body>
</html>