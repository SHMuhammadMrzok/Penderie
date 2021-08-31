<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/template/site/css/maps/styles.css" />
<div class="portlet-body">
	<div class="row">
		<div class="col-md-3 col-sm-3 col-xs-3">
        	<div class="tab_user_style">
			<ul class="nav nav-tabs tabs-left" style="border-right: none;"><!--tabs-right-->
				<li class="active"><a href="#general" data-toggle="tab"><?php echo lang('user_info');?> </a></li>
				<li><a href="#user_affiliate" data-toggle="tab"><?php echo lang('user_affiliate');?></a></li>
				<li><a href="#user_orders" data-toggle="tab"><?php echo lang('user_orders');?></a></li>
                <li><a href="#user_log" data-toggle="tab"><?php echo lang('user_log');?> </a></li>
                <li><a href="#user_visits" data-toggle="tab"><?php echo lang('user_visits');?> </a></li>
                <li><a href="#user_balance" data-toggle="tab"><?php echo lang('user_balance');?> </a></li>
			</ul>
            </div><!--tab_user_style-->
		</div>
		<div class="col-md-9 col-sm-9 col-xs-9">
			<div class="tab-content">
				<div class="tab-pane active" id="general">
					<p>
					  <?php if(!empty($row_data))
                        {
                            foreach($row_data as $column_name => $column_data)
                            {?>
                                <div class="row static-info">
                    				<div class="col-md-5 name">
                    					 <?php echo $column_name;?>  :
                    				</div>
                    				<div class="col-md-7 value">
                    					 <?php echo $column_data;?>
                    				</div>
                    			</div>
                            <?php }?>
                            <?php if( ($user_data->wd_lat != '') && ($user_data->wd_lng != '') ){?>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                    					 <?php echo lang('device_location');?>  :
                    				</div>
                       	        </div>
                                <div class="map_canvas"></div>
                                 <?php
                                 $geo_att  = array('name'=>'geocomplete', 'id'=>'geocomplete', 'placeholder'=>lang('type_in_address'), 'value'=> "[$user_data->wd_lng, $user_data->wd_lat]", 'style'=>'display: none;');
                                 
                                 echo form_input($geo_att);
                                 echo form_error('geocomplete');
                            
                            }?>
                            
                        <?php }else{?>
                            <div class="row static-info">
                                <div class="col-md-5 name">
                					  <?php echo lang("no_data");?>
                				</div>
           				    
                    		</div>
                        <?php }// if !empty?>
					</p>
				</div>
				<div class="tab-pane fade" id="user_affiliate">
					<p>
    	               <div class="portlet-body">
					       <div class="table-scrollable">
    				        <table class="table table-hover">
    					       <thead>
        					       <tr>
                                        <td ><?php echo lang('buyer');?></td>
                                        <td ><?php echo lang('order_total');?></td>
                                        <td ><?php echo lang('commission');?></td>
                                        <td ><?php echo lang('commission_amount');?></td>
                                        <td ><?php echo lang('unix_time');?></td>
                                        <td ><?php echo lang('pay_stat');?></td>
        		                   </tr>
    					       </thead>
    					       <tbody>
                                   <?php if(isset($user_affiliate_log_data) && !empty($user_affiliate_log_data)){
                                            foreach($user_affiliate_log_data as $log){
                                   ?>
    					           <tr>
    					                <td><?php echo $log->first_name.' '.$log->last_name;?></td>
                                        <td><?php echo $log->final_total;?></td>
                                        <td><?php echo $log->commission;?></td>
                                        <td><?php echo $log->amount;?></td>
                                        <td><?php echo date('Y/m/d H:i',$log->unix_time);?></td>
                                        <td>
                                            <?php 
                                                if($log->pay == 0)
                                                {
                                                    $pay = '<span class="badge badge-danger">'.lang('no').'</span>';    
                                                }
                                                elseif($log->pay == 1)
                                                {
                                                    $pay = '<span class="badge badge-success">'.lang('yes').'</span>';
                                                }
                                            ?>
                                            <?php echo $pay;?>
                                        </td>
                                   </tr>
                                   <?php }
                                   }else{?>
                                    <tr><td colspan="6" style="text-align: center;"><?php echo lang('no_data');?></td></tr>
                                   <?php }?>
                                   
    						   </tbody>
    						</table>
                           </div>
                        </div>
					</p>
				</div>
				
                
                <div class="tab-pane fade" id="user_log"></div>
                <div class="tab-pane fade" id="user_visits"></div>
                <div class="tab-pane fade" id="user_orders"></div>
                <div class="tab-pane fade" id="user_balance"></div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
   get_visits('<?php echo base_url().'users/admin_users/user_visits_log_ajax/'; ?>');
   get_userslog('<?php echo base_url().'users/admin_users/user_userslog_ajax/'; ?>');
   get_user_orders('<?php echo base_url().'users/admin_users/user_orders_ajax/'; ?>');
   get_user_balance('<?php echo base_url().'users/admin_users/user_balance_ajax/'; ?>');
   
   $('body').on('click', '.pages_links_visits', function(e){
        e.preventDefault();
        get_visits($(this).attr('href'));
   });
   
   $('body').on('click', '.pages_links_userslog', function(e){
        e.preventDefault();
        get_userslog($(this).attr('href'));
   }); 
   
   $('body').on('click', '.pages_links_orders', function(e){
        e.preventDefault();
        get_user_orders($(this).attr('href'));
   });
   
   $('body').on('click', '.pages_links_balance', function(e){
        e.preventDefault();
        get_user_balance($(this).attr('href'));
   }); 
});

function get_visits(url)
{
    $.post(url, {lang_id: <?php echo $lang_id; ?>, user_id: <?php echo $user_id; ?>}, function(result){
        $('#user_visits').html(result);
    });
}

function get_userslog(url)
{
    $.post(url, {lang_id: <?php echo $lang_id; ?>, user_id: <?php echo $user_id; ?>}, function(result){
        $('#user_log').html(result);
    });
}

function get_user_orders(url)
{
    $.post(url, {lang_id: <?php echo $lang_id; ?>, user_id: <?php echo $user_id; ?>}, function(result){
        $('#user_orders').html(result);
    });
}

function get_user_balance(url)
{
    $.post(url, {lang_id: <?php echo $lang_id; ?>, user_id: <?php echo $user_id; ?>}, function(result){
        $('#user_balance').html(result);
    });
}




</script>

 
<?php /*<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
<script src="<?php echo base_url();?>assets/template/site/js/jquery.geocomplete.js"></script>
<?php if( ($user_data->wd_lat != '') && ($user_data->wd_lng != '') ){?>
    <script>
      
      $(function(){
            
        var options = {
          map: ".map_canvas",
          location: ['<?php echo $user_data->wd_lng;?>', '<?php echo $user_data->wd_lat;?>']
        };
        
        $("#geocomplete").geocomplete(options);
        
      });
          
    </script>
<?php }?>*/?>