<script type="text/javascript">
    $(document).ready(function() {

        $("#mytags_users").tagit({
           fieldName: "user_id",
           singleField: false,
           singleFieldNode: $('#mytags_users'),
           allowSpaces: true,
           minLength: 2,
           removeConfirmation: true,
           tagLimit :1,
           tagSource: function( request, response ) {

            $.ajax({
             url: "<?php echo base_url();?>reports/dynamic_reports/get_users_suggestions",  //url to get tags
             data: { term:request.term }, //data post
             dataType: "json",
             type:"POST",
             success: function( data ) {
              response( $.map( data, function( item ) {

               return {
                label: item.label,
                value: item.value
               }
              }));
             }
            });
           }
          });


          $("#mytags_phones").tagit({
           fieldName: "user_phone_id",
           singleField: false,
           singleFieldNode: $('#mytags_phones'),
           allowSpaces: true,
           minLength: 3,
           removeConfirmation: true,
           tagLimit :1,
           tagSource: function( request, response ) {
            //console.log("1");
            $.ajax({
             url: "<?php echo base_url();?>reports/dynamic_reports/get_suggestions/phone",  //url to get tags
             data: { term:request.term }, //data post
             dataType: "json",
             type:"POST",
             success: function( data ) {
              response( $.map( data, function( item ) {

               return {
                label: item.label,
                value: item.value
               }
              }));
             }
            });
           }
          });

          $("#mytags_ip_address").tagit({
           fieldName: "user_ip_address_id",
           singleField: false,
           singleFieldNode: $('#mytags_ip_address'),
           allowSpaces: true,
           minLength: 3,
           removeConfirmation: true,
           tagLimit :1,
           tagSource: function( request, response ) {
            //console.log("1");
            $.ajax({
             url: "<?php echo base_url();?>reports/dynamic_reports/get_suggestions/ip_address",  //url to get tags
             data: { term:request.term }, //data post
             dataType: "json",
             type:"POST",
             success: function( data ) {
              response( $.map( data, function( item ) {

               return {
                label: item.label,
                value: item.value
               }
              }));
             }
            });
           }
          });

          $("#mytags_email").tagit({
           fieldName: "user_email_id",
           singleField: false,
           singleFieldNode: $('#mytags_email'),
           allowSpaces: true,
           minLength: 3,
           removeConfirmation: true,
           tagLimit :1,
           tagSource: function( request, response ) {
            //console.log("1");
            $.ajax({
             url: "<?php echo base_url();?>reports/dynamic_reports/get_suggestions/email",  //url to get tags
             data: { term:request.term }, //data post
             dataType: "json",
             type:"POST",
             success: function( data ) {
              response( $.map( data, function( item ) {

               return {
                label: item.label,
                value: item.value
               }
              }));
             }
            });
           }
          });


          $("#mytags_coupons").tagit({
           fieldName: "coupon_id",
           singleField: false,
           singleFieldNode: $('#mytags_coupons'),
           allowSpaces: true,
           minLength: 3,
           removeConfirmation: true,
           tagLimit :1,
           tagSource: function( request, response ) {

            $.ajax({
             url: "<?php echo base_url();?>reports/dynamic_reports/get_coupon_suggestions/",  //url to get tags
             data: { term:request.term }, //data post
             dataType: "json",
             type:"POST",
             success: function( data ) {
              response( $.map( data, function( item ) {

               return {
                label: item.label,
                value: item.value
               }
              }));
             }
            });
           }
          });


    });
</script>


