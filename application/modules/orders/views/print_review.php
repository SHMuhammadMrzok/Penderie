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
        <script src="<?php echo base_url();?>assets/template/site/js/modernizr.custom.97074.js"></script><style>body{background-color:#fff !important}</style>
     </head>
<body>
    <div class="iner_page">
        <div class="container">
                                  
            <div class="logos_popup">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-sx-12 pull-left">
                        <?php if($serial_data->logo != ''){?>
                            <div class="logo_pop">
                                <img src="<?php echo base_url();?>assets/uploads/<?php echo $serial_data->logo;?>" class="like"  alt="logo"/>
                            </div><!--logo_pop-->
                        <?php }?>
                </div><!--col-->
              </div><!--logos_popup-->
              
             <div class="notable_pop">
                 <table width="100%">
                    <tr class="header_tr">
                        <td align="center" style="border:2px solid #000"><?php echo lang('order_number');?></td>
                        <td align="center" style="border:2px solid #000"><?php echo lang('date');?></td>
                    </tr>
                    <tr>
                        <td align="center" style="border:2px solid #000"><?php echo $serial_data->order_id;?></td>
                        <td align="center" style="border:2px solid #000"><?php echo date('Y-m-d H:i', $serial_data->unix_time);?></td> 
                    </tr>
                 </table>
              </div><!--table_pop-->
              
              <div class="thanks"> <?php echo $serial_data->header;?> <br /><?php //echo lang('thanks_for_buying_from_us')." , ".lang('card_number');?>

                  <div class="row no-margin margin-top-20px text-center">
                      <img src="<?php echo base_url().'assets/uploads/products/'.$serial_data->image;?>" alt="img"  height="100" />
                  </div><!--row-->
                  <div class="row no-margin margin-top-20px"><?php echo $serial_data->title;?> </div><!--row-->
              </div><!--thanks-->
                                            
              <div class="number">
                <?php echo $serial_data->dec_serial;?>
              </div><!--number-->
                <div class="footer_popup">
                <?php echo $serial_data->footer;?>
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
		
        <script>
			jQuery("#layerslider").layerSlider({
				pauseOnHover: false,
				autoPlayVideos: false,
				skinsPath: 'js/skins/'
			});
            
            window.print();
            
	    </script>
   </body>
</html>