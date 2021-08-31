<div class="portlet-body">
    <div class="table-scrollable">
        <table class="table table-hover">
        <thead>
            <tr>
                <td ><?php echo lang('order_id');?></td>
                <td ><?php echo lang('country');?></td>
                <td ><?php echo lang('purchased_products');?></td>
                <td ><?php echo lang('payment_method');?></td>
                <td ><?php echo lang('status');?></td>
                <td ><?php echo lang('total');?></td>
                <td ><?php echo lang('date');?></td>
            </tr>
        </thead>
        <tbody>
        <?php 
        if(count($user_orders_data) != 0)
        {
            foreach($user_orders_data as $order_log)
            {
        ?>
        <tr>
            <td><a href="<?php echo base_url()."orders/admin_order/view_order/".$order_log->id;?>"><?php echo $order_log->id;?></a></td>
            <td><?php echo $order_log->country;?></td>
            <td><?php 
                  $products_names     = '';
                  $products_data      = $this->orders_model->get_order_products($order_log->id, $lang_id);
                  $order_charge_cards = $this->orders_model->get_recharge_card($order_log->id);
                
                  foreach($products_data as $item)
                  {
                    $products_names .= $item->qty." X ".$item->title." <br> ";
                  }
                  foreach($order_charge_cards as $card)
                  {
                    $products_names .= lang('recharge_card')." X ".$card->price;
                  }
                  echo $products_names;
                ?>
            </td>
            <td><?php echo $order_log->payment_method; 
                ?>
            </td>
            <td><?php echo $order_log->status;?></td> 
            <td><?php echo $order_log->final_total." ".$order_log->currency_symbol;?></td>
            <td><?php echo date('Y/m/d H:i',$order_log->unix_time);?></td>
        </tr>
        <?php
            }
        }
        else
        {
        ?>
          <tr><td colspan="7" style="text-align: center;"><?php echo lang('no_data');?></td></tr>
        <?php
        }
        ?>
        </tbody>
        </table>
    </div>
    <?php if(count($user_orders_data) != 0){?>
        <ul class="pagination"><?php echo $page_links; ?></ul>
    <?php }?>
</div>