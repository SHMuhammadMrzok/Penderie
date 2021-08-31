<div class="portlet-body flip-scroll">
    <table class="table table-bordered table-striped table-condensed flip-content">
        <thead class="flip-content">
            <tr>
            	<th>
            		 <?php echo lang('product_name');?>
            	</th>
            	<th>
            		 <?php echo lang('cat_name');?>
            	</th>
            	<th class="numeric">
            		 <?php echo lang('viewed');?>
            	</th>
                <th class="numeric">
            		 <?php echo lang('percent');?>
            	</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($products as $product){?>
                <tr>
                    <td>
                		 <?php echo $product->title;?>
                	</td>
                	<td>
                		 <?php echo $product->name;?>
                	</td>
                	<td class="numeric">
                		 <?php echo $product->view;?>
                	</td>
                    <td class="numeric">
                		 <?php echo $product->percent;?> %
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
