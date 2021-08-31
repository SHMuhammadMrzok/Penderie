<div class="row">
    <div class="col-md-6 col-sm-12">
		<div class="portlet-body">
            <div class="row static-info">
				<div class="col-md-5 name">
					 <?php echo lang('country');?>  :
				</div>
				<div class="col-md-7 value">
					 <?php echo $row_data->name;?>
				</div>
			</div>
            <div class="row static-info">
				<div class="col-md-5 name">
					 <?php echo lang('flag');?>:
				</div>
				<div class="col-md-7 value">
					 <a href="<?php echo base_url();?>assets/uploads/<?php echo $row_data->flag;?>" class="image-thumbnail" data-rel="fancybox-button">
    				    <img class="img-responsive" src="<?php echo base_url();?>assets/uploads/<?php echo $row_data->flag;?>" alt="">
    				</a>
				</div>
			</div>
			<div class="row static-info">
				<div class="col-md-5 name">
					 <?php echo lang('symbol');?> :
				</div>
				<div class="col-md-7 value">
					 <?php echo $row_data->currency_symbol;?>
				</div>
			</div>
			<div class="row static-info">
				<div class="col-md-5 name">
					 <?php echo lang('currency');?> :
				</div>
				<div class="col-md-7 value">
					<?php echo $row_data->currency;?>
				</div>
			</div>
			
		</div>
    </div>
</div>