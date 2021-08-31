<?php
    foreach($contact_us_data as $column_name => $column_data) {?>
            <div class="static-info border-cell">
    			<div class="col-md-5 name">
    				 <?php echo $column_name;?>  :
    			</div>
    			<div class="col-md-7 value">
    				 <?php echo $column_data;?>
    			</div>
    		</div>
    <?php }?>
    <div class="static-info border-cell" >
    	<!-- BEGIN SAMPLE TABLE PORTLET-->
    	<div class="portlet box blue">
    	    <div class="portlet-title"><div class="caption"><?php echo lang('replies');?></div></div>
    		<div class="portlet-body">
    			<div class="table-scrollable">
                  <table class="table table-bordered table-hover">
    				<thead>
    				<tr>
    					<td> <?php echo lang('sender');?></td>
                        <td> <?php echo lang('date');?></td>
                        <td> <?php echo lang('reply');?></td>
               		</tr>
    				</thead>
    				<tbody>
    			        <?php foreach($admin_contact_us_reply as $admin_replay){ ?>
                            <tr>
                                <td> <?php echo $admin_replay['admin_name'];?></td>
                                <td> <?php echo $admin_replay['time'];?></td>
                                <td> <?php echo $admin_replay['contact_us_reply'];?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
        	  </div>
    	   </div>
    	</div><!-- END SAMPLE TABLE PORTLET-->
    </div>
