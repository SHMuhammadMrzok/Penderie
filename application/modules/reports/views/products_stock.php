<div class="portlet-body flip-scroll">
    <table class="table table-bordered table-striped table-condensed flip-content">
        <thead class="flip-content">
            <tr>
              <th>
            		 <?php echo lang('product_name');?>
            	</th>
              <th class="noprint">
            		 <?php echo lang('sales_history');?>
            	</th>
              <?php /*
              <th>
               <?php echo lang('sold_times');?>
              </th>
              <th>
               <?php echo lang('price');?>
              </th>
              <th>
               <?php echo lang('total_sales_price');?>
              </th>
              */?>
                <th>
            		 <?php echo lang('country');?>
            	</th>
            	<th>
            		 <?php echo lang('current_quantity');?>
            	</th>
                <th>
            		 <?php echo lang('average_cost');?>
            	</th>
            	<th class="">
            		 <?php echo lang('total_cost');?>
            	</th>

            </tr>
        </thead>
        <tbody>
            <?php foreach($products as $product){?>
                <tr>

                  <td rowspan="<?php echo ( count($product->country_data) !=0) ? count($product->country_data) : 1;?>">
                		 <?php echo $product->title;?>
                	</td>

                  <td class="noprint" rowspan="<?php echo (count($product->country_data) !=0) ? count($product->country_data) : 1;?>">
                     <a href="<?php echo base_url();?>products/admin_products/read/<?php echo $product->product_id.'/'.$this->data['lang_id'].'#Sales_History';?>" target="_blank"><?php echo lang('product_sales');?></a>
                  </td>

                  <?php /*<td rowspan="<?php echo ( count($product->country_data) !=0) ? count($product->country_data) : 1;?>">
                		 <?php echo $product->total_qty;?>
                	</td>

                  <td rowspan="<?php echo ( count($product->country_data) !=0) ? count($product->country_data) : 1;?>">
                		 <?php echo $product->total_price.' '.$currency;?>
                	</td>

                  <td rowspan="<?php echo ( count($product->country_data) !=0) ? count($product->country_data) : 1;?>">
                		 <?php echo $product->final_total_price.' '.$currency;?>
                	</td>*/?>

                  <?php if(count($product->country_data) != 0){
                     foreach($product->country_data as $key=>$country){
                    ?>
                      <?php echo $key == 0 ? '' : '<tr>';?>
                          <td>
                      		 <?php echo $country->name;?>
                      	</td>
                      	<td >
                      		 <?php echo $country->current_qty;?>
                      	</td>
                      	<td >
                      		 <?php echo $country->average_cost;?>
                      	</td>
                      	<td >
                      		 <?php echo $country->total_avg_cost;?>
                      	</td>

                       </tr>

                       <?php echo $key == 0? '</tr>' : '';

                   }


                 }else{?>
                   <td></td>
                   <td ></td>
                   <td ></td>
                   <td ></td>
                <?php }?>



            <?php }?>
            <?php /*
            <tr>
                <td rowspan="2" colspan="2"></td>
                <td><?php echo lang('total_qty');?></td>
                <td><?php echo lang('total');?></td>
                <td><?php echo lang('total');?></td>
            </tr>
            <tr>
                <td><?php echo $total_vals->product_quantity;?></td>
                <td><?php echo $total_vals->average_cost;?></td>
                <td><?php echo $total_vals->total;?></td>

            </tr>
        */?>
        </tbody>
    </table>
    <ul class="pagination noprint"><?php if($pagination) echo $pagination;?></ul>
    <ul class="pagination noprint">
        <li><a onclick="myFunction()" class="noprint"><i class="fa fa-print"></i><?php echo lang('print_page');?></a></li>
    </ul>
</div>
