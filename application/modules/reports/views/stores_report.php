<div class="portlet-body flip-scroll">
    <table class="table table-bordered table-striped table-condensed flip-content">
        <thead class="flip-content">
            <tr>
            	<th>
            		 <?php echo lang('name_of_store');?>
            	</th>
            	<th>
            		 <?php echo lang('active');?>
            	</th>
            	<th >
            		 <?php echo lang('commission_type');?>
            	</th>
              <th >
            		 <?php echo lang('commission');?>
            	</th>
              <th >
            		 <?php echo lang('orders_count');?>
            	</th>
              <th >
            		 <?php echo lang('final_total');?>
            	</th>
              <th >
            		 <?php echo lang('site_commission');?>
            	</th>

            </tr>
        </thead>
        <tbody>
            <?php foreach($report_data as $row){?>
                <tr>
                    <td>
                		 <a class="noprint" href="<?php echo base_url();?>stores/admin_stores/edit/<?php echo $row->id;?>" target="_blank"><?php echo $row->name;?></a>
             	          <p class="print" style="display: none;"><?php echo $row->name;?></p>
                     </td>
                  	<td>
                      <?php
                      if($row->active == 0)
                      {
                          $active = '<span class="badge badge-danger">'.lang('not_active').'</span>';
                      }
                      elseif($row->active == 1)
                      {
                          $active = '<span class="badge badge-success">'.lang('active').'</span>';
                      }
                      echo $active;?>

                  	</td>
                    <td>
                  		 <?php echo $row->commission_type;?>
                  	</td>

                    <td>
                  		 <?php echo $row->commission;?>
                  	</td>
                    <td>
                       <?php echo $row->orders_count;?>
                    </td>

                    <td>
                       <?php echo $row->orders_total.' '.$currency;?>
                    </td>
                    <td>
                  		 <?php echo $row->site_commission.' '.$currency;?>
                  	</td>



                </tr>
            <?php }?>

        </tbody>
    </table>
    <ul class="pagination noprint"><?php if($pagination) echo $pagination;?></ul>
    <ul class="pagination noprint">
        <li><a onclick="myFunction()" class="noprint"><i class="fa fa-print"></i><?php echo lang('print_page');?></a></li>
    </ul>

</div>
