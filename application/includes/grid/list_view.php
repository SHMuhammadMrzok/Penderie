<div class="row">
    <div class="col-md-6 col-sm-12">
		<div class="portlet-body">
            <?php foreach($list_data as $column_name => $column_data){?>
                <div class="row static-info">
    				<div class="col-md-5 name">
    					 <?php echo $column_name;?>  :
    				</div>
    				<div class="col-md-7 value">
    					 <?php echo $column_data;?>
    				</div>
    			</div>
            <?php }?>
		</div>
    </div>
</div>