<div class="tab-pane active" id="tab_1">
	<div class="portlet box blue">
		<div class="portlet-title">
			<div class="caption">
				<i class="fa fa-gift"></i><?php echo lang('filters');?>
			</div>
			<div class="tools">
				<a href="javascript:;" class="collapse" data-original-title="" title="">
				</a>
				<a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
				</a>
				<a href="javascript:;" class="reload" data-original-title="" title="">
				</a>
				<a href="javascript:;" class="remove" data-original-title="" title="">
				</a>
			</div>
		</div>
		<div class="portlet-body form form_new">
			<!-- BEGIN FORM-->
			<form action="<?php echo base_url();?>reports/dynamic_reports/index" method="post" class="horizontal-form">
				<div class="form-body">
                    <div class="row row_gray no-margin">
                    	<div class="row no-margin">

							<div class="col-md-3">

                                <div class="form-group">
                                	<label class="control-label"><?php echo lang('customer_groups_filter');?></label>
									<?php echo form_dropdown('customer_group_id', $customer_groups, (isset($_POST['customer_group_id']) ? $_POST['customer_group_id'] : ''), 'class="form-control select2" id="customer_group_id"');?>
    							</div><!--Customer Group Filter-->

                                <!--<div class="form-group">
                                	<label class="control-label"><?php echo lang('details');?></label>
                                    <select class="form-control" id="select_area" name="report_details">
                                            <option value="0">=====</option>
                                        	<option value="1"><?php echo lang('order_details');?></option>
                                            <option value="2"><?php echo lang('product_details');?></option>
                                            <option value="3"><?php echo lang('customer_list');?></option>
                                            <option value="4"><?php echo lang('all_details');?></option>
                                        </select>
                            	</div><!--form-group-->

                                <div style="display: block;">
                                    <input type="radio" name="details_type" value="view" checked='checked' />
                                    <span><?php echo lang('view');?></span>
                                </div>

                                <?php /*
                                <div style="display: block;">
                                    <input type="radio" name="details_type" value="pdf" />
                                    <span><?php echo lang('export').' PDF';?></span>
                                </div>
                                */?>

                                <div style="display: block;">
                                    <input type="radio" name="details_type" value="excel" />
                                    <span><?php echo lang('export').' Excel';?></span>
                                </div>

    						</div><!--col-->

                            <div class="col-md-2">
                                <div class="form-group">
                                	<label class="control-label"><?php echo lang('payment_filter');?></label>

                                    <?php echo form_dropdown('payment', $payment_options, (isset($_POST['payment']) ? $_POST['payment'] : ''), 'class="form-control select2" id="payment_filter"');?>
    							</div><!--Payment Filter-->

                                <div class="form-group">
                                	<label class="control-label"><?php echo lang('users_filters');?></label>
									<?php //echo form_dropdown('user_id', $users, (isset($_POST['user_id']) ? $_POST['user_id'] : ''), 'class="form-control select2" id="users_filter"');?>

                                    <ul id="mytags_users">
                                        <!-- Existing list items will be pre-added to the tags -->
                                        <?php if(isset($_POST['user_id'])){?>
                                            <li id="user_id"><?php echo $_POST['user_id']; ?></li>
                                        <?php }?>

                                    </ul>
    							              </div><!--Users Filter-->

                                <div class="form-group">
                                	<label class="control-label"><?php echo lang('stores_filter');?></label>
    							                <?php echo form_dropdown('store_id', $stores, (isset($_POST['store_id']) ? $_POST['store_id'] : ''), 'class="form-control select2" id="store_id"');?>
    							              </div><!--stores Filter-->

                            </div><!--col-->

                            <div class="col-md-2">
                                <!--<div class="form-group">
                                	<label class="control-label"><?php echo lang('products_filter');?></label>
									            <?php echo form_dropdown('products_filter', $products, (isset($_POST['products_filter']) ? $_POST['products_filter'] : ''), 'class="form-control select2" id="products_filter"');?>
    							                 </div><!--Products Filter-->
                              <div class="form-group">
                                <label class="control-label"><?php echo lang('email_filter');?></label>
									                       <?php //echo form_dropdown('user_email_id', $emails, (isset($_POST['user_email_id']) ? $_POST['user_email_id'] : ''), 'class="form-control select2 js-data-example-ajax" id="emails"');?>
                                 <ul id="mytags_email">
                                    <!-- Existing list items will be pre-added to the tags -->
                                    <?php if(isset($_POST['user_email_id'])){?>
                                        <li id="emails"><?php echo $_POST['user_email_id']; ?></li>
                                    <?php }?>

                                </ul>
    							           </div><!--Emails Filter-->

                              <div class="form-group">
                              	<label class="control-label"><?php echo lang('countries_filter');?></label>
  							                <?php echo form_dropdown('country_id', $countries, (isset($_POST['country_id']) ? $_POST['country_id'] : ''), 'class="form-control select2" id="country_id"');?>
  							              </div><!--Countries Filter-->

                              
                            </div><!--col-->

                            <div class="col-md-3">
                                <div class="form-group">
                                	<label class="control-label"><?php echo lang('categories_filter');?></label>
              									   <?php echo form_multiselect('cat_id[]', $categories, (isset($_POST['cat_id']) ? $_POST['cat_id'] : ''), 'class="form-control select2" id="cat_id"');?>

                  							</div><!--Categories Filter-->

                            	   <div class="form-group">
                                	<label class="control-label"><?php echo lang('phones_filter');?></label>
									                         <?php //echo form_dropdown('user_phone_id', $phone_numbers, (isset($_POST['user_phone_id']) ? $_POST['user_phone_id'] : ''), 'class="form-control select2" id="phone_number"');?>

                                   <ul id="mytags_phones">
                                       <!-- Existing list items will be pre-added to the tags -->
                                       <?php if(isset($_POST['user_phone_id'])){?>
                                           <li id="phones"><?php echo $_POST['user_phone_id']; ?></li>
                                       <?php }?>
                                   </ul>

    							              </div><!--Phone Filter-->
                            </div><!--col-->
                            <div class="col-md-2">
                                <div class="form-group">
                                	<label class="control-label"><?php echo lang('coupon_codes_filter');?></label>

                                       <ul id="mytags_coupons">
                                           <!-- Existing list items will be pre-added to the tags -->
                                           <?php if(isset($_POST['coupon_id'])){?>
                                               <li id="coupon_id"><?php echo $_POST['coupon_id']; ?></li>
                                           <?php }?>
                                       </ul>
    							              </div><!--Coupon Codes-->

                                <div class="form-group">
                                	<label class="control-label"><?php echo lang('ip_addresses_filter');?></label>
									   <?php //echo form_dropdown('user_ip_address_id', $ip_addresses, (isset($_POST['user_ip_address_id']) ? $_POST['user_ip_address_id'] : ''), 'class="form-control select2" id="ip_address"');?>

                                       <ul id="mytags_ip_address">
                                        <!-- Existing list items will be pre-added to the tags -->
                                        <?php if(isset($_POST['user_ip_address_id'])){?>
                                            <li id="ip_address"><?php echo $_POST['user_ip_address_id']; ?></li>
                                        <?php }?>

                                    </ul>
    							</div><!--IP addresses Filter-->

                            </div><!--col-->
                        </div><!--row-->
                    </div><!--row-->
                    <div class="row no-margin margin-top-15">
                    	<div class="col-md-5  padding-5px no-padding-left">
                            <div class="gray_col date-picker input-daterange">
                                <h3 class="form-section font-22px"><?php echo lang('date_order_added');?></h3>
                                <div class="col-md-4 padding-5px">
                                    <label class="control-label"><?php echo lang('date_start');?></label>
                                    <!--<input type="text" class="form-control" name="date_from" id="date_from"/>-->
                                    <?php
                                      $date_from_att = array('class'=>'form-control', 'id'=>'date_from', 'name'=>'date_from', 'value'=>set_value('date_from'));
                                      echo form_input($date_from_att);
                                    ?>
                                </div><!--col-->
                                <div class="col-md-4 padding-5px">
                                    <label class="control-label"> <?php echo lang('date_end');?></label>
                                    <!--<input type="text" class="form-control" name="date_to" id="date_to" />-->
                                    <?php
                                      $date_to_att = array('class'=>'form-control', 'id'=>'date_to', 'name'=>'date_to', 'value'=>set_value('date_to'));
                                      echo form_input($date_to_att);
                                    ?>
                                </div><!--col-->
                            </div><!--gray_col-->
                        </div><!--col-->
                        <div class="col-md-4 padding-5px">
                        	<div class="gray_col">
                                <h3 class="form-section font-22px"><?php echo lang('date_modefied_status_changed');?></h3>
                                <div class="date-picker input-daterange">
                                    <div class="col-md-4 padding-5px">
                                        <label class="control-label"><?php echo lang('date_start');?></label>
                                        <!--<input type="text" class="form-control" id="status_date_from" name="status_date_from" />-->
                                        <?php
                                          $status_date_from_att = array('class'=>'form-control', 'id'=>'status_date_from', 'name'=>'status_date_from', 'value'=>set_value('status_date_from'));
                                          echo form_input($status_date_from_att);
                                        ?>
                                    </div><!--col-->
                                    <div class="col-md-4 padding-5px">
                                        <label class="control-label"><?php echo lang('date_end');?></label>
                                        <!--<input type="text" class="form-control" id="status_date_to" name="status_date_to" />-->
                                        <?php
                                          $status_date_to_att = array('class'=>'form-control', 'id'=>'status_date_to', 'name'=>'status_date_to', 'value'=>set_value('status_date_to'));
                                          echo form_input($status_date_to_att);
                                        ?>
                                    </div><!--col-->
                                </div>
                                <div class="col-md-4 padding-5px">
                                    <label class="control-label"><?php echo lang('order_status');?></label>
                                    <?php echo form_dropdown('status_id', $order_status, (isset($_POST['status_id']) ? $_POST['status_id'] : ''), 'class="form-control select2" id="order_status_id"');?>
                                </div><!--col-->
                            </div><!--gray_col-->
                        </div><!--col-->
                        <div class="col-md-3 padding-5px no-padding-right">
                        	<div class="gray_col">
                            	<h3 class="form-section font-22px"><?php echo lang('order_id');?></h3>
                                <div class="col-md-6 padding-5px">
                                	<label class="control-label"><?php echo lang('from');?></label>
                                    <!--<input type="text" class="form-control" name="order_id_from" id="order_id_from" value="<?php set_value('order_id_from');?>" />-->
                                    <?php
                                     $order_id_from_att = array('class'=>'form-control', 'name'=>'order_id_from', 'id'=>'order_id_from', 'value'=>set_value('order_id_from'));
                                     echo form_input($order_id_from_att);
                                    ?>
                                </div><!--col-->
                                <div class="col-md-6 padding-5px">
                                	<label class="control-label"><?php echo lang('to');?></label>
                                    <!--<input type="text" class="form-control" name="order_id_to" id="order_id_to" value="<?php set_value('order_id_to');?>" />-->
                                    <?php
                                     $order_id_to_att = array('class'=>'form-control', 'name'=>'order_id_to', 'id'=>'order_id_to', 'value'=>set_value('order_id_to'));
                                     echo form_input($order_id_to_att);
                                    ?>
                                </div><!--col-->
                            </div><!--gray_col-->
                        </div><!--col-->
                    </div><!--row-->
                    <div class="row row_gray no-margin">
                        <div class="row no-margin">
                            <div class="row no-margin margin-top-10 margin-bottom-10">
                            	<div class="col-md-12">
                                    <!--<label><input type="checkbox" name="export" value="2" />Export</label>-->
                                    <!--
                                    <button id="export" class="btn red"><?php echo lang('export');?></button>
                                    <div style="margin: 5px;"></div>
                                    <div class="clearfix" id="export_div" style="display: none;">
										<div class="btn-group" data-toggle="buttons">
											<label class="btn btn-default">
                                              <input type="radio" class="toggle" name="export" value="1" /> <?php echo lang('without_details');?>
                                            </label>
											<label class="btn btn-default">
                                              <input type="radio" class="toggle" name="export" value="2" /> <?php echo lang('with')." ".lang('order_details');?>
                                            </label>
											<label class="btn btn-default">
											  <input type="radio" class="toggle" name="export" value="3" /> <?php echo lang('with')." ".lang('product_details');?>
                                            </label>
                                            <label class="btn btn-default">
											  <input type="radio" class="toggle" name="export" value="4" /> <?php echo lang('with')." ".lang('customer_list');?>
                                            </label>
                                            <label class="btn btn-default">
											  <input type="radio" class="toggle" name="export" value="5" /> <?php echo lang('with')." ".lang('all_details');?>
                                            </label>
										</div>
									</div>
                                    -->
                                    <div style="margin: 10px;"></div>
                                	<button class="btn blue" id="filter"><?php echo lang('filter');?></button>
                                    <!--<button class="btn green">Charts</button>
                                    <button name="export" value="1" class="btn red">Export</button>
                                    <button class="btn yellow">Settings</button>
                                    <button class="btn pink">Documentation</button>-->
                                </div><!--col-->
                            </div><!--row-->
                        </div>
                    </div>
				</div>
			</form>
			<!-- END FORM-->

		</div>
	</div><!--portlet-->
	<div class="portlet box blue margin-top-15">
		<div class="portlet-title">
			<div class="caption">
			<!--	<i class="fa fa-gift"></i>Form Sample-->
			</div><!--caption-->
			<div class="tools">
				<a href="javascript:;" class="collapse" data-original-title="" title="">
				</a>
				<a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
				</a>
				<a href="javascript:;" class="reload" data-original-title="" title="">
				</a>
				<a href="javascript:;" class="remove" data-original-title="" title="">
				</a>
			</div><!--tools-->
		</div><!--portlet-title-->
		<div class="portlet-body">
          	<div class="table-scrollable">

                <table class="table  table-bordered table-hover" id="big-table">
                    <thead>
                        <tr>
                            <?php foreach($columns as $column_name){?>
                                <th scope="col">
                                     <?php echo $column_name;?>
                                </th>
                            <?php }?>
                        </tr>
                    </thead>
                    <tbody id="tbody">

                        <?php
                         if(isset($data) && count($data)!= 0)
                         {
                            foreach($data as $row){?>
                                <tr class="button_filter">
                                    <td>
                                         <?php echo $row['details_icon'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['year'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['month'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['orders_count'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['users_count'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['products_count'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['reward_points'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['total'];?>
                                    </td>

                                    <?php if(!$represetitive){?>
                                        <td>
                                            <?php echo $row['orders_sub_total'];?>
                                        </td>
                                        <td>
                                             <?php echo $row['total_discount'];?>
                                        </td>
                                        <td>
                                             <?php echo $row['coupons_discount'];?>
                                        </td>
                                        <td>
                                             <?php echo $row['products_cost'];?>
                                        </td>
                                        <td>
                                             <?php echo $row['total_profit'];?>
                                        </td>
                                        <td>
                                             <?php echo $row['profit_percent'];?> %
                                        </td>
                                    <?php }?>
                                </tr>
                                <tr><td colspan="13" style="display: none;"> </td></tr>
                                <?php /*<tr  class="">
                                  <td colspan="10">
                                    <!--Orders Details-->


                                    <!--Products List-->


                                    <!--Products List-->


                                  </td>
                                </tr>*/?>
                        <?php }
                        }
                        else{?>
                            <tr><td colspan="13"><span style="text-align: center; display: block;"><?php echo lang('empty_table');?></span></td></tr>
                        <?php }?>





                    </tbody>
                </table>
			</div><!--table-scrollable-->
        </div><!--form-->
        <div class="row no-margin bg_fff">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
            <?php if($pagination){?>
    			<ul class="pagination pagination-sm">
                    <?php echo $pagination;?>
    			</ul>
            <?php }?>
		</div><!--col-->
        </div><!--row-->

    </div><!--portlet-->

    <?php if(!$represetitive){?>
        <div>
            <span class="hint"><?php echo lang('total_profit') . ' = ' . lang('total') . ' - ( ' . lang('products_cost') .' + '. lang('recharge_card') .' + '. lang('total_discount') .' + '. lang('coupon_discount') .')';?></span>
            <span class="hint"><?php echo lang('profit') .'[%]' . ' = ' . lang('total_profit') .' / (' . lang('total') . ' - ( ' . lang('recharge_card') .' + '. lang('total_discount') .' + '. lang('coupon_discount') .' ) )';?>%</span>

        </div>
    <?php }?>

</div><!--tab-->
<style>
.hint{
    display: block;
}
</style>
<script>


    ///////////////////////////////////////////////////////

    $('body').on("click", '.details_icon', function(e){
        e.preventDefault();

        $this       = $(this);
        $currentIMG = $this.children('img');
        $targetTD   = $this.closest('tr').next().children('td');

        var img_plus  = "<?php echo base_url().'assets/template/admin/icons/plus.png'?>";
        var img_minus = "<?php echo base_url().'assets/template/admin/icons/minus.png'?>";
        var img       = $currentIMG.attr('src');

        $currentIMG.attr('src', img == img_plus ? img_minus : img_plus);

        if($.trim($targetTD.html()).length == 0)
        {
            year  = $this.data('year');
            month = $this.data('month');

            var selected_payment = $('#payment_filter').find('option:selected');
            var payment_type     = selected_payment.data('type');

            //alert($("#coupon_id]").val());
            /*if($("input[name=coupon_id]").val() != '')
            {
                var coupon_result   = $("input[name=coupon_id]").val().split('/');
                var coupon_id       = coupon_result[1];
            }
            else
            {
                var coupon_id       = 0;
            }
            */
            //var users           = $("input[name=user_id]").val().split('-');
            //var user_id         = users[2];

            //var emails          = $("input[name=user_email_id]").val();

            //var phones          = $("input[name=user_phone_id]").val().split('/');
            //var phone_id        = phones[1];

            //var ip_address_data = $("input[name=user_ip_address_id]").val().split('/');
            //var ip_address      = ip_address_data[1];

            var postData = {
                             year               : year                          ,
                             month              : month                         ,
                             country_id         : $('#country_id').val()        ,
                             payment_id         : $('#payment_filter').val()    ,
                             payment_type       : payment_type                  ,
                             customer_group_id  : $('#customer_group_id').val() ,
                             cat_id             : $('#cat_id').val()            ,
                             coupon_id          : '<?php echo isset($_POST['coupon_id']) ? $_POST['coupon_id'] : "";?>',
                             order_id_from      : $('#order_id_from').val()     ,
                             order_id_to        : $('#order_id_to').val()       ,
                             date_from          : $('#date_from').val()         ,
                             date_to            : $('#date_to').val()           ,
                             status_date_from   : $('#status_date_from').val()  ,
                             status_date_to     : $('#status_date_to').val()    ,
                             order_status_id    : $('#order_status_id').val()   ,
                             store_id           : $('#store_id').val()   ,
                             user_id            : '<?php echo isset($_POST['user_id']) ? $_POST['user_id'] : "";?>',
                             user_email_id      : '<?php echo isset($_POST['user_email_id']) ? $_POST['user_email_id'] : "";?>',
                             user_phone_id      : '<?php echo isset($_POST['user_phone_id']) ? $_POST['user_phone_id'] : "";?>',
                             user_ip_address_id : '<?php echo isset($_POST['user_ip_address_id']) ? $_POST['user_ip_address_id'] : "";?>',
                           };

            $.post('<?php echo base_url().'reports/dynamic_reports/get_month_orders';?>', postData, function(result){
                $targetTD.html(result);
                $targetTD.toggle();
            });
        }
        else
        {
            $targetTD.toggle();
        }

    });



    $('body').on('click', '.orders_details', function(e){
        e.preventDefault();
        get_users_details($(this).attr('href'));
   });

   function get_users_details(url)
   {
        year  = $this.data('year');
        month = $this.data('month');

        var selected_payment = $('#payment_filter').find('option:selected');
        var payment_type     = selected_payment.data('type');

        var postData = {
                             year               : year                          ,
                             month              : month                         ,
                             country_id         : $('#country_id').val()        ,
                             payment_id         : $('#payment_filter').val()    ,
                             payment_type       : payment_type                  ,
                             customer_group_id  : $('#customer_group_id').val() ,
                             cat_id             : $('#cat_id').val()            ,
                             coupon_id          : '<?php echo isset($_POST['coupon_id']) ? $_POST['coupon_id'] : "";?>',
                             order_id_from      : $('#order_id_from').val()     ,
                             order_id_to        : $('#order_id_to').val()       ,
                             date_from          : $('#date_from').val()         ,
                             date_to            : $('#date_to').val()           ,
                             status_date_from   : $('#status_date_from').val()  ,
                             status_date_to     : $('#status_date_to').val()    ,
                             order_status_id    : $('#order_status_id').val()   ,
                             store_id           : $('#store_id').val()          ,
                             user_id            : '<?php echo isset($_POST['user_id']) ? $_POST['user_id'] : "";?>',
                             user_email_id      : '<?php echo isset($_POST['user_email_id']) ? $_POST['user_email_id'] : "";?>',
                             user_phone_id      : '<?php echo isset($_POST['user_phone_id']) ? $_POST['user_phone_id'] : "";?>',
                             user_ip_address_id : '<?php echo isset($_POST['user_ip_address_id']) ? $_POST['user_ip_address_id'] : "";?>',
                           };

       $.post(url, postData, function(result){
           $('.dr_orders_details_'+year+'_'+month).html(result);
       });
   }




</script>
