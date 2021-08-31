<div class="row">
    <div class="col-md-6 col-sm-12">
		<div class="portlet-body">
            <div class="row static-info">
				<div class="col-md-5 name">
					 <?php echo lang('customer_group_name');?>  :
				</div>
				<div class="col-md-7 value">
					 <?php echo $row_data->title;?>
				</div>
			</div>
            <div class="row static-info">
				<div class="col-md-5 name">
					 <?php echo lang('country');?>:
				</div>
				<div class="col-md-7 value">
					 <?php echo $row_data->name;?>
				</div>
			</div>
			<div class="row static-info">
				<div class="col-md-5 name">
					 <?php echo lang('discount_percentage');?> :
				</div>
				<div class="col-md-7 value">
					 <?php echo $row_data->discount_percentage;?>
				</div>
			</div>
			<div class="row static-info">
				<div class="col-md-5 name">
					 <?php echo lang('product_limit_per_order');?> :
				</div>
				<div class="col-md-7 value">
					<?php echo $row_data->product_limit_per_order;?>
				</div>
			</div>
			
		</div>
    </div>
</div